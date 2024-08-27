<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Mídia</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Mídia</h1>        
        <a href="../index.php">Início</a>
        <br><br>
    </div>

    <div class="form-cadastro">
        <form action="" method="POST">
            <select name="nome_escola" required>
                <option value="">Selecione a escola</option>
                <?php
                // Conectar ao banco de dados
                $conn = new mysqli("localhost", "root", "", "bdescola");
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }

                // Buscar escolas
                $sql = "SELECT nome_escola FROM tbescola";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["nome_escola"] . '">' . $row["nome_escola"] . '</option>';
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
                        echo '<option value="' . $row["classe"] . '">' . $row["classe"] . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhuma classe encontrada</option>';
                }
                ?>
            </select>

            <input type="text" name="titulo" placeholder="Título" required>            
            <label>Data de Lançamento: </label>
            <input type="date" name="data_lancamento" required>        
            <input type="text" name="genero" placeholder="Gênero" required>
            <input type="text" name="diretor_artista" placeholder="Diretor/Artista" required>
            <input type="text" name="duracao" placeholder="Duração" required>
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
        $nome_escola = $_POST['nome_escola'];
        $classe = $_POST['classe'];
        $titulo = $_POST['titulo'];
        $data_lancamento = $_POST['data_lancamento'];
        $genero = $_POST['genero'];
        $diretor_artista = $_POST['diretor_artista'];
        $duracao = $_POST['duracao'];
        $estante = $_POST['estante'];
        $prateleira = $_POST['prateleira'];
        $quantidade = $_POST['quantidade'];

        // Inserir os dados na tabela tbmidias
        $sql = "INSERT INTO tbmidias (nome_escola, classe, titulo, data_lancamento, genero, diretor_artista, duracao, estante, prateleira, quantidade) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Preparar a declaração
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $nome_escola, $classe, $titulo, $data_lancamento, $genero, $diretor_artista, $duracao, $estante, $prateleira, $quantidade);

        // Executar a declaração
        if ($stmt->execute()) {
            echo "Nova mídia cadastrada com sucesso!";
        } else {
            echo "Erro ao cadastrar mídia: " . $stmt->error;
        }

        // Fechar o statement
        $stmt->close();
    }

    // Fechar a conexão
    $conn->close();
    ?>

</body>
</html>
