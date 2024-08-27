<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Jogo Educativo</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Jogo Educativo</h1>        
        <a href="../index.php">Início</a>
        <br><br>
    </div>

    <div class="form-cadastro">
        <form action="" method="POST">
            <select name="nome_escola" required>
                <option value="">Selecione a escola</option>
                <?php
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

                // Conectar e buscar escolas
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
            <input type="text" name="categoria" placeholder="Categoria" required>
            <input type="number" name="idade_minima" placeholder="Idade Mínima" required>
            <input type="number" name="num_jogadores" placeholder="Número de Jogadores" required>
            <input type="text" name="fabricante" placeholder="Fabricante" required>
            <input type="number" name="estante" placeholder="Estante" required>
            <input type="number" name="prateleira" placeholder="Prateleira" required>
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
        $categoria = $_POST['categoria'];
        $idade_minima = $_POST['idade_minima'];
        $num_jogadores = $_POST['num_jogadores'];
        $fabricante = $_POST['fabricante'];
        $estante = $_POST['estante'];
        $prateleira = $_POST['prateleira'];
        $quantidade = $_POST['quantidade'];

        $conn = conectarBanco();

        // Corrigir a vinculação do tipo de dados do fabricante para string
        $stmt = $conn->prepare("INSERT INTO tbjogoseducativos (nome_escola, classe, titulo, categoria, idade_minima, num_jogadores, fabricante, estante, prateleira, quantidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nome_escola, $classe, $titulo, $categoria, $idade_minima, $num_jogadores, $fabricante, $estante, $prateleira, $quantidade);

        if ($stmt->execute()) {
            echo "Novo jogo educativo cadastrado com sucesso!";
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>

</body>
</html>
