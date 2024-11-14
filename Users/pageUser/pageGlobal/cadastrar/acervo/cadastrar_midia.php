<?php
session_start();
include_once("../../../../../conexao/conexao.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
    $classe = filter_input(INPUT_POST, 'classe', FILTER_SANITIZE_STRING);
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $data_lancamento = filter_input(INPUT_POST, 'data_lancamento', FILTER_SANITIZE_STRING);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
    $diretor_artista = filter_input(INPUT_POST, 'diretor_artista', FILTER_SANITIZE_STRING);
    $duracao = filter_input(INPUT_POST, 'duracao', FILTER_SANITIZE_STRING);
    $estante = filter_input(INPUT_POST, 'estante', FILTER_SANITIZE_NUMBER_INT);
    $prateleira = filter_input(INPUT_POST, 'prateleira', FILTER_SANITIZE_NUMBER_INT);
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT);
    $cadastrado_por = $_SESSION['nome'];
    $data_cadastro = date("Y-m-d H:i:s");

    try {
        $conn->begin_transaction();

        $sql = "INSERT INTO tbmidias (codigo_escola, classe, titulo, data_lancamento, genero, diretor_artista, duracao, estante, prateleira, quantidade, cadastrado_por, data_cadastro)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta de inserção: " . $conn->error);
        }

        $stmt->bind_param("sssssssssiis", $codigo_escola, $classe, $titulo, $data_lancamento, $genero, $diretor_artista, $duracao, $estante, $prateleira, $quantidade, $cadastrado_por, $data_cadastro);

        if ($stmt->execute()) {
            function registraHistorico($conn, $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora) {
                $stmt_hist = $conn->prepare("INSERT INTO historico_usuarios (historico_acao, historico_responsavel, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt_hist) {
                    throw new Exception("Erro ao preparar a consulta de histórico: " . $conn->error);
                }
                $stmt_hist->bind_param("sssss", $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
                $stmt_hist->execute();
                $stmt_hist->close();
            }

            registraHistorico($conn, "cadastrar", $_SESSION['nome'], $titulo, "Midia", $data_cadastro);
            
            $conn->commit();
            $_SESSION["msg"] = "<p class='alert alert-success mt-4'>Nova mídia cadastrada com sucesso!</p>";
            header("Location: cadastrar_midia.php");
            exit();
        } else {
            throw new Exception("Erro ao executar o cadastro: " . $stmt->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION["msg"] = "<p class='alert alert-danger mt-4'>" . $e->getMessage() . "</p>";
        header("Location: cadastrar_midia.php");
        exit();
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}

if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location:  ../../../../../loginDev.php");
    exit();
}

if (isset($_SESSION['sucesso'])) {
    $sucesso = htmlspecialchars($_SESSION['sucesso'], ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$sucesso');</script>";
    unset($_SESSION['sucesso']);
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Mídia</title>
    <link rel="stylesheet" href="../../../../../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../UserCss/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">

    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
          
        <!-- ira verificar qual  usuario esta logado para voltar para pagina especifica do tipo do acesso  -->
        <?php
                                        

                                        // Verifica o tipo de acesso do usuário
                                        $acesso = $_SESSION['acesso'] ?? ''; // Define o valor de acesso na sessão, caso não exista

                                        // Define o link de redirecionamento com base no tipo de acesso
                                        switch ($acesso) {
                                            case 'administrador':
                                                $pagina_inicial = "../../../admin/pageAdmin.php";
                                                break;
                                            case 'bibliotecario':
                                                $pagina_inicial = "../../../bibliotecario/pageBibliotecario.php";
                                                break;
                                             default:
                                                // Redireciona para uma página padrão, caso o acesso não seja identificado
                                                $pagina_inicial = "../../../../login/login.php";
                                                break;
                                        }
             ?>

        
        <a href="<?php echo $pagina_inicial; ?>" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                <span class="fw-medium">Início</span>
            </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Mídia</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
    <?php
            // Exibe a mensagem de sucesso ou erro armazenada na sessão
            if (isset($_SESSION["msg"])) {
                echo $_SESSION["msg"];
                unset($_SESSION["msg"]); // Limpa a mensagem da sessão após exibir
            }
            ?>
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
                <label for="data_lancamento" class="form-label">Data de Lançamento:</label>
                <input type="date" name="data_lancamento" required class="form-control">
            </div>

            <div>
                <label for="genero" class="form-label">Gênero:</label>
                <input type="text" name="genero" placeholder="Gênero" required class="form-control">
            </div>

            <div>
                <label for="diretor_artista" class="form-label">Diretor/Artista:</label>
                <input type="text" name="diretor_artista" placeholder="Diretor/Artista" required class="form-control">
            </div>

            <div>
                <label for="duracao" class="form-label">Duração:</label>
                <input type="text" name="duracao" placeholder="Duração" required class="form-control">
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
