<?php
        session_start();
        include '../../../../conexao/conexao.php'; // Inclui o arquivo de conexão
        date_default_timezone_set('America/Sao_Paulo');

        // Variável para armazenar mensagens de erro
        $_SESSION['msg'] = '';

        // Conexão e consulta para dados das escolas
        $sql = "SELECT codigo_escola, unidadeEscola FROM dados_etec";
        $result = $conn->query($sql);

        $dadosEtec = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $dadosEtec[] = $row;
            }
        } 

     // Captura o usuário logado
     $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão
     

     
     // Captura a data e hora do cadastro
     $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

     // Variável para armazenar mensagens de erro
     $_SESSION['msg'] = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_curso = filter_input(INPUT_POST, 'nome_curso', FILTER_SANITIZE_STRING);
        $tempo_curso = filter_input(INPUT_POST, 'tempo_curso', FILTER_SANITIZE_STRING);       
        $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
         $data_cadastro = filter_input(INPUT_POST, 'data_cadastro', FILTER_SANITIZE_STRING);
         $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);

            // Verifica se o campo nome_escola não está vazio
            if (empty($nome_escola)) {
                $_SESSION['msg'] = "O nome da escola é obrigatório!";
                header("Location: cadastrar_curso.php");
                exit();
            }

 
        // Captura o usuário logado
            $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

            // Captura a data e hora do cadastro
            $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

            // Variável para armazenar mensagens de erro
            $_SESSION['msg'] = '';
        

        try {
            // Usando prepared statements para evitar SQL Injection
            $stmt = $conn->prepare("INSERT INTO tbcursos (nome_curso, tempo_curso, cadastrado_por, data_cadastro, nome_escola) VALUES (?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Erro ao preparar a consulta: " . $conn->error);
            }
        
            $stmt->bind_param("sssss", $nome_curso, $tempo_curso, $cadastrado_por, $data_cadastro, $nome_escola);
        
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
                    registraHistorico($conn, "cadastrar", $_SESSION['nome'], $nome_curso, "Curso", date('Y-m-d H:i:s'));
                }
                $conn->commit();
                $_SESSION['sucesso'] = "Curso:" .  $_SESSIOn['nome_curso'] . "  cadastrado com sucesso";
                
                // Redireciona para a página desejada
                header("Location: cadastrar_curso.php");
                exit();
            } else {
                // Tratar possíveis erros aqui
                throw new Exception("Erro ao cadastrar a Curso: " . $stmt->error);
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
            header("Location:  ../../../../../login/login.php");
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
    <title>Cadastrar Curso</title>

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
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Curso</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
    <p class="text-primary">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?></p>
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div class="breakable-row d-flex justify-between gap-4">
                    <div class="w-100">
                    <label for="nome_curso" class="form-label">Nome do Curso :</label>
                    <input type="text" name="nome_curso" placeholder="Insira o nome do curso" required class="form-control">
           
                    </div>
                   <div class="w-100">
                            <label for="tempo_curso" class="form-label">Tempo do Curso:</label>
                            <input type="text" name="tempo_curso" placeholder="Insira o tempo do curso" required class="form-control">
                 </div>
            </div>
            
            <div class="breakable-row d-flex justify-between gap-4">
            <div class="w-20">
                        <label for="codigo_escola" class="form-label">Codigo Etec:</label>
                        <!-- Campo para o código da ETEC -->
                        <input type="text" id="codigo_escola" name="codigo_escola" list="codigos" placeholder="Código Etec" required class="form-control" onchange="updateNomeEscola()">

                        <!-- Datalist com opções -->
                        <datalist id="codigos">
                            <?php foreach ($dadosEtec as $escola): ?>
                                <?php $codigo = $escola['codigo_escola']; ?>
                                <?php $nome = $escola['unidadeEscola']; ?>
                                <option value="<?php echo "$codigo"; ?>" data-nome="<?php echo "$nome"; ?>"><?php echo "$codigo - $nome"; ?></option>
                            <?php endforeach; ?>
                        </datalist>  
                    </div>
                    <div class="w-100">
                       <!-- Campo para mostrar o nome da escola selecionada -->
                       <label for="nome_escola" class="form-label">Nome Etec:</label>
                       <input type="text" id="nome_escola" name="nome_escola" placeholder="Nome da Etec"  class="form-control">
                    </div>
                       
            </div>
            <div class="breakable-row d-flex justify-between gap-4">
            <div class="w-100">
                <button type="submit" class="btn btn-primary w-100 ">Cadastrar</button>
                </div>

                <div class="w-100"> 
                <button type="reset" class="btn btn-outline-secondary w-100 ">Limpar</button>          
                </div>

                
            </div>
            
            


        </form>
    </div>

  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                const codigoEscolaInput = document.getElementById('codigo_escola');
                const nomeEscolaInput = document.getElementById('nome_escola');
                const codigosDatalist = document.getElementById('codigos');

                codigoEscolaInput.addEventListener('input', function() {
                    const codigoSelecionado = this.value.trim();
                    
                    // Encontra a opção correspondente ao código selecionado
                    const option = Array.from(codigosDatalist.options).find(opt => opt.value === codigoSelecionado);
                    
                    if (option) {
                        nomeEscolaInput.value = option.textContent.split(' - ')[1] || '';
                    } else {
                        nomeEscolaInput.value = '';
                    }
                });
            });

    </script>                                        
</body>
</html>