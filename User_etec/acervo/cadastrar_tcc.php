<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de TCC</title>

    <link rel="stylesheet" href="../../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="./index_acervo.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar TCC</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="nome_escola" class="form-label">Escola:</label>
                <select name="nome_escola" required class="form-select">
                    <option value="">Selecione a escola</option>
                    <?php
                    include '../../conexao.php'; // Conexão externa
                    
                    $sql = "SELECT nome_escola FROM tbescola";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row["nome_escola"]) . '">' . htmlspecialchars($row["nome_escola"]) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhuma escola encontrada</option>';
                    }

                    $conn->close();
                    ?>
                </select>
            </div>

            <div>
                <label for="classe" class="form-label">Classe:</label>
                <select name="classe" required class="form-select">
                    <option value="">Selecione a classe</option>
                    <?php
                    include '../../conexao.php';

                    $sql = "SELECT classe FROM tbclasse";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row["classe"]) . '">' . htmlspecialchars($row["classe"]) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhuma classe encontrada</option>';
                    }

                    $conn->close();
                    ?>
                </select>
            </div>

            <div>
                <label for="titulo" class="form-label">Título do TCC:</label>
                <input type="text" name="titulo" placeholder="Título" required class="form-control">
            </div>

            <div>
                <label for="autor" class="form-label">Autor(es):</label>
                <input type="text" name="autor" placeholder="Autor(es)" required class="form-control">
            </div>

            <div>
                <label for="orientador" class="form-label">Orientador:</label>
                <input type="text" name="orientador" placeholder="Orientador" required class="form-control">
            </div>

            <div>
                <label for="curso" class="form-label">Curso:</label>
                <input type="text" name="curso" placeholder="Curso" required class="form-control">
            </div>

            <div>
                <label for="ano" class="form-label">Ano:</label>
                <input type="number" name="ano" placeholder="Ano" required class="form-control">
            </div>

            <div>
                <label for="estante" class="form-label">Estante:</label>
                <input type="number" name="estante" placeholder="Estante" required class="form-control">
            </div>

            <div>
                <label for="prateleira" class="form-label">Prateleira:</label>
                <input type="number" name="prateleira" placeholder="Prateleira" required class="form-control">
            </div>

            <div>
                <label for="quantidade" class="form-label">Quantidade:</label>
                <input type="number" name="quantidade" placeholder="Quantidade" required class="form-control">
            </div>

            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" name="cadastrar" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

    <?php
    // Função de conexão incluída do arquivo externo
    include '../../conexao.php';

    // Processar o formulário ao enviar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        // Capturar dados do formulário
        $nome_escola = mysqli_real_escape_string($conn, $_POST['nome_escola']);
        $classe = mysqli_real_escape_string($conn, $_POST['classe']);
        $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
        $autor = mysqli_real_escape_string($conn, $_POST['autor']);
        $orientador = mysqli_real_escape_string($conn, $_POST['orientador']);
        $curso = mysqli_real_escape_string($conn, $_POST['curso']);
        $ano = mysqli_real_escape_string($conn, $_POST['ano']);
        $estante = mysqli_real_escape_string($conn, $_POST['estante']);
        $prateleira = mysqli_real_escape_string($conn, $_POST['prateleira']);
        $quantidade = mysqli_real_escape_string($conn, $_POST['quantidade']);

        // Inserir os dados na tabela tbtcc
        $sql = "INSERT INTO tbtcc (nome_escola, classe, titulo, autor, orientador, curso, ano, estante, prateleira, quantidade) 
                VALUES ('$nome_escola', '$classe', '$titulo', '$autor', '$orientador', '$curso', '$ano', '$estante', '$prateleira', '$quantidade')";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='alert alert-success mt-4'>Novo TCC cadastrado com sucesso!</p>";
        } else {
            echo "<p class='alert alert-danger mt-4'>Erro ao cadastrar TCC: " . $conn->error . "</p>";
        }

        // Fechar a conexão
        $conn->close();
    }
    ?>

    <script src="../../src/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
