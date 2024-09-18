<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>

    <link rel="stylesheet" href="../src/output.css">
    <link href="../src/bootstrap/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-itens-center">
<header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
            <a href="index.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                <span class="fw-medium">Início</span>
            </a>
            <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Aluno</a>
        </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-flex flex-column gap-4">
        <div>
                <label for="nome" class="form-label">Nome Completo:</label>
                <input type="text" name="nome" placeholder="Insira o nome completo" required class="form-control">
            </div>
            <div>
                <label for="data_nascimento" class="form-label">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" placeholder="Insira a data de nascimento" required class="form-control">
            </div>
            <div>
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" name="endereco" placeholder="Insira o endereço" required class="form-control">
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
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" name="cpf" placeholder="Insira o CPF" required class="form-control">
            </div>
            <div>
                <label for="celular" class="form-label">Celular:</label>
                <input type="text" name="celular" placeholder="Insira o número de celular" required class="form-control">
            </div>
            <div>
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" placeholder="Insira o email" required class="form-control">
            </div>
            <div>
                <label for="responsavel" class="form-label">Nome do responsável:</label>
                <input type="text" name="responsavel" placeholder="Insira o nome do responsável" required class="form-control">
            </div>
            <div>
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" placeholder="Insira o senha" required class="form-control">
            </div>
            <div>
                <label for="senha2" class="form-label">Confirmar Senha:</label>
                <input type="password" name="senha2" placeholder="Confirme a Senha" required class="form-control">
            </div>
            
            <div>
                <label for="nome_escola" class="form-label">Selecione a ETEC:</label>
                <select name="nome_escola" required class="form-select">
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
            </div>

            <div>
                <label for="tipo_ensino" class="form-label">Selecione o tipo de ensino:</label>
                <select name="tipo_ensino" required class="form-select">
                    <option value=""></option>
                    <option value="medio">Médio</option>
                    <option value="tecnico">Técnico</option>
                    <option value="integrado">Integrado</option>
                </select>
            </div>

            <div>
                <label for="periodo" class="form-label">Selecione o período:</label>
                <select name="periodo" required class="form-select">
                    <option value=""></option>
                    <option value="manha">Manhã</option>
                    <option value="tarde">Tarde</option>
                    <option value="noite">Noite</option>
                    <option value="integral">Integral</option>
                </select>
            </div>

            <div>
                <label for="nome_curso" class="form-label">Selecione o curso:</label>
                <select name="nome_curso" required class="form-select">
                    <option value=""></option>
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
            </div>

            <div>
                <label for="situacao" class="form-label">Selecione a situação:</label>
                <select name="situacao" required class="form-select">
                    <option value=""></option>
                    <option value="a cursar">A cursar</option>
                    <option value="cursando">Cursando</option>
                    <option value="desistente">Desistente</option>
                    <option value="matricula trancada">Matrícula Trancada</option>
                </select>
            </div>

            <div>
                <label for="acesso" class="form-label">Confirme o tipo de acesso:</label>
                <select name="acesso" required class="form-select">
                    <option value="">Tipo de Acesso</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>

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
