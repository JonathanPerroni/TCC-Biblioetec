<?php
session_start();



//importa as classe do phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Fuso horário do lugar informado 
date_default_timezone_set('America/Sao_Paulo');

// Inclui a conexão MySQLi
include_once("../conexao.php");

// Receber os dados do usuário 
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o if quando o usuário clicar no botão "Recuperar Senha" do formulário
if (!empty($dados['SendRecupSenha'])) {
    // Query para recuperar os dados do usuário no banco de dados
    $query_usuario = "SELECT codigo, nome, email
                      FROM tbdev
                      WHERE email = ? 
                      LIMIT 1"; 

    // Preparar query
    if ($stmt = $conn->prepare($query_usuario)) {
        // Substituir o valor na query pelo valor que vem do formulário
        $stmt->bind_param('s', $dados['email']);

        // Executar a query
        if ($stmt->execute()) {
            // Verifica se encontrou o registro no banco de dados
            $result_usuario = $stmt->get_result();
            if ($result_usuario->num_rows > 0) {
                // Ler os registros retornados do banco de dados
                $row_usuario = $result_usuario->fetch_assoc();
                
                // Gerar a chave para recuperar a senha
                $chave_recuperar_senha = password_hash($row_usuario['codigo'] . $row_usuario['email'], PASSWORD_DEFAULT);
                
                // Query para atualizar a chave de recuperação de senha
                $query_up_usuario = "UPDATE tbdev
                                     SET chave_recuperar_senha = ?
                                     WHERE codigo = ?
                                     LIMIT 1";

                // Preparar a query
                if ($stmt_up = $conn->prepare($query_up_usuario)) {
                    // Substituir os valores na query
                    $stmt_up->bind_param('si', $chave_recuperar_senha, $row_usuario['codigo']);

                    // Executar a query
                    if ($stmt_up->execute()) {
                        // Gerar o link de recuperação de senha
                        $link = "http://localhost/biblioetec/Desenvolvedor/Screen/esqueceuSenha.php?chave=" . urlencode($chave_recuperar_senha);

                        // Incluir o composer
                        require '../lib/vendor/autoload.php';

                        // Criar o objeto e instanciar a classe do PHPMailer
                        $mail = new PHPMailer(true);

                        try {                    
                            // Server settings
                            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Desativar o modo de depuração
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();
                            $mail->Host       = 'sandbox.smtp.mailtrap.io';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = '0a1cfc235b42dd';
                            $mail->Password   = 'c23abe058e2e4f';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 465;

                            // Email do remetente
                            $mail->setFrom('DevBiblioEtec@devs.com', 'Atendimento');
                            // Email do destinatário
                            $mail->addAddress($row_usuario['email'], $row_usuario['nome']);     

                            // Definir o formato do email para HTML
                            $mail->isHTML(true);                   
                            $mail->Subject = 'Recuperar Senha';

                            // Conteúdo do email em HTML
                            $mail->Body    = "Ola! " . $row_usuario['nome'] . ", Você solicitou alteração de senha.<br><br>
                            Para continuar o processo de recuperação de sua senha, clique no link abaixo <br><br>
                            ou cole o endereço no seu navegador: <br><br>
                            <a href='" .$link. "'>" .$link. "</a><br><br>Se você não solicitou essa alteração, nenhuma ação é necessária. <br>
                            Sua senha permanecerá a mesma até que você ative este código.<br><br>" ;
                            
                            // Conteúdo alternativo do email (texto puro)
                            $mail->AltBody = "Ola! " . $row_usuario['nome'] . " \n\nVocê solicitou a alteração de senha.\n\n
                            Para continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no navegador\n\n"
                            . $link. "\n\nSe você não solicitou essa alteração, nenhuma ação é necessária.
                             \n\nSua senha permanecerá a mesma até que você ative este código.\n\n";

                            // Enviar o email
                            $mail->send();

                            // Criar a variável global com a mensagem de sucesso
                            $_SESSION['msg'] = "<p style='color: green;'> Confira seu email, link enviado </p>";

                            // Redirecionar para a página principal (login)
                            header('Location: ../loginDev.php');

                        
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            $_SESSION['msg'] = "<p style='color: #f00;'> Erro: Email não enviado!</p>";
                        }
                    } else {
                        $_SESSION['msg'] = "<p style='color: yellow;'> Erro: Tente novamente!</p>";
                    }

                    // Fechar a declaração
                    $stmt_up->close();
                } else {
                    die('Erro ao preparar a query de atualização: ' . $conn->error);
                }
            } else {
                $_SESSION['msg'] = "<p style='color: #f00;'> Erro: Email não encontrado!</p>";
            }
        } else {
            die('Erro ao executar a query de seleção: ' . $stmt->error);
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        die('Erro ao preparar a query de seleção: ' . $conn->error);
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="/biblioetec/Desenvolvedor/style/defaults.css">
</head>

<Style>
 body{
        height: 90vh;
        justify-content: center;
        align-items: center;
        }

        main{
    max-width:400px;   
    width: 100%;
    height: auto;
    padding: 16px;
    border: none;
    border-radius: 16px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.25 );
    background-color: white;
    display: flex;
    flex-direction: column;

}
header{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;    
    text-align: center;
}

h1{
    font-size: 25px;
}

.separation-line{
    height: 2px;
        width: 80%;
        margin: 0px 16px;
        background-color: var(--off-white);
        color: var(--off-white);
}



#brand-title{
    color:  var(--primary-emphasis);
    font-weight: 600;   
    width: 200px;
}

#cad-title{
    color: var(--off-black);
    font-weight: 400;   
    width: 200px;
}

.formsContainer{
    margin-top: 10px;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    
    
}

form{
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;    
    justify-content:  center;
    align-items: center;  
    
}

.form-row{
    
    display: flex;
    flex-direction: column;
    justify-content: space-around ;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
    min-width: 100%;
    gap: 5px;
   
}
.input-container {
            margin-bottom: 15px;
            position: relative;
            flex-grow: 1;   
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            
        }

        .input-container label {
            display: block;
            margin-bottom: 5px;
        }
        .input-container input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .input-error {
            border: 1px solid red;
        }
        .input-success {
            border: 1px solid green;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        input{
    outline: none;
    border: 1px solid;
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 8px;
    border: 2px solid rgba(0, 0, 0, 0.5);
    font-size: 12px;
    font-weight: 500;
    color: var(--secondary-emphasis);
    caret-color: var(--secondary-emphasis);
    background-color: var(--off-white);
    font-family: 'Poppins', sans-serif;
    
}

input:focus{
    border: 2px solid var(--secondary-emphasis);
    transition: ease-in .1s;
}


.placeholder{
    position: absolute;
    top: 4px;
    left: 8px;
    color: var(--secondary-emphasis);
    font-weight: 500;    
    transition: .3s;
    pointer-events: none;
}


input:focus + .placeholder,
input:not(:placeholder-shown) + .placeholder {
    font-size: 12px;
    top: -16px;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    appearance: textfield;
}

input[type="submit"]{
    color: white;
    background-color: var(--secondary-emphasis);
    cursor: pointer;
    transition: ease-in-out .2s;
    margin-bottom: 5px;
}
input[type="submit"]:hover{
    scale: 1.025;
    background-color: #3B6603;
}

a{
    text-decoration: none;
    color: black;
}

a:hover {
    color: #EFC340;
}

    </Style>
<body>

    <main class="container">
    <header>
        <h1 id="brand-title">Biblietec</h1>
        <span class="separation-line"></span>
        <h1 id="login-title">Código De Autenticação</h1>
    </header>
    <div class="formsContainer">
  
        <form action="" method="post">
        <div class="form-row">
        <div class="input-container">   
                <input type="text" name="email" placeholder=""  required >
                <label class="placeholder">E-mail:</label><br>
            </div>
        </div>
        <div class="formBtn">
            <input type="submit" name="SendRecupSenha" value="Recuperar" ><br>
            <a href="protect.php" name="SendLembrouSenha">Lembrou a Senha?</a>
        </div>
        <div class="mensagemErro">
            <?php
            // Imprimir a mensagem da sessão
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>
        </div>
        </form>
    </div>
    </main>

</body>
</html>
