<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Curso</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="../src/bootstrap/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="index.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Curso</a>
    </header>
    
    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="nome_escola" class="form-label">Escola:</label>
                <select name="nome_escola" required class="form-select">
                    <option value="">Selecione a escola</option>
                    <?php
                    include '../conexao_testes.php';        
                    
                    $sql = "SELECT nome_escola FROM tbescola";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["nome_escola"] . '">' . $row["nome_escola"] . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhuma escola encontrada</option>';
                    }

                    $conn->close();
                    ?>
                </select>
            </div>

            <div>
                <label for="nome_curso" class="form-label">Nome do Curso:</label>
                <input type="text" name="nome_curso" placeholder="Insira o nome do curso" required class="form-control">
            </div>

            <div>
                <label for="tempo_curso" class="form-label">Tempo do Curso:</label>
                <select name="tempo_curso" required class="form-select">
                    <option value="">Selecione o tempo do curso</option>
                    <option value="1 semestre">1 semestre</option>
                    <option value="2 semestres">2 semestres</option>
                    <option value="3 semestres">3 semestres</option>
                    <option value="4 semestres">4 semestres</option>
                    <option value="5 semestres">5 semestres</option>
                    <option value="6 semestres">6 semestres</option>
                    <option value="7 semestres">7 semestres</option>
                    <option value="8 semestres">8 semestres</option>
                </select>
            </div>

            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $nome_curso = filter_input(INPUT_POST, 'nome_curso', FILTER_SANITIZE_STRING);
        $tempo_curso = filter_input(INPUT_POST, 'tempo_curso', FILTER_SANITIZE_STRING);

        $conn = new mysqli("localhost", "root", "", "bdescola");

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO tbcursos (nome_escola, nome_curso, tempo_curso) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome_escola, $nome_curso, $tempo_curso);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar o curso.</p>";
        }

        $conn->close();
    }
    ?>

    <script src="../src/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
