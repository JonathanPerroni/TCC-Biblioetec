<?php
    session_start();
    include '../../../../conexao/conexao.php'; // Inclui o arquivo de conexão
    date_default_timezone_set('America/Sao_Paulo');

     // Captura o usuário logado
     $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

     // Captura a data e hora do cadastro
     $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

     // Variável para armazenar mensagens de erro
     $_SESSION['msg'] = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $tipoEscola = filter_input(INPUT_POST, 'tipoEscola', FILTER_SANITIZE_STRING);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
        $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);      
        $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);       
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
        $cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
        $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
        $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
        $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
         $data_cadastro = filter_input(INPUT_POST, 'data_cadastro', FILTER_SANITIZE_STRING);
 
        // Captura o usuário logado
            $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

            // Captura a data e hora do cadastro
            $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

            // Variável para armazenar mensagens de erro
            $_SESSION['msg'] = '';
        
              // Verificar se o código da escola já está em uso
              $query_verifica_codigo_escola = "SELECT COUNT(*) AS total FROM tbescola WHERE codigo_escola = ?";
              $stmt_verifica_codigo_escola = $conn->prepare($query_verifica_codigo_escola);
              $stmt_verifica_codigo_escola->bind_param("s", $codigo_escola);
              $stmt_verifica_codigo_escola->execute();
              $result_verifica_codigo_escola = $stmt_verifica_codigo_escola->get_result();
              $row_verifica_codigo_escola = $result_verifica_codigo_escola->fetch_assoc();
              if ($row_verifica_codigo_escola['total'] > 0) {
                  $_SESSION['msg'] .= "Codigo escola já em uso por outro ETEC!";
              }      
    


        // Validação de CNPJ
        if (!preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', $cnpj)) {
            $_SESSION['msg'] = "<p>CNPJ inválido. Use o formato XX.XXX.XXX/XXXX-XX.</p>";
        }

        try {
            // Usando prepared statements para evitar SQL Injection
            $stmt = $conn->prepare("INSERT INTO tbescola (nome_escola, tipoEscola, codigo_escola, endereco, numero, bairro, cidade, cep, estado, cnpj, telefone, celular, cadastrado_por, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Erro ao preparar a consulta: " . $conn->error);
            }
        
            $stmt->bind_param("ssssssssssssss", $nome_escola, $tipoEscola, $codigo_escola, $endereco, $numero, $bairro, $cidade, $cep, $estado, $cnpj, $telefone, $celular, $cadastrado_por, $data_cadastro);
        
            // Função para registrar histórico
            function registraHistorico($conn, $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora) {
                $stmt = $conn->prepare("INSERT INTO historico_usuarios (historico_acao, historico_responsavel, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Erro ao preparar a consulta de histórico: " . $conn->error);
                }
                $stmt->bind_param("sssss", $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
                $stmt->execute();
                $stmt->close();
            }
        
            if ($stmt->execute()) {
                if (empty($_SESSION['msg'])) {
                    registraHistorico($conn, "cadastrar", $_SESSION['nome'], $nome_escola, "Escola", date('Y-m-d H:i:s'));
                }
                $conn->commit();
                $_SESSION['sucesso'] = "Escola cadastrada com sucesso!";
                
                // Redireciona para a página desejada
                header("Location: cadastrar_escola.php");
                exit();
            } else {
                // Tratar possíveis erros aqui
                throw new Exception("Erro ao cadastrar a escola: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        

        $conn->close();
    }

         // Validação de login, só entra se estiver logado
         if (empty($_SESSION['email'])) {
            // echo  $_SESSION['nome'];
            // echo  $_SESSION['acesso'];
            $_SESSION['msg'] = "Faça o Login!!";
            header("Location:  ../../../../loginDev.php");
            exit();
        }
    
        // Verifica se há mensagem na sessão
        if (isset($_SESSION['sucesso'])) {
            $sucesso = htmlspecialchars($_SESSION['sucesso'], ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$sucesso');</script>";
            // Limpa a mensagem da sessão
            unset($_SESSION['sucesso']);
        }



    ?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de ETEC</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../UserCss/defaults.css">
    <link rel="stylesheet" href="./css/cadastrar_etec.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-itens-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <!-- ira verificar qual  usuario esta logado para voltar para pagina especifica do tipo do acesso  -->
        <?php
                                        

                                        // Verifica o tipo de acesso do usuário
                                        $acesso = $_SESSION['acesso'] ?? ''; // Define o valor de acesso na sessão, caso não exista

                                        // Define o link de redirecionamento com base no tipo de acesso
                                        switch ($acesso) {
                                            case 'administrador':
                                                $pagina_inicial = "../../admin/pageAdmin.php";
                                                break;
                                            case 'bibliotecario':
                                                $pagina_inicial = "../../bibliotecario/pageBibliotecario.php";
                                                break;
                                             default:
                                                // Redireciona para uma página padrão, caso o acesso não seja identificado
                                                $pagina_inicial = "../../../login/login.php";
                                                break;
                                        }
             ?>

        
        <a href="<?php echo $pagina_inicial; ?>" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                <span class="fw-medium">Início</span>
            </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar ETEC</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
    <p class="text-primary">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?></p>
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="nome_escola" class="form-label">Nome da Etec:</label>
                <input type="text" name="nome_escola" placeholder="Insira o nome da Etec" required class="form-control">
            </div>
            <div>
                <label for="tipoEscola" class="form-label">Tipo de Etec:</label>
                <input type="text" name="tipoEscola" placeholder="Insira o tipo de Etec" required class="form-control">
            </div>
            <div>
                <label for="codigo_escola" class="form-label">Código da Etec:</label>
                <input type="text" name="codigo_escola" placeholder="Insira o código da Etec" required class="form-control">
            </div>
            <div>
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" name="endereco" placeholder="Insira o endereço" required class="form-control">
            </div>
            <div>
                <label for="numero" class="form-label">Nº:</label>
                <input type="text" name="numero" placeholder="Insira o numero" required class="form-control">
            </div>
            <div>
                <label for="bairro" class="form-label">Bairro:</label>
                <input type="text" name="bairro" placeholder="Insira o bairro" required class="form-control">
            </div>
            <div>
                <label for="cidade" class="form-label">Cidade:</label>
                <input type="text" name="cidade" placeholder="Insira a cidade" required class="form-control">
            </div>
            <div>
                <label for="cep" class="form-label">CEP:</label>
                <input type="text" name="cep" placeholder="Insira o cep" required class="form-control">
            </div>
            <div>
                <label for="estado" class="form-label">Estado:</label>
                <input type="text" name="estado" placeholder="Insira o estado" required class="form-control">
            </div>
            <div>
                <label for="cnpj" class="form-label">CNPJ:</label>
                <input type="text" name="cnpj" placeholder="Insira o CNPJ" required class="form-control">
            </div>
            <div class="breakable-row d-flex justify-between gap-4">
                <div class="w-100">
                    <label for="telefone" class="form-label">Telefone:</label>
                    <input type="tel" name="telefone" placeholder="Insira o telefone" class="form-control">
                </div>
                <div class="w-100">
                    <label for="celular" class="form-label">Celular:</label>
                    <input type="tel" name="celular" placeholder="Insira o celular" required class="form-control">
                </div>
            </div>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>