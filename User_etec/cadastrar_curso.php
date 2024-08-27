<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Curso</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Curso</h1>        
        <a href="index.php">Início</a>
        <br><br>
    </div>  

    <div class="form-cadastro">
        <form action="" method="POST">
             <select name="nome_escola" required>
                <option value="">Selecione a escola</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "bdescola");
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }

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
            <input type="text" name="nome_curso" placeholder="Nome do Curso" required>
            <select name="tempo_curso" required>
                <option value="">Selecione tempo do curso</option>
                <option value="1 semestre">1 semestre</option>
                <option value="2 semestres">2 semestres</option>
                <option value="3 semestres">3 semestres</option>
                <option value="4 semestres">4 semestres</option>
                <option value="5 semestres">5 semestres</option>
                <option value="6 semestres">6 semestres</option>
                <option value="7 semestres">7 semestres</option>
                <option value="8 semestres">8 semestres</option>                
            </select>            

            <button type="reset">Limpar</button>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
    
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $nome_curso = filter_input(INPUT_POST, 'nome_curso', FILTER_SANITIZE_STRING);
        $tempo_curso = filter_input(INPUT_POST, 'tempo_curso', FILTER_SANITIZE_STRING);

        $conn = new mysqli("localhost", "root", "", "bdescola");

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Usando prepared statements para evitar SQL Injection
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
</body>
</html>
