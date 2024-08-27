<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
</head>
<body>
    <div class="header">
        <h1>Cadastrar Aluno</h1>
        <a href="index.php">Início</a>
        <br><br>
    </div>

    <div class="form-cadastro">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <label>Data de Nascimento: </label>
            <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>
            <input type="text" name="endereco" placeholder="Endereço" required>
            <input type="text" name="cidade" placeholder="Cidade" required>
            <input type="text" name="estado" placeholder="Estado" required>
            <input type="text" name="cpf" placeholder="CPF" required>
            <input type="text" name="celular" placeholder="Celular" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="responsavel" placeholder="Nome do responsável" required>            
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="senha2" placeholder="Confirmar Senha" required>
            
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

            <select name="tipo_ensino" required>
                <option value="">Selecione o tipo de ensino</option>
                <option value="medio">Médio</option>
                <option value="tecnico">Técnico</option>
                <option value="integrado">Integrado</option>
            </select>

            <select name="periodo" required>
                <option value="">Selecione um período</option>
                <option value="manha">Manhã</option>
                <option value="tarde">Tarde</option>
                <option value="noite">Noite</option>
                <option value="integral">Integral</option>
            </select>

            <select name="nome_curso" required>
                <option value="">Selecione o curso</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "bdescola");
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }

                $sql = "SELECT nome_curso FROM tbcursos";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["nome_curso"] . '">' . $row["nome_curso"] . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhum curso encontrado</option>';
                }

                $conn->close();
                ?>
            </select>

            <select name="situacao" required>
                <option value="">Selecione a situação</option>
                <option value="a cursar">A cursar</option>
                <option value="cursando">Cursando</option>
                <option value="desistente">Desistente</option>
                <option value="matricula trancada">Matrícula Trancada</option>
            </select>

            <select name="acesso" required>
                <option value="">Tipo de Acesso</option>
                <option value="aluno">Aluno</option>
            </select>

            <button type="reset">Limpar</button>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $data_nascimento = $_POST['data_nascimento']; // Não precisa de sanitização para data
        $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
        $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $responsavel = filter_input(INPUT_POST, 'responsavel', FILTER_SANITIZE_STRING);
        $senha = $_POST['senha'];  // Senha não precisa de sanitização
        $senha2 = $_POST['senha2'];
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $tipo_ensino = $_POST['tipo_ensino'];
        $periodo = $_POST['periodo'];
        $nome_curso = filter_input(INPUT_POST, 'nome_curso', FILTER_SANITIZE_STRING);
        $situacao = $_POST['situacao'];
        $acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p>Email inválido.</p>";
            exit();
        }

        $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);


        if ($senha !== $senha2) {
            echo "<p>As senhas não coincidem.</p>";
            exit();
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $conn = new mysqli("localhost", "root", "", "bdescola");

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO tbalunos (nome, data_nascimento, endereco, cidade, estado, cpf, celular, email, responsavel, senha, nome_escola, tipo_ensino, periodo, nome_curso, situacao, acesso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssss", $nome, $data_nascimento, $endereco, $cidade, $estado, $cpf, $celular, $email, $responsavel, $senhaHash, $nome_escola, $tipo_ensino, $periodo, $nome_curso, $situacao, $acesso);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar o aluno: " . $conn->error . "</p>";
        }

        $conn->close();
    }
    ?>
</body>
</html>
