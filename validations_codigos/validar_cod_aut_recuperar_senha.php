<?php
session_start();

ob_start(); // Limpar o buffer de saída

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
    //var_dump($dados);

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
                //var_dump($row_usuario);
                
                // Gerar a chave para recuperar a senha
                $chave_recuperar_senha = password_hash($row_usuario['codigo'] . $row_usuario['email'], PASSWORD_DEFAULT);
                //var_dump($chave_recuperar_senha);
                
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
                        //gerar o link recuperar senha!
                        $link = "http://localhost/biblioetec/Desenvolvedor/Screen/esqueceuSenha.php?chave=" . urlencode($chave_recuperar_senha);
                        //var_dump($link);

                        //incluir o composer
                        require '../lib/vendor/autoload.php';

                        //cria o objeto e  instanciar  a classe  do PHPMailer
                         $mail = new PHPMailer(true);

                         try {                    
                            //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;             // imprimir os erros
    
                        //PERMITIR  O ENVIO DO EMAIL COM CARCTER ESPECIAL
                        $mail->CharSet = 'UTF-8';

                        $mail->isSMTP();                                   // definir para usar a SMTP
                        $mail->Host       = 'sandbox.smtp.mailtrap.io';            // Serviço de envio de email
                        $mail->SMTPAuth   = true;                          // indica que é nescessario autenticar
                        $mail->Username   = '0a1cfc235b42dd';            // usuario/e-mail para enviar  o email
                        $mail->Password   = 'c23abe058e2e4f';                      //senha do email para enviar email

                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   //ativar criptografia
                        $mail->Port       = 465;                           //porta para enviar o email

                        //email do remetente 
                        $mail->setFrom('DevBiblioEtec@devs.com', 'Atendimento');
                        //email do destinatraio 
                        $mail->addAddress($row_usuario['email'], $row_usuario['nome']);     //Add a recipient
                        
                        // Definir o formato do email para html
                        $mail->isHTML(true);                   
                        
                        $mail->isHTML(true);                   
                        //titulo do email
                        $mail->Subject = 'Recuperar Senha';
    
                        //conteudo  do email  em formato HTML
                        $mail->Body    = "Ola! " . $row_usuario['nome'] . ", Você solicitou alteração de senha.<br><br>
                        Para continuar o processo de recuperação de sua senha, clique no link abaixo <br><br>
                        ou cole o endereço no seu navegador: <br><br>
                        <a href='" .$link. "'>" .$link. "</a><br><br>Se você não solicitou essa alteração, nenhuma ação é nescessaria. <br>
                        Sua senha permnaecerá a mesma até que você ative este codigo.<br><br>" ;
                        
                        //conteudo do email em formato texto
                        $mail->AltBody = "Ola! " . $row_usuario['nome'] . " \n\nVoce solicitou a alteração de senha.\n\n
                        Para continuar o processo de recuperação de sua senha, clique no link abaixo ou cole endereço do navegador\n\n"
                        . $link. "\n\n Se voce não solicitou  essa alteração, nenhuma ação é nescessaria.
                         \n\nSua senha permanecerá  a mesma até que voce ative este codigo.\n\n";
    
                        //enviar o email
                        $mail->send();
                            
                        //Criar a variavel global com a mensagem de sucesso!!
                        $_SESSION['msg'] = "<p styler='color: green ;'> Enviado e-mail com instruções para recuperar
                        a senha acesse a sua caixa de e-mail para recuperar a senha!!</p>";

                        //redirecionar o usuario 
                        header('Location: ../loginDev.php');
    

                    exit();


                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        $_SESSION['msg'] = "<p styler='color: #f00;'> ErRo: Email nao enviado!</p>";
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
</head>
<body>

    <main class="container">
    <header>
        <h1 id="brand-title">Biblietec</h1>
        <span class="separation-line"></span>
        <h1 id="login-title">Código De Autenticação</h1>
    </header>
    <div class="formsContainer">
         
        <div class="mensagemErro">
            <?php
            // Imprimir a mensagem da sessão
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>
        </div>
        <form action="" method="post">
        <div class="form-row">
            <div class="input-group">
                <label>E-mail:</label><br>
                <input type="text" name="email" placeholder="Digite seu email" required ><br><br>
            </div>
        </div>
        <div class="formBtn">
            <input type="submit" name="SendRecupSenha" value="Recuperar" ><br>
            <a href="../loginDev.php">Lembrou a Senha?</a>
        </div>
        </form>
    </div>
    </main>

</body>
</html>
