<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de ETEC</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/defaults.css">
    <link rel="stylesheet" href="./css/cadastrar_etec.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-itens-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="index.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar ETEC</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="nome_escola" class="form-label">Nome da Etec:</label>
                <input type="text" name="nome_escola" placeholder="Insira o nome da Etec" required class="form-control">
            </div>
            <div>
                <label for="tipo_escola" class="form-label">Tipo de Etec:</label>
                <input type="text" name="tipo_escola" placeholder="Insira o tipo de Etec" required class="form-control">
            </div>
            <div>
                <label for="codigo_escola" class="form-label">Código da Etec:</label>
                <input type="text" name="codigo_escola" placeholder="Insira o código da Etec" required class="form-control">
            </div>
            <div>
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" name="endereco" placeholder="Insira o endereço" required class="form-control">
            </div>
            <div>
                <label for="bairro" class="form-label">Bairro:</label>
                <input type="text" name="bairro" placeholder="Insira o bairro" required class="form-control">
            </div>
            <div>
                <label for="cidade" class="form-label">Cidade:</label>
                <input type="text" name="cidade" placeholder="Insira a cidade" required class="form-control">
            </div>
            <div>
                <label for="estado" class="form-label">Estado:</label>
                <input type="text" name="estado" placeholder="Insira o estado" required class="form-control">
            </div>
            <div>
                <label for="cnpj" class="form-label">CNPJ:</label>
                <input type="text" name="cnpj" placeholder="Insira o CNPJ" required class="form-control">
            </div>
            <div class="breakable-row d-flex justify-between gap-4">
                <div class="w-100">
                    <label for="telefone" class="form-label">Telefone:</label>
                    <input type="tel" name="telefone" placeholder="Insira o telefone" class="form-control">
                </div>
                <div class="w-100">
                    <label for="celular" class="form-label">Celular:</label>
                    <input type="tel" name="celular" placeholder="Insira o celular" required class="form-control">
                </div>
            </div>
            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

    <?php
    include '../conexao.php'; // Inclui o arquivo de conexão

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $tipo_escola = filter_input(INPUT_POST, 'tipo_escola', FILTER_SANITIZE_STRING);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
        $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
        $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
        $cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);

        // Validação de CNPJ
        if (!preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', $cnpj)) {
            echo "<p>CNPJ inválido. Use o formato XX.XXX.XXX/XXXX-XX.</p>";
        }

        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO tbescola (nome_escola, tipo_escola, codigo_escola, endereco, bairro, cidade, estado, cnpj, telefone, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nome_escola, $tipo_escola, $codigo_escola, $endereco, $bairro, $cidade, $estado, $cnpj, $telefone, $celular);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar a ETEC.</p>";
        }

        $conn->close();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>