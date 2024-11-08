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
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);       
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);   
        $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
        $data_cadastro = filter_input(INPUT_POST, 'data_cadastro', FILTER_SANITIZE_STRING);
        $status = 1;
        // Captura o usuário logado
            $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

            // Captura a data e hora do cadastro
            $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

            // Variável para armazenar mensagens de erro
            $_SESSION['msg'] = '';
        
                    
                // Função para validar se é um número de CPF válido
                function validarCPF($cpf) {
                    // Remover caracteres não numéricos
                    $cpf = preg_replace('/[^0-9]/', '', $cpf);
                    if (strlen($cpf) != 11) {
                        return false;
                    }
                    if (preg_match('/(\d)\1{10}/', $cpf)) {
                        return false;
                    }
                    for ($i = 9; $i < 11; $i++) {
                        $sum = 0;
                        for ($j = 0; $j < $i; $j++) {
                            $sum += $cpf[$j] * (($i + 1) - $j);
                        }
                        $remainder = $sum % 11;
                        $digit = ($remainder < 2) ? 0 : 11 - $remainder;
                        if ($digit != $cpf[$i]) {
                            return false;
                        }
                    }
                    return true;
                }

                    // Verifica se as senhas coincidem
            if ($password !== $password2) {
                $_SESSION['msg'] .= "As senhas não coincidem.<br>";
            }

            // Verifica se a senha possui pelo menos um número
            if (!preg_match('/[0-9]/', $password)) {
                $_SESSION['msg'] .= "A senha deve conter pelo menos um número.<br>";
            }

            // Verifica se a senha possui pelo menos uma letra maiúscula
            if (!preg_match('/[A-Z]/', $password)) {
                $_SESSION['msg'] .= "A senha deve conter pelo menos uma letra maiúscula.<br>";
            }

            // Verifica se a senha possui pelo menos uma letra minúscula
            if (!preg_match('/[a-z]/', $password)) {
                $_SESSION['msg'] .= "A senha deve conter pelo menos uma letra minúscula.<br>";
            }

            // Verifica se a senha possui pelo menos um caractere especial
            if (!preg_match('/[!@#$%^&*()_\-+=]/', $password)) {
                $_SESSION['msg'] .= "A senha deve conter pelo menos um caractere especial.<br>";
            }

            // Verifica se a senha possui no mínimo 8 caracteres
            if (strlen($password) < 8) {
                $_SESSION['msg'] .= "A senha deve ter no mínimo 8 caracteres.<br>";
            }

                            // Normalizar o CPF removendo caracteres não numéricos
                    $cpf = preg_replace('/[^0-9]/', '', $cpf);

                    // Verificar se é um número de CPF válido
                    if (!empty($cpf) && !validarCPF($cpf)) {
                        $_SESSION['msg'] .= "CPF inválido!";
                    }

                    // Verificar se o CPF já está cadastrado no banco de dados
                    if (empty($_SESSION['msg'])) {
                        $query_verifica_cpf = "SELECT COUNT(*) AS total FROM tbprofessores WHERE cpf = ?";
                        $stmt_verifica_cpf = $conn->prepare($query_verifica_cpf);
                        $stmt_verifica_cpf->bind_param("s", $cpf);
                        $stmt_verifica_cpf->execute();
                        $result_verifica_cpf = $stmt_verifica_cpf->get_result();
                        $row_verifica_cpf = $result_verifica_cpf->fetch_assoc();

                        if ($row_verifica_cpf['total'] > 0) {
                            $_SESSION['msg'] .= "CPF já cadastrado!";
                        }
                    }

                        // Verificar se os e-mails são iguais
            if (!empty($email) && !empty($confirma_email) && $email !== $confirma_email) {
                $_SESSION['msg'] .= "Os e-mails não são iguais!";
            }

            // Verificar se o email é válido
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['msg'] .= "Email inválido!";
            }

            // Verificar se o email já está cadastrado no banco de dados
            if (empty($errors['email']) && empty($errors['confirma_email'])) {
                $query_verifica_email = "SELECT COUNT(*) AS total FROM tbprofessores WHERE email = ?";
                $stmt_verifica_email = $conn->prepare($query_verifica_email);
                $stmt_verifica_email->bind_param("s", $email);
                $stmt_verifica_email->execute();
                $result_verifica_email = $stmt_verifica_email->get_result();
                $row_verifica_email = $result_verifica_email->fetch_assoc();

                if ($row_verifica_email['total'] > 0) {
                    $_SESSION['msg'] .= "Email já cadastrado!";
                }
            }
            
             // Hash da senha informada pelo usuário
             $password_hash = password_hash($password, PASSWORD_DEFAULT);
 

        try {
                        // Usando prepared statements para evitar SQL Injection
                $stmt = $conn->prepare("INSERT INTO tbprofessores (nome, cpf, telefone, celular, codigo_escola, nome_escola, email, acesso, password, cadastrado_por, data_cadastro, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                if (!$stmt) {
                    throw new Exception("Erro ao preparar a consulta: " . $conn->error);
                }

                // Verificar se o número de parâmetros no bind_param corresponde ao número de colunas
                $stmt->bind_param("sssssssssssi", 
                    $nome, 
                    $cpf,
                    $telefone,                     
                    $celular, 
                    $codigo_escola,
                    $nome_escola, 
                    $email, 
                    $acesso, 
                    $password_hash, // Aqui é `password_hash`, que contém o hash da senha
                    $cadastrado_por, 
                    $data_cadastro, 
                    $status // Verificar se a variável `status` foi definida corretamente
                );
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
                    registraHistorico($conn, "cadastrar", $_SESSION['nome'], $nome, "Professores", date('Y-m-d H:i:s'));
                }
                $conn->commit();
                $_SESSION['sucesso'] = "Professor cadastrada com sucesso!";
                
                // Redireciona para a página desejada
                header("Location: cadastrar_professor.php");
                exit();
            } else {
                // Tratar possíveis erros aqui
                throw new Exception("Erro ao cadastrar a professores: " . $stmt->error);
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
           // Conexão e consulta para dados das escolas
           $sql = "SELECT codigo_escola, unidadeEscola FROM dados_etec";
           $result = $conn->query($sql);
   
           $dadosEtec = [];
           if ($result && $result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                   $dadosEtec[] = $row;
               }
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
    <title>Cadastro do Aluno</title>

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
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Professor</a>
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
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" name="nome" placeholder="Insira o nome do aluno" required class="form-control">
                        </div>
                        <div class="w-100">
                            <label for="cpf" class="form-label">CPF:</label>
                            <input type="text" name="cpf" placeholder="Insira o CPF" required class="form-control">
                        </div>
                       
                 </div>

                 <div class="breakable-row d-flex justify-between gap-4">
                    <div class="w-100">
                        <label for="telefone" class="form-label">Telefone:</label>
                        <input type="text" name="telefone" placeholder="Insira o telefone" required class="form-control">
                    </div>
                    <div class="w-100">
                        <label for="celular" class="form-label">Celular:</label>
                        <input type="tel" name="celular" placeholder="Insira o celular" required class="form-control">
                    </div>
                 </div>
                
                 <div class="breakable-row d-flex justify-between gap-4">
                    
                            <div class="w-50">
                                <label for="codigo_escola" class="form-label">Codigo Etec:</label>
                                                            <!-- Campo para o código da ETEC -->
                                    <input type="text" id="codigo_escola" name="codigo_escola" list="codigos"  placeholder="Código Etec" required class="form-control">                       

                                    <!-- Datalist com opções -->
                                    <datalist id="codigos">
                                        <?php foreach ($dadosEtec as $escola): ?>
                                            <?php $codigo = $escola['codigo_escola']; ?>
                                            <?php $nome = $escola['unidadeEscola']; ?>
                                            <option value="<?php echo "$codigo"; ?>"><?php echo "$codigo - $nome"; ?></option>
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
                            <label for="acesso" class="form-label">Confirme o acesso:</label>
                            <select name="acesso" id="acesso" required class="form-select">
                                <option value="">Tipo de Acesso</option>
                                <option value="aluno">Professor</option>
                            </select>
                        </div>
                        <div class="w-100">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" placeholder="Insira o email" required class="form-control">
                </div>    

                </div>
                              
                <div>
                    <label for="password" class="form-label">Senha:</label>
                    <input type="password" name="password" placeholder="Insira a senha" required class="form-control">
                </div>
                <div>
                    <label for="password2" class="form-label">Confirme a Senha:</label>
                    <input type="password" name="password2" placeholder="Confirme a senha" required class="form-control">
                </div>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
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