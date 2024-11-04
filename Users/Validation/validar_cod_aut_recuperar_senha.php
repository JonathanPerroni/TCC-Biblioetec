<?php
session_start();

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Sao_Paulo');
include_once("../../conexao/conexao.php");

// Receber os dados do usuário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Verifica se o botão de recuperação de senha foi clicado
if (!empty($dados['SendRecupSenha'])) {
    // Valida o tipo de acesso
    $acesso = $dados['tipo_acesso'];
    $tabelasAcesso = [
        'admin' => 'tbadmin',
        'funcionario' => 'tbfuncionarios',
        'bibliotecario' => 'tbbibliotecario',
        'professor' => 'tbprofessores',
        'aluno' => 'tbalunos'
    ];

    // Verifica se o tipo de acesso é válido
    if (array_key_exists($acesso, $tabelasAcesso)) {
        $tabela = $tabelasAcesso[$acesso];

        // Query para buscar o usuário pelo email e tipo de acesso
        $query_usuario = "SELECT codigo, nome, email
                          FROM $tabela
                          WHERE email = ? 
                          LIMIT 1";

        // Prepara a consulta
        if ($stmt = $conn->prepare($query_usuario)) {
            $stmt->bind_param('s', $dados['email']);

            // Executa a consulta
            if ($stmt->execute()) {
                $result_usuario = $stmt->get_result();
                if ($result_usuario->num_rows > 0) {
                    $row_usuario = $result_usuario->fetch_assoc();
                    $chave_recuperar_senha = password_hash($row_usuario['codigo'] . $row_usuario['email'], PASSWORD_DEFAULT);

                    // Atualiza a chave de recuperação de senha
                    $query_up_usuario = "UPDATE $tabela
                                         SET chave_recuperar_senha = ?
                                         WHERE codigo = ?
                                         LIMIT 1";

                    if ($stmt_up = $conn->prepare($query_up_usuario)) {
                        $stmt_up->bind_param('si', $chave_recuperar_senha, $row_usuario['codigo']);

                        if ($stmt_up->execute()) {
                            // Gera o link de recuperação de senha
                            $link = "http://localhost/biblioetec/Desenvolvedor/Users/login/esqueceuSenha.php?chave=" . urlencode($chave_recuperar_senha);

                            require '../../lib/vendor/autoload.php';

                            $mail = new PHPMailer(true);

                            try {
                                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                                $mail->CharSet = 'UTF-8';
                                $mail->isSMTP();
                                $mail->Host       = 'sandbox.smtp.mailtrap.io';
                                $mail->SMTPAuth   = true;
                                $mail->Username   = '83b76c3790613b';
                                $mail->Password   = '62ae586ad2ecb3';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port       = 465;

                                $mail->setFrom('DevBiblioEtec@devs.com', 'Atendimento');
                                $mail->addAddress($row_usuario['email'], $row_usuario['nome']);

                                $mail->isHTML(true);
                                $mail->Subject = 'Recuperar Senha';
                                $mail->Body    = "Olá " . $row_usuario['nome'] . ", clique no link para recuperar sua senha: <a href='$link'>$link</a>";
                                $mail->AltBody = "Olá " . $row_usuario['nome'] . ", copie e cole o link no navegador para recuperar sua senha: $link";

                                $mail->send();
                                $_SESSION['msg'] = "<p style='color: green;'> Confira seu email, link enviado </p>";
                                header('Location: ../login/login.php');
                            } catch (Exception $e) {
                                echo "Erro ao enviar o email: {$mail->ErrorInfo}";
                                $_SESSION['msg'] = "<p style='color: #f00;'> Erro: Email não enviado!</p>";
                            }
                        } else {
                            $_SESSION['msg'] = "<p style='color: yellow;'> Erro: Tente novamente!</p>";
                        }

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

            $stmt->close();
        } else {
            die('Erro ao preparar a query de seleção: ' . $conn->error);
        }
    } else {
        $_SESSION['msg'] = "<p style='color: #f00;'> Erro: Tipo de acesso inválido!</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../UserCss/defaults.css">
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
                <input type="text" name="email" placeholder="" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
            </div>
            <div class="min-w-full flex flex-col">
                <label class="text-secondary font-medium">Tipo de Acesso:</label>
                <select name="tipo_acesso" required class="border-2 border-[var(--secondary)] rounded text-secondary">
                    <option value="">Selecione</option>
                    <option value="admin">Administrador</option>
                    <option value="funcionario">Funcionário</option>
                    <option value="bibliotecario">Bibliotecário</option>
                    <option value="professor">Professor</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>
            <div>
                <input type="submit" name="SendRecupSenha" value="Recuperar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer"><br>
                <a href="protect.php" name="SendLembrouSenha" class="text-secondary text-xs text-right underline float-right mt-1">Lembrou a Senha?</a>
            </div>
            <div class="mensagemErro">
                <?php
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
