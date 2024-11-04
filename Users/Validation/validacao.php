<?php
session_start();

ob_start(); // limpar o buff de saída
include_once("../../conexao/conexao.php");

date_default_timezone_set('America/Sao_Paulo');

//importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$conectar = filter_input(INPUT_POST, 'conectar', FILTER_SANITIZE_STRING);
if ($conectar) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Função para verificar o login em uma tabela específica e retornar os dados do usuário
    function verificarLogin($conn, $email, $password, $tabela, $acessoTipo) {
        $stmt = $conn->prepare("SELECT codigo, nome, cpf, email, password, telefone, celular, acesso, statusDev FROM $tabela WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado_usuario = $stmt->get_result();

        if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
            $row_usuario = $resultado_usuario->fetch_assoc();
            
            if ($row_usuario['statusDev'] == 0) {
                $_SESSION['msg'] = "Usuário bloqueado!";
                header("Location: ../loginDev.php");
                exit();
            }
            
            if (password_verify($password, $row_usuario['password'])) {
                $_SESSION['codigo'] = $row_usuario['codigo'];
                $_SESSION['nome'] = $row_usuario['nome'];
                $_SESSION['cpf'] = $row_usuario['cpf'];
                $_SESSION['email'] = $row_usuario['email'];
                $_SESSION['telefone'] = $row_usuario['telefone'];
                $_SESSION['celular'] = $row_usuario['celular'];
                $_SESSION['acesso'] = $acessoTipo;

                return $row_usuario; // Retorna os dados do usuário
            }
        }
        return false;
    }

    // Verifica em qual tabela o usuário está e define o tipo de acesso
    $acessoEncontrado = false;
    $tabelasAcesso = [
        'tbadmin' => 'administrador',
        'tbfuncionarios' => 'funcionario',
        'tbbibliotecario' => 'bibliotecario',
        'tbprofessores' => 'professor',
        'tbalunos' => 'aluno'
    ];

    $row_usuario = null; // Inicializa a variável

    foreach ($tabelasAcesso as $tabela => $acessoTipo) {
        $usuario = verificarLogin($conn, $email, $password, $tabela, $acessoTipo);
        if ($usuario) {
            $row_usuario = $usuario; // Armazena os dados do usuário
            $acessoEncontrado = true;
            break;
        }
    }

    if ($acessoEncontrado) {
        // Gerar número e letras aleatórios para o código de autenticação
        function gerarCodigoRandomico($tamanho = 8) {
            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codigo_autenticacao = '';
        
            for ($i = 0; $i < $tamanho; $i++) {
                $indiceAleatorio = mt_rand(0, strlen($caracteres) - 1);
                $codigo_autenticacao .= $caracteres[$indiceAleatorio];
            }
            return $codigo_autenticacao;
        }

        $codigo_autenticacao = gerarCodigoRandomico(6);
        $data = date('Y-m-d H:i:s');

        // Lista das tabelas que devem ser atualizadas
        $tabelas = ['tbadmin', 'tbfuncionarios', 'tbbibliotecario', 'tbprofessores', 'tbalunos'];
        // Atualizar o código de autenticação no banco
        foreach ($tabelas as $tabela) {
            // Verifica se a coluna existe antes de atualizar
            $verificaColuna = $conn->query("SHOW COLUMNS FROM $tabela LIKE 'codigo_autenticacao'");
            if ($verificaColuna->num_rows > 0) {
                $query_up_usuario = "UPDATE $tabela SET codigo_autenticacao = ?, data_codigo_autenticacao = ? WHERE codigo = ? LIMIT 1";
                
                $stmt = $conn->prepare($query_up_usuario);
                
                if ($stmt === false) {
                    die("Erro ao preparar a query: " . $conn->error);
                }

                $stmt->bind_param("sss", $codigo_autenticacao, $data, $_SESSION['codigo']);

                // Executa a atualização e verifica por erros
                if (!$stmt->execute()) {
                    echo "Erro ao atualizar a tabela $tabela: " . $stmt->error;
                }

                $stmt->close(); // Fechar a declaração após a execução
            } else {
                echo "A coluna 'codigo_autenticacao' não existe na tabela $tabela.";
            }
        }

        //incluir o composer
        require '../../lib/vendor/autoload.php';
        
        //cria o objeto e instanciar a classe do PHPMailer
        $mail = new PHPMailer(true);

        //VERIFICA SE ENVIA O EMAIL CORRETAMENTE COM O TRY CATCH
        try {                    
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;             // imprimir os erros

            //PERMITIR O ENVIO DO EMAIL COM CARCTER ESPECIAL
            $mail->CharSet = 'UTF-8';

            $mail->isSMTP();                                   // definir para usar a SMTP
            $mail->Host       = 'sandbox.smtp.mailtrap.io';   // Serviço de envio de email
            $mail->SMTPAuth   = true;                          // indica que é necessário autenticar
            $mail->Username   = '0a1cfc235b42dd';              // usuario/e-mail para enviar o email
            $mail->Password   = 'c23abe058e2e4f';              //senha do email para enviar email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //ativar criptografia
            $mail->Port       = 465;                           //porta para enviar o email
            
            //email do remetente 
            $mail->setFrom('DevBiblioEtec@devs.com', 'Atendimento');
            //email do destinatário 
            $mail->addAddress($row_usuario['email'], $row_usuario['nome']);     //Add a recipient
            
            // Definir o formato do email para html
            $mail->isHTML(true);                   
            //titulo do email
            $mail->Subject = 'Aqui está o código de verificação';
            
            //conteudo do email em formato HTML
            $mail->Body    = "Olá! " . $row_usuario['nome'] . ", Autenticação de multifator.<br><br>
            seu código de verificação de 6 dígitos é $codigo_autenticacao.<br><br> Esse código foi enviado
            para validar.<br><br>";
            //conteudo do email em formato texto
            $mail->AltBody = "Olá! " . $row_usuario['nome'] . ", Autenticação de multifator.\n\n
            seu código de verificação de 6 dígitos é $codigo_autenticacao.\n\n Esse código foi enviado
            para validar.\n\n";

            //enviar o email
            $mail->send();
            header('Location: validar_cod_autenticacao.php');

            // Redirecionar para a página de acordo com o tipo de acesso
            

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $_SESSION['msg'] = "<p style='color: #f00;'> Erro: Email não enviado!</p>";
        }

    } else {
        $_SESSION['msg'] = "Senha incorreta!";
        header("Location: ../login/login.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "Email incorreto!";
    header("Location: ../login/login.php");
    exit();
}
