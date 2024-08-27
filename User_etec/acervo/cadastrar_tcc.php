<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de TCC</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar TCC</h1>        
        <a href="../index.php">Início</a>
        <br><br>
    </div>

    <div class="form-cadastro">
        <form action="" method="POST">
            <select name="nome_escola" required>
                <option value="">Selecione a escola</option>
                <?php
                // Função para conectar ao banco de dados
                function conectarBanco() {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "bdescola";
                    
                    // Criar a conexão
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    
                    // Verificar a conexão
                    if ($conn->connect_error) {
                        die("Conexão falhou: " . $conn->connect_error);
                    }
                    return $conn;
                }

                // Buscar escolas
                $conn = conectarBanco();
                $sql = "SELECT nome_escola FROM tbescola";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["nome_escola"]) . '">' . htmlspecialchars($row["nome_escola"]) . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhuma escola encontrada</option>';
                }

                // Fechar a conexão
                $conn->close();
                ?>
            </select>

            <select name="classe" required>
                <option value="">Selecione a classe</option>
                <?php
                // Reabrir conexão para buscar classes
                $conn = conectarBanco();
                $sql = "SELECT classe FROM tbclasse";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["classe"]) . '">' . htmlspecialchars($row["classe"]) . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhuma classe encontrada</option>';
                }

                // Fechar a conexão
                $conn->close();
                ?>
            </select>

            <input type="text" name="titulo" placeholder="Título" required>
            <input type="text" name="autor" placeholder="Autor(es)" required>
            <input type="text" name="orientador" placeholder="Orientador" required>
            <input type="text" name="curso" placeholder="Curso" required>
            <input type="number" name="ano" placeholder="Ano" required>
            <input type="number" name="estante" placeholder="Estante" required>
            <input type="number" name="prateleira" placeholder="Prateleira" required>
            <input type="number" name="quantidade" placeholder="Quantidade" required>

            <button type="reset">Limpar</button>
            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>
    </div>

    <?php
    // Processar o formulário ao enviar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        // Capturar dados do formulário
        $conn = conectarBanco();
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
            echo "Novo TCC cadastrado com sucesso!";
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }

        // Fechar a conexão
        $conn->close();
    }
    ?>

</body>
</html>
