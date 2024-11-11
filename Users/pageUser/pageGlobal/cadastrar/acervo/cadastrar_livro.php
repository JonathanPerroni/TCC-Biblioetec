<?php
session_start();
include '../../../../../conexao/conexao.php'; // Conexão externa

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
    $classe = filter_input(INPUT_POST, 'classe', FILTER_SANITIZE_STRING);
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $autor = filter_input(INPUT_POST, 'autor', FILTER_SANITIZE_STRING);
    $editora = filter_input(INPUT_POST, 'editora', FILTER_SANITIZE_STRING);
    $ano_publicacao = filter_input(INPUT_POST, 'ano_publicacao', FILTER_SANITIZE_NUMBER_INT);
    $isbn = filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
    $num_paginas = filter_input(INPUT_POST, 'num_paginas', FILTER_SANITIZE_NUMBER_INT);
    $idioma = filter_input(INPUT_POST, 'idioma', FILTER_SANITIZE_STRING);
    $estante = filter_input(INPUT_POST, 'estante', FILTER_SANITIZE_NUMBER_INT);
    $prateleira = filter_input(INPUT_POST, 'prateleira', FILTER_SANITIZE_NUMBER_INT);
    $edicao = filter_input(INPUT_POST, 'edicao', FILTER_SANITIZE_NUMBER_INT);
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT);
    
    $cadastrado_por = $_SESSION['nome']; // Usuário logado que cadastrou o item
    $data_cadastro = date("Y-m-d H:i:s"); // Data e hora do cadastro

  

    try {
        // Iniciar transação
        $conn->begin_transaction();

        // Verificar se já existe um livro com o mesmo ISBN no mesmo código de escola
        $sql_check = "SELECT * FROM tblivros WHERE isbn = ? AND codigo_escola = ?";
        $stmt_check = $conn->prepare($sql_check);
        if (!$stmt_check) {
            throw new Exception("Erro ao preparar a consulta de verificação: " . $conn->error);
        }
        $stmt_check->bind_param("ss", $isbn, $codigo_escola);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $_SESSION["msg"] = "<p class='alert alert-warning mt-4'>Este ISBN já está cadastrado para essa escola.</p>";
            $conn->rollback();
            header("Location: cadastrar_livro.php");
            exit();
        }

        // Inserir os dados na tabela tblivros
        $sql = "INSERT INTO tblivros (codigo_escola, classe, titulo, autor, editora, ano_publicacao, isbn, genero, num_paginas, idioma, estante, prateleira, edicao, quantidade, cadastrado_por, data_cadastro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta de inserção: " . $conn->error);
        }
        $stmt->bind_param("ssssssssssiissss", $codigo_escola, $classe, $titulo, $autor, $editora, $ano_publicacao, $isbn, $genero, $num_paginas, $idioma, $estante, $prateleira, $edicao, $quantidade, $cadastrado_por, $data_cadastro);

        if ($stmt->execute()) {
            // Função para registrar histórico
            function registraHistorico($conn, $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora) {
                $stmt_hist = $conn->prepare("INSERT INTO historico_usuarios (historico_acao, historico_responsavel, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt_hist) {
                    throw new Exception("Erro ao preparar a consulta de histórico: " . $conn->error);
                }
                $stmt_hist->bind_param("sssss", $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
                $stmt_hist->execute();
                $stmt_hist->close();
            }

            // Registrar histórico de cadastro
            registraHistorico($conn, "cadastrar", $_SESSION['nome'], $titulo, $cadastrado_por, $data_cadastro);

            $conn->commit();
            $_SESSION["msg"] = "<p class='alert alert-success mt-4'>Novo livro cadastrado com sucesso!</p>";
            header("Location: cadastrar_livro.php");
            exit();
        } else {
            throw new Exception("Erro ao executar o cadastro: " . $stmt->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION["msg"] = "<p class='alert alert-danger mt-4'>" . $e->getMessage() . "</p>";
        header("Location: cadastrar_livro.php");
        exit();
    } finally {
        $stmt_check->close();
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}

      // Validação de login, só entra se estiver logado
      if (empty($_SESSION['email'])) {
        // echo  $_SESSION['nome'];
        // echo  $_SESSION['acesso'];
        $_SESSION['msg'] = "Faça o Login!!";
        header("Location:  ../../../../../loginDev.php");
        exit();
    }

    // Verifica se há mensagem na sessão
    if (isset($_SESSION['sucesso'])) {
        $sucesso = htmlspecialchars($_SESSION['sucesso'], ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$sucesso');</script>";
        // Limpa a mensagem da sessão
        unset($_SESSION['sucesso']);
    }


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Livro</title>
    <link rel="stylesheet" href="../../../../../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../UserCss/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../../../bibliotecario/pagebibliotecario.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Início</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Livro</a>
    </header>

    <div class="container-sm w-50 my-4 bg-white shadow p-4 rounded-3">
    <p class="text-primary d-flex flex-column">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?></p>
        <form action="" method="POST" class="d-flex flex-column gap-4">
            <div>
                <label for="codigo_escola" class="form-label">Escola:</label>
                <select name="codigo_escola" required class="form-select">
                    <option value="">Selecione a escola</option>
                    <?php
                   
                    
                    // Buscar escolas
                    $sql = "SELECT codigo_escola FROM tbescola";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row["codigo_escola"]) . '">' . htmlspecialchars($row["codigo_escola"]) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhuma escola encontrada</option>';
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="classe" class="form-label">Classe:</label>
                <select name="classe" required class="form-select">
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
            </div>

            <div>
                <label for="titulo" class="form-label">Título:</label>
                <input type="text" name="titulo" placeholder="Título" required class="form-control">
            </div>

            <div>
                <label for="autor" class="form-label">Autor(es):</label>
                <input type="text" name="autor" placeholder="Autor(es)" required class="form-control">
            </div>

            <div>
                <label for="editora" class="form-label">Editora:</label>
                <input type="text" name="editora" placeholder="Editora" required class="form-control">
            </div>

            <div>
                <label for="ano_publicacao" class="form-label">Ano de Publicação:</label>
                <input type="number" name="ano_publicacao" placeholder="Ano de Publicação" required class="form-control">
            </div>

            <div>
                <label for="isbn" class="form-label">ISBN:</label>
                <input type="text" name="isbn" placeholder="ISBN" required class="form-control">
            </div>

            <div>
                <label for="genero" class="form-label">Gênero:</label>
                <input type="text" name="genero" placeholder="Gênero" required class="form-control">
            </div>

            <div>
                <label for="num_paginas" class="form-label">Número de Páginas:</label>
                <input type="number" name="num_paginas" placeholder="Número de Páginas" required class="form-control">
            </div>

            <div>
                <label for="idioma" class="form-label">Idioma:</label>
                <input type="text" name="idioma" placeholder="Idioma" required class="form-control">
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
                <label for="edicao" class="form-label">Edição:</label>
                <input type="number" name="edicao" placeholder="Edição" required class="form-control">
            </div>

            <div>
                <label for="quantidade" class="form-label">Quantidade:</label>
                <input type="number" name="quantidade" placeholder="Quantidade" required class="form-control">
            </div>

            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
            <button type="submit" name="cadastrar" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>




    <script src="../../src/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>