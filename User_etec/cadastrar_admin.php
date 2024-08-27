<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Admin</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Administrador</h1>
        <a href="index.php">Início</a>
        <br><br>
    </div>

    <div class="form-cadastro">
        <form action="" method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="senha2" placeholder="Confirmar Senha" required>
            <input type="text" name="telefone" placeholder="Telefone">
            <input type="text" name="celular" placeholder="Celular" required>
            <input type="text" name="cpf" placeholder="CPF" required>
            <input type="text" name="codigo_escola" placeholder="Código Etec" required>
            <div class="seletor">
                <select name="acesso" id="acesso" required>
                    <option value="">Tipo de Acesso</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>

            <button type="reset">Limpar</button>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];  // Senha não precisa de sanitização
        $senha2 = $_POST['senha2'];
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p>Email inválido.</p>";
            // continue the script instead of exit
        }

        if (!preg_match('/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', $cpf)) {
            echo "<p>CPF inválido. Use o formato XXX.XXX.XXX-XX.</p>";
            // continue the script instead of exit
        }

        // Verifica se as senhas coincidem
        if ($senha !== $senha2) {
            echo "<p>As senhas não coincidem.</p>";
            exit();
        }

        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $conn = new mysqli("localhost", "root", "", "bdescola");

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO tbadmin (nome, email, senha, telefone, celular, cpf, codigo_escola, acesso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nome, $email, $senhaHash, $telefone, $celular, $cpf, $codigo_escola, $acesso);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar o usuário: " . $conn->error . "</p>";
        }

        $conn->close();
    }
    ?>
    
</body>
</html>