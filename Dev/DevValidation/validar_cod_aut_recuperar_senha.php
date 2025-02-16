<?php
session_start();



//importa as classe do phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Fuso horário do lugar informado 
date_default_timezone_set('America/Sao_Paulo');

// Inclui a conexão MySQLi
include_once("../../conexao/conexao.php");

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
                        $link = "http://localhost/TCC-Biblioetec/Dev/DevScreen/esqueceuSenha.php?chave=" . urlencode($chave_recuperar_senha);

                        // Incluir o composer
                        require '../../lib/vendor/autoload.php';
                        

                        // Criar o objeto e instanciar a classe do PHPMailer
                        $mail = new PHPMailer(true);

                        try {                    
                            // Server settings
                            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Desativar o modo de depuração
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();
                            $mail->Host       = 'sandbox.smtp.mailtrap.io';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = '83b76c3790613b';
                            $mail->Password   = '62ae586ad2ecb3';
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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../DevCss/defaults.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-screen h-screen flex flex-col items-center justify-center bg-[var(--off-white)]">

    <main class="min-w-[320px] w-[320px] sm:w-[392px] flex flex-col gap-4 pb-2 sm:pb-2 p-4 sm:p-8 bg-white rounded-md shadow-md">
        <header class="flex gap-2 justify-center items-center">
            <h1 class="text-3xl sm:text-4xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
            <span class="w-[2px] h-6 sm:h-8  bg-secondary"></span>
            <h1 class="text-3xl sm:text-4xl font-regular text-secondary">Senha</h1>
        </header>
        <form action="" method="post" class="flex flex-col gap-4">
            <div class="min-w-full flex flex-col">
                <label class="text-secondary font-medium">E-mail:</label>
                <input type="text" name="email" placeholder=""  required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
            </div>
            <div class="">
                <input type="submit" name="SendRecupSenha" value="Recuperar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer"><br>
                <a href="protect.php" name="SendLembrouSenha" class="text-secondary text-xs text-right underline float-right mt-1">Lembrou a Senha?</a>
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
    </main>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>
