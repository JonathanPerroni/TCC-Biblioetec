<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Admin</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/defaults.css">
    <link rel="stylesheet" href="./css/cadastrar_admin.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-itens-center">
        <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
            <a href="index.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                <span class="fw-medium">Início</span>
            </a>
            <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Administrador</a>
        </header>
        <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
            <form action="" method="POST" class="d-flex flex-column gap-4">
                <div>
                    <label for="nome" class="form-label">Nome completo:</label>
                    <input type="text" name="nome" placeholder="Insira o nome completo" required class="form-control">
                </div>
                <div>
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" placeholder="Insira o email" required class="form-control">
                </div>
                <div>
                    <Senha: for="senha" class="form-label">Senha:</label>
                    <input type="password" name="senha" placeholder="Insira a senha" required class="form-control">
                </div>
                <div>
                    <label for="senha2" class="form-label">Confirme a senha:</label>
                    <input type="password" name="senha2" placeholder="Confirme a senha" required class="form-control">
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
                <div class="breakable-row d-flex justify-between gap-4">
                    <div class="w-100">
                        <label for="cpf" class="form-label">CPF:</label>
                        <input type="text" name="cpf" placeholder="Insira o CPF" required class="form-control">
                    </div>
                    <div class="w-50">
                        <label for="codigo_escola" class="form-label">Código da ETEC:</label>
                        <input type="text" name="codigo_escola" placeholder="Insira o código da ETEC" required class="form-control">
                    </div>
                </div>
                <div>
                    <label for="acesso" class="form-label">Confirme o acesso:</label>
                    <select name="acesso" id="acesso" required class="form-select">
                        <option value="">Tipo de Acesso</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
    
    <?php
    include("../conexao.php");

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>