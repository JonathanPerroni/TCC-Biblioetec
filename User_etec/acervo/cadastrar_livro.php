<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Livro</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Livro</h1>        
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
            ?>
        </select>

        <select name="classe" required>
            <option value="">Selecione a classe</option>
            <?php
            // Buscar classes
            $sql = "SELECT classe FROM tbclasse";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row["classe"]) . '">' . htmlspecialchars($row["classe"]) . '</option>';
                }
            } else {
                echo '<option value="">Nenhuma classe encontrada</option>';
            }
            ?>
        </select>

        <input type="text" name="titulo" placeholder="Título" required>
        <input type="text" name="autor" placeholder="Autor(es)" required>
        <input type="text" name="editora" placeholder="Editora" required>
        <input type="number" name="ano_publicacao" placeholder="Ano de Publicação" required>
        <input type="text" name="isbn" placeholder="ISBN" required>
        <input type="text" name="genero" placeholder="Gênero" required>
        <input type="number" name="num_paginas" placeholder="Nº Páginas" required>
        <input type="text" name="idioma" placeholder="Idioma" required>
        <input type="number" name="estante" placeholder="Estante" required>
        <input type="number" name="prateleira" placeholder="Prateleira" required>
        <input type="number" name="edicao" placeholder="Edição" required>
        <input type="number" name="quantidade" placeholder="Quantidade" required>

        <button type="reset">Limpar</button>
        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        $nome_escola = $_POST['nome_escola'];
        $classe = $_POST['classe'];
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $editora = $_POST['editora'];
        $ano_publicacao = $_POST['ano_publicacao'];
        $isbn = $_POST['isbn'];
        $genero = $_POST['genero'];
        $num_paginas = $_POST['num_paginas'];
        $idioma = $_POST['idioma'];
        $estante = $_POST['estante'];
        $prateleira = $_POST['prateleira'];
        $edicao = $_POST['edicao'];
        $quantidade = $_POST['quantidade'];

        $conn = conectarBanco();
        
        // Usar prepared statements para evitar SQL injection
        $stmt = $conn->prepare("INSERT INTO tblivros (nome_escola, classe, titulo, autor, editora, ano_publicacao, isbn, genero, num_paginas, idioma, estante, prateleira, edicao, quantidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssiiss", $nome_escola, $classe, $titulo, $autor, $editora, $ano_publicacao, $isbn, $genero, $num_paginas, $idioma, $estante, $prateleira, $edicao, $quantidade);

        if ($stmt->execute()) {
            echo "Novo livro cadastrado com sucesso!";
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>

</body>
</html>
