<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Publicações Periódicas</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Publicação Periódica</h1>        
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

                // Conectar ao banco de dados
                $conn = conectarBanco();

                // Buscar escolas
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
            <input type="text" name="editora" placeholder="Editora" required>
            <label>Data de Publicação: </label>
            <input type="date" name="data_publicacao" required>        
            <input type="text" name="categoria" placeholder="Categoria" required>
            <input type="text" name="issn" placeholder="ISSN" required>
            <input type="number" name="estante" placeholder="Estante" required>
            <input type="number" name="prateleira" placeholder="Prateleira" required>
            <input type="number" name="edicao" placeholder="Edição" required>
            <input type="number" name="quantidade" placeholder="Quantidade" required>

            <button type="reset">Limpar</button>
            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>
    </div>

    <?php
    // Processar o formulário ao enviar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        // Capturar dados do formulário
        $nome_escola = $_POST['nome_escola'];
        $classe = $_POST['classe'];
        $titulo = $_POST['titulo'];
        $editora = $_POST['editora'];
        $data_publicacao = $_POST['data_publicacao'];
        $categoria = $_POST['categoria'];
        $issn = $_POST['issn'];
        $estante = $_POST['estante'];
        $prateleira = $_POST['prateleira'];
        $edicao = $_POST['edicao'];
        $quantidade = $_POST['quantidade'];

        // Inserir os dados na tabela tbjornal_revista
        $sql = "INSERT INTO tbjornal_revista (nome_escola, classe, titulo, editora, data_publicacao, categoria, issn, estante, prateleira, edicao, quantidade) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar e executar a consulta parametrizada
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiiiss", $nome_escola, $classe, $titulo, $editora, $data_publicacao, $categoria, $issn, $estante, $prateleira, $edicao, $quantidade);

        if ($stmt->execute()) {
            echo "Nova publicação cadastrada com sucesso!";
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
    }

    // Fechar a conexão
    $conn->close();
    ?>

</body>
</html>
