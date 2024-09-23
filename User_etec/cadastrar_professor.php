<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/defaults.css">
    <link rel="stylesheet" href="./css/cadastrar_funcionario.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="index.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Professor</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="nome" class="form-label">Nome Completo:</label>
                <input type="text" name="nome" placeholder="Nome Completo" required class="form-control">
            </div>
            <div>
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" placeholder="Email" required class="form-control">
            </div>
            <div>
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" placeholder="Senha" required class="form-control">
            </div>
            <div>
                <label for="senha2" class="form-label">Confirmar Senha:</label>
                <input type="password" name="senha2" placeholder="Confirmar Senha" required class="form-control">
            </div>
            <div>
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="tel" name="telefone" placeholder="Telefone" class="form-control">
            </div>
            <div>
                <label for="celular" class="form-label">Celular:</label>
                <input type="tel" name="celular" placeholder="Celular" required class="form-control">
            </div>
            <div>
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" name="cpf" placeholder="CPF" required class="form-control">
            </div>
            <div>
                <label for="codigo_escola" class="form-label">Código da Etec:</label>
                <input type="text" name="codigo_escola" placeholder="Código da Etec" required class="form-control">
            </div>
            <div class="seletor">
                <label for="acesso" class="form-label">Tipo de Acesso:</label>
                <select name="acesso" id="acesso" required class="form-select">
                    <option value="">Selecione o tipo de acesso</option>
                    <option value="professor">Professor</option>
                </select>
            </div>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

    <?php
    include '../conexao.php'; // Inclui o arquivo de conexão

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];
        $senha2 = $_POST['senha2'];
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);

        // Validação do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p>Email inválido.</p>";
        }

        // Validação do CPF
        if (!preg_match('/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', $cpf)) {
            echo "<p>CPF inválido. Use o formato XXX.XXX.XXX-XX.</p>";
        }

        // Verifica se as senhas coincidem
        if ($senha !== $senha2) {
            echo "<p>As senhas não coincidem.</p>";
            exit();
        }

        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO tbprofessores (nome, email, senha, telefone, celular, cpf, codigo_escola, acesso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nome, $email, $senhaHash, $telefone, $celular, $cpf, $codigo_escola, $acesso);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar o professor: " . $conn->error . "</p>";
        }

        $conn->close();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
