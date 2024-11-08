<?php
session_start();
include_once "../../conexao/conexao.php";

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recebe os dados do formulário
    $codigo_usuario = filter_input(INPUT_POST, 'codigo_usuario', FILTER_VALIDATE_INT);
    $chave_recuperar_senha = filter_input(INPUT_POST, 'chave_recuperar_senha', FILTER_DEFAULT);
    $nova_senha = filter_input(INPUT_POST, 'nova_senha', FILTER_DEFAULT);
    $confirmar_senha = filter_input(INPUT_POST, 'confirmar_senha', FILTER_DEFAULT);

    // Valida se as senhas coincidem
    if ($nova_senha !== $confirmar_senha) {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: As senhas não coincidem!</p>";
        header("Location: ../../app/redefinir-senha.php?chave=$chave_recuperar_senha");
        exit();
    }

    // Valida a força da senha (mínimo 8 caracteres, pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $nova_senha)) {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: A senha deve conter pelo menos 8 caracteres, uma letra maiúscula, uma minúscula, um número e um caractere especial.</p>";
        header("Location: ../../app/redefinir-senha.php?chave=$chave_recuperar_senha");
        exit();
    }

    // Atualiza a senha no banco de dados
    $query = "UPDATE $tabela_usuario SET senha = ? WHERE codigo = ? AND chave_recuperar_senha = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Criptografa a nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Executa a atualização
        $stmt->bind_param('sis', $senha_hash, $codigo_usuario, $chave_recuperar_senha);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p style='color: #0a0;'>Senha redefinida com sucesso!</p>";
            header("Location: ../../login.php"); // Redireciona para a página de login
        } else {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro ao redefinir a senha. Tente novamente!</p>";
        }
        $stmt->close();
    } else {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro ao preparar a consulta. Tente novamente!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="../UserCss/defaults.css">
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

        /* Media query para telas menores (max 768px) */
        @media (max-width: 768px) {
            /* Estiliza o cabeçalho da página */
            header {
                flex-direction: column;
            }

            /* Define tamanho da fonte para o título da marca em telas menores */
            #brand-title {
                font-size: 16px;
            }

            /* Define tamanho da fonte para o título da página de cadastro em telas menores */
            #cad-title {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <h1 id="brand-title">BIBLIOTECA ETEC</h1>
            <div class="separation-line"></div>
            <h1 id="cad-title">Recuperar Senha</h1>
        </header>
        <div class="EsqueceuSenha">
            <form method="POST" action="../../app/redefinir-senha.php">
                <div class="form-row">
                    <input type="hidden" name="codigo_usuario" value="<?php echo $codigo_usuario; ?>">
                    <input type="hidden" name="chave_recuperar_senha" value="<?php echo $chave_recuperar_senha; ?>">
                </div>
                <div class="form-row">
                    <div class="input-container">
                        <label for="nova_senha">Nova Senha</label>
                        <input type="password" name="nova_senha" id="nova_senha" placeholder="Nova Senha" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-container">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirmar Senha" required>
                    </div>
                </div>
                <div class="form-row">
                    <input type="submit" value="Redefinir Senha">
                </div>
            </form>
        </div>
        <div class="erros-notification">
            <?php
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>
        </div>
    </main>
</body>
</html>
