<?php
session_start(); // Iniciar sessão
ob_start(); // Limpar o buffer de saída

date_default_timezone_set('America/Sao_Paulo');

include_once "../../conexao/conexao.php"; // Conexão

// Recebendo a chave
$chave_recuperar_senha = filter_input(INPUT_GET, 'chave', FILTER_DEFAULT);

if (empty($chave_recuperar_senha)) {
    $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Link Inválido!!!</p>";
     header('Location: ../../logout.php');
    exit();
} else {
    $query_usuario = "SELECT codigo FROM tbdev WHERE chave_recuperar_senha = ? LIMIT 1";
    $stmt = $conn->prepare($query_usuario);

    if ($stmt) {
        $stmt->bind_param('s', $chave_recuperar_senha);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Link Inválido!!!</p>";
            header('Location: ../../logout.php');
            exit();
        } else {
            $stmt->bind_result($codigo_usuario);
            $stmt->fetch();
        }

        $stmt->close();
    }
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../DevCss/defaults.css">

    <style>
        /* Estiliza o corpo da página para centralizar o conteúdo */
        body {
            height: 90vh;
            justify-content: center;
            align-items: center;
        }

        /* Estiliza o elemento principal para definir largura, sombras e alinhamento */
        main {
            max-width: 700px;
            width: 100%;
            height: auto;
            padding: 16px;
            border: none;
            border-radius: 16px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.25);
            background-color: white;
            display: flex;
            flex-direction: column;
        }

        /* Centraliza o conteúdo do cabeçalho */
        header {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;
        }

        /* Define o tamanho da fonte para o título principal */
        h1 {
            font-size: 20px;
        }

        /* Define a linha de separação no layout */
        .separation-line {
            height: 40px;
            width: 2px;
            margin: 0px 16px;
            background-color: var(--off-white);
            color: var(--off-white);
        }

        /* Estiliza o título da marca */
        #brand-title {
            color: var(--primary-emphasis);
            font-weight: 600;
            width: 200px;
        }

        /* Estiliza o título da página de cadastro */
        #cad-title {
            color: var(--off-black);
            font-weight: 400;
            width: 200px;
        }

        /* Define o estilo da seção de recuperação de senha */
        .EsqueceuSenha {
            margin-top: 10px;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        /* Estiliza o formulário */
        form {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Define a disposição das linhas do formulário */
        .form-row {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 10px;
            width: 100%;
            min-width: 100%;
            gap: 16px;
        }

        /* Define o contêiner para os inputs */
        .input-container {
            margin-bottom: 15px;
            position: relative;
            flex-grow: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Estiliza os rótulos dos inputs */
        .input-container label {
            display: block;
            margin-bottom: 5px;
        }

        /* Estiliza os inputs */
        .input-container input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Adiciona borda vermelha em inputs com erro */
        .input-error {
            border: 1px solid red;
        }

        /* Adiciona borda verde em inputs corretos */
        .input-success {
            border: 1px solid green;
        }

        /* Estiliza a mensagem de erro */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Estiliza os inputs */
        input {
            outline: none;
            width: 100%;
            height: 32px;
            padding: 0 8px;
            border-radius: 8px;
            border: 2px solid rgba(0, 0, 0, 0.5);
            font-size: 16px;
            font-weight: 500;
            color: var(--secondary-emphasis);
            caret-color: var(--secondary-emphasis);
            background-color: var(--off-white);
            font-family: 'Poppins', sans-serif;
        }

        /* Adiciona animação de foco aos inputs */
        input:focus {
            border: 2px solid var(--secondary-emphasis);
            transition: ease-in 0.1s;
        }

        /* Estiliza o placeholder dos inputs */
        .placeholder {
            position: absolute;
            top: 4px;
            left: 8px;
            color: var(--secondary-emphasis);
            font-weight: 500;
            transition: 0.3s;
            pointer-events: none;
        }

        /* Anima o placeholder ao focar ou preencher o input */
        input:focus+.placeholder,
        input:not(:placeholder-shown)+.placeholder {
            font-size: 12px;
            top: -16px;
        }

        /* Remove os botões de spin em inputs de número */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Torna inputs de número semelhantes a campos de texto */
        input[type="number"] {
            appearance: textfield;
        }

        /* Estiliza o botão de submissão */
        input[type="submit"] {
            color: white;
            background-color: var(--secondary-emphasis);
            cursor: pointer;
            transition: ease-in-out 0.2s;
            margin-bottom: 5px;
        }

        /* Adiciona animação ao hover do botão */
        input[type="submit"]:hover {
            scale: 1.025;
            background-color: #3B6603;
        }

        /* Define o estilo do contêiner dos botões */
        .container-btn {
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: 0 5px;
        }

        /* Estiliza links (como botões) */
        a {
            color: white;
            background-color: var(--secondary-emphasis);
            cursor: pointer;
            transition: ease-in-out 0.2s;
            margin-bottom: 5px;
            width: 100%;
            border-radius: 8px;
            border: 2px solid rgba(0, 0, 0, 0.5);
            text-decoration: none;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
        }

        /* Adiciona animação ao hover dos links */
        a:hover {
            scale: 1.025;
            background-color: #B20000;
        }

        /* Define o estilo para notificações de erro */
        .erros-notification {
            display: flex;
            flex-direction: row;
            width: 100%;
            height: auto;
            gap: 5px 10px;
            justify-content: center;
            align-items: center;
        }

        /* Media query para telas menores (responsividade) */
        @media (max-width: 465px) {

            /* Ajusta o corpo para centralizar em telas menores */
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            /* Ajusta o main em telas menores */
            main {
                display: flex;
                align-items: center;
            }

            /* Ajusta o cabeçalho para telas menores */
            header {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                text-align: center;
                width: 200px;
                height: auto;
            }

            /* Ajusta a linha de separação para telas menores */
            .separation-line {
                height: 2px;
                width: 100%;
                margin: 0px 16px;
                background-color: var(--off-white);
                color: var(--off-white);
            }

            /* Ajusta a seção de recuperação de senha para telas menores */
            .EsqueceuSenha {
                display: flex;
                flex-direction: column;
            }

            /* Ajusta o formulário para telas menores */
            form {
                display: flex;
                flex-wrap: wrap;
                flex-direction: column;
            }

            /* Ajusta as linhas do formulário para telas menores */
            .form-row {
                display: flex;
                flex-wrap: wrap;
                flex-direction: column;
            }

            /* Ajusta o contêiner de botões para telas menores */
            .container-btn {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0 5px;
            }
        }
    </style>


</head>

<body>
    <main class="container">
        <header>
            <h1 id="brand-title">Biblietec</h1>
            <span class="separation-line"></span>
            <h1 id="cad-title">Trocar a Senha</h1>
        </header>
        <div class="EsqueceuSenha">

            <form action="" method="post">
            <?php

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($dados['SendNovaSenha'])) {
    // Verifica se a senha e a confirmação são iguais
    if ($dados['password'] === $dados['confirm_password']) {
        // Verifica o comprimento da senha
        if (strlen($dados['password']) >= 8) {
            $criptoPass = password_hash($dados['password'], PASSWORD_DEFAULT);
            $nova_chave_recuperar_senha = NULL;

            $query_update_usuario = "UPDATE tbdev SET password = ?, chave_recuperar_senha = ? WHERE codigo = ? LIMIT 1";
            $stmt_update = $conn->prepare($query_update_usuario);

            if ($stmt_update) {
                $stmt_update->bind_param('ssi', $criptoPass, $nova_chave_recuperar_senha, $codigo_usuario);

                if ($stmt_update->execute()) {
                    $_SESSION['msg'] = "<p style='color: green;'>Senha atualizada com sucesso!</p>";
                    header('Location: ../loginDev.php');
                    exit();
                } else {
                    $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Tente novamente!</p>";
                }

                $stmt_update->close();
            }
        } else {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: A senha precisa ter pelo menos 8 caracteres!</p>";
        }
    } else {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: As senhas não coincidem!</p>";
    }
}

?>

                <div class="form-row">
                    <div class="input-container">
                        <input type="password" name="password" id="password" placeholder=""
                            value="<?php echo isset($_SESSION['values']['password']) ? htmlspecialchars($_SESSION['values']['password']) : ''; ?>"
                            required>
                        <label for="password" class="placeholder">Senha:</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-container">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder=""
                            value="<?php echo isset($_SESSION['values']['confirm_password']) ? htmlspecialchars($_SESSION['values']['confirm_password']) : ''; ?>"
                            required>
                        <label for="confirm_password" class="placeholder">Confirmar Senha:</label>

                    </div>
                </div>

                <div class="container-btn">
                    <input type="submit" name="SendNovaSenha" value="Enviar">
                    <a href="../DevScreen/logout.php" class="voltar">Voltar</a>
                </div>
            </form>
            <p>
                <?php
    if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    unset($_SESSION['values']);
    unset($_SESSION['errors']);
    ?>
            </p>
        </div>
    </main>


</body>

</html>