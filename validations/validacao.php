<?php
session_start();

ob_start(); // limpar o buff de saida 
include_once("../conexao.php");

data_default_timezone_set('America/Sao_Paulo');


//importa as classe do phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;




$conectar = filter_input(INPUT_POST, 'conectar', FILTER_SANITIZE_STRING);
if ($conectar) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Pesquisar o usuário no banco de dados
    if (!empty($email) && !empty($password)) {
        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("SELECT codigo, nome, cpf, email, password, telefone, celular, acesso FROM tbdev WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado_usuario = $stmt->get_result();

        if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
            $row_usuario = $resultado_usuario->fetch_assoc();
            
            // Verifique se a senha está sendo comparada corretamente
            if (password_verify($password, $row_usuario['password'])) {
                $_SESSION['nome'] = $row_usuario['nome'];
                $_SESSION['cpf'] = $row_usuario['cpf'];
                $_SESSION['email'] = $row_usuario['email'];
                $_SESSION['telefone'] = $row_usuario['telefone'];
                $_SESSION['celular'] = $row_usuario['celular'];
                $_SESSION['acesso'] = $row_usuario['acesso'];

                // Recuperar a data atual
                $data = date('Y-m-d H:i:s');
                
                // Gerar número e letras aleatórios
                function gerarCodigoRandomico($tamanho = 8) {
                    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $codigo_autenticacao = '';
                
                    for ($i = 0; $i < $tamanho; $i++) {
                        $indiceAleatorio = mt_rand(0, strlen($caracteres) - 1);
                        $codigo_autenticacao .= $caracteres[$indiceAleatorio];
                    }                
                    return $codigo_autenticacao;
                }
                                
                $codigo_autenticacao = gerarCodigoRandomico(6); // Gera um código de 6 caracteres
                                
                // Pra salvar no banco de dados 
                $query_up_usuario = "UPDATE tbdev SET 
                    codigo_autenticacao = ?,
                    data_codigo_autenticacao = ?
                    WHERE codigo = ?
                    LIMIT 1";

                // Preparar a query
                $result_up_usuario = $conn->prepare($query_up_usuario);

                if ($result_up_usuario === false) {
                    die("Erro ao preparar a query: " . $conn->error);
                }

                // Substituir link da query pelo valor
                $result_up_usuario->bind_param("sss", $codigo_autenticacao, $data, $row_usuario['codigo']);
                
                // Executar a query
                $result_up_usuario->execute();

                //incluir o composer
                require '../lib/vendor/autoload.php';
                
                //cria o objeto e  instanciar  a classe  do PHPMailer
                $mail = new PHPMailer(true);

                //VERIFICA SE ENVIA O EMAIL CORRETAMENTE COM O TRY CATCH
                try {                    
                        //Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;             // imprimir os erros

                    //PERMITIR  O ENVIO DO EMAIL COM CARCTER ESPECIAL
                    $email->CharSet = 'UTF-8';

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
                    //titulo do email
                    $mail->Subject = 'Aqui esta o codigo de verificação';
                    //conteudo  do email  em formato HTML
                    $mail->Body    = "Ola! " . $row_usuario['nome'] . ", Autenticação  de multifator.<br><br>
                    seu codigo de verificação de 6 digitos é $codigo_autenticacao.<br><br> Esse codigo foi  enviado
                    para validar.<br><br>";
                    //conteudo do email em formato texto
                    $mail->AltBody = "Ola! " . $row_usuario['nome'] . ", Autenticação  de multifator.\n\n
                    seu codigo de verificação de 6 digitos é $codigo_autenticacao.\n\n Esse codigo foi  enviado
                    para validar.\n\n";

                    //enviar o email
                    $mail->send();

                    header('Location: validar_codigo.php');

                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $_SESSION['msg'] = "<p styler='color: #f00;'> ErRo: Email nao enviado!</p>";
                }


                

                if ($result_up_usuario === false) {
                    die("Erro ao executar a query: " . $result_up_usuario->error);
                }

                header("Location: ../Screen/pagedev.php");
                exit();
            } else {
                $_SESSION['msg'] = "Senha incorreta!";
                header("Location: ../loginDev.php");
                exit();
            }
        } else {
            $_SESSION['msg'] = "Email incorreto!";
            header("Location: ../loginDev.php");
            exit();
        }
    } else {
        $_SESSION['msg'] = "Preencha todos os campos!";
        header("Location:  ../loginDev.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "PÁGINA NÃO ENCONTRADA";
    header("Location: ../loginDev.php");
    exit();
}
?>
