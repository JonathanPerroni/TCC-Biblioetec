<?php
   session_start();
   include_once("../../../conexao/conexao.php");
   date_default_timezone_set('America/Sao_Paulo');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $confirma_email = filter_input(INPUT_POST, 'confirma_email', FILTER_SANITIZE_EMAIL);        
        $password = $_POST['password'];  // Senha não precisa de sanitização
        $password2 = $_POST['password2'];  // Senha não precisa de sanitização
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
        $acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);

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
    $query_verifica_cpf = "SELECT COUNT(*) AS total FROM tbdev WHERE cpf = ?";
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
                $query_verifica_email = "SELECT COUNT(*) AS total FROM tbdev WHERE email = ?";
                $stmt_verifica_email = $conn->prepare($query_verifica_email);
                $stmt_verifica_email->bind_param("s", $email);
                $stmt_verifica_email->execute();
                $result_verifica_email = $stmt_verifica_email->get_result();
                $row_verifica_email = $result_verifica_email->fetch_assoc();

                if ($row_verifica_email['total'] > 0) {
                    $_SESSION['msg'] .= "Email já cadastrado!";
                }
            }
            
            
            // Verificar se o telefone é um número válido
            if (!empty($telefone) && !preg_match('/^(\(?\d{2}\)?\s?)?\d{5}-?\d{4}$/', $telefone)) {
                $_SESSION['msg'] .= "Telefone inválido! O formato deve ser (XX) 9XXXX-XXXX ou XX9XXXX-XXXX";
            }

            // Verifica se há mensagens de erro
            if (!empty($_SESSION['msg'])) {
                header("Location: cadastrar_dev.php");
                exit();
            }





                        
            // Hash da senha informada pelo usuário
            $password_hash = password_hash($password, PASSWORD_DEFAULT);



        // Usando a conexão do arquivo 'conexao_testes.php'
        $stmt = $conn->prepare("INSERT INTO tbdev (nome, cpf, email, password, telefone, celular, acesso) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nome, $cpf, $email, $password_hash, $telefone, $celular, $acesso);

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
            // Redireciona para a página de edição   
            header("Location: cadastrar_dev.php");
            exit(); 
        } else {
             // Tratar possíveis erros aqui
            echo "Erro ao atualizar o registro: " . $stmt->error;
        }
        
    $stmt->close();
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
    <title>Cadastro de DEV's</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../DevCss/defaults.css">
    
</head>
<body class="w-100 h-auto d-flex flex-column align-itens-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../../DevScreen/pagedevNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar DEV</a>
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
                <label for="nome" class="form-label">Nome completo:</label>
                <input type="text" name="nome" placeholder="Insira o nome completo" required class="form-control">
            </div>

            <div>
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" name="cpf" placeholder="Insira o CPF" required class="form-control">
            </div>

            <div>
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" placeholder="Insira o email" required class="form-control">
            </div>
            <div>
                <label for="confirma_email" class="form-label">Confirmar Email:</label>
                <input type="email" name="confirmar_email" placeholder="Insira o email" required class="form-control">
            </div>

            <div>
                <label for="password" class="form-label">Senha:</label>
                <input type="password" name="password" placeholder="Insira a senha" required class="form-control">
            </div>

            <div>
                <label for="password2" class="form-label">Confirme a senha:</label>
                <input type="password" name="password2" placeholder="Confirme a senha" required class="form-control">
            </div>

            <div class="breakable-row d-flex justify-between gap-4">
                <div class="w-100">
                    <label for="telefone" class="form-label">Telefone:</label>
                    <input type="tel" name="telefone" placeholder="Insira o número de telefone" class="form-control">
                </div>
                <div class="w-100">
                    <label for="celular" class="form-label">Celular:</label>
                    <input type="tel" name="celular" placeholder="Insira o número de celular" required class="form-control">
                </div>
            </div>
            <div>
                <label for="acesso" class="form-label">Tipo de Acesso:</label>
                <select name="acesso" id="acesso" required class="form-select">
                    <option value="">Selecione o tipo de acesso</option>
                    <option value="desenvolvedor">Desenvolvedor</option>
                </select>
            </div>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

   
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
