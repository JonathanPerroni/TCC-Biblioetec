<?php
    session_start();
    include '../../../../../conexao/conexao.php'; // Conexão externa

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $classe = filter_input(INPUT_POST, 'classe', FILTER_SANITIZE_STRING);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);
        $idade_minima = filter_input(INPUT_POST, 'idade_minima', FILTER_SANITIZE_NUMBER_INT);
        $num_jogadores = filter_input(INPUT_POST, 'num_jogadores', FILTER_SANITIZE_NUMBER_INT);
        $fabricante = filter_input(INPUT_POST, 'fabricante', FILTER_SANITIZE_STRING);
        $estante = filter_input(INPUT_POST, 'estante', FILTER_SANITIZE_NUMBER_INT);
        $prateleira = filter_input(INPUT_POST, 'prateleira', FILTER_SANITIZE_NUMBER_INT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT);

        $cadastrado_por = $_SESSION['nome']; // Usuário logado que cadastrou o item
        $data_cadastro = date("Y-m-d H:i:s"); // Data e hora do cadastro

         // Iniciar transação
         $conn->begin_transaction();

        try {
            $conn->begin_transaction();
    
            $sql = "INSERT INTO tbjogoseducativos (codigo_escola, classe, titulo, categoria, idade_minima, num_jogadores, fabricante, estante, prateleira, quantidade, cadastrado_por, data_cadastro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erro ao preparar a consulta de inserção: " . $conn->error);
            }
    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiissssiss", $codigo_escola, $classe, $titulo, $categoria, $idade_minima, $num_jogadores, $fabricante, $estante, $prateleira, $quantidade, $cadastrado_por, $data_cadastro);


            
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
    
                registraHistorico($conn, "cadastrar", $_SESSION['nome'], $titulo, "Jogo", $data_cadastro);
                
                $conn->commit();
                $_SESSION["msg"] = "<p class='alert alert-success mt-4'>Nova mídia cadastrada com sucesso!</p>";
                header("Location: cadastrar_jogos.php");
                exit();
            } else {
                throw new Exception("Erro ao executar o cadastro: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION["msg"] = "<p class='alert alert-danger mt-4'>" . $e->getMessage() . "</p>";
            header("Location: cadastrar_jogos.php");
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
        header("Location:  ../../../../../../loginDev.php");
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
    <title>Cadastro de Jogo Educativo</title>
  
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
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Cadastrar Jogo Educativo</a>
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
            </div>

            <div>
                <label for="titulo" class="form-label">Título:</label>
                <input type="text" name="titulo" placeholder="Título" required class="form-control">
            </div>

            <div>
                <label for="categoria" class="form-label">Categoria:</label>
                <input type="text" name="categoria" placeholder="Categoria" required class="form-control">
            </div>

            <div>
                <label for="idade_minima" class="form-label">Idade Mínima:</label>
                <input type="number" name="idade_minima" placeholder="Idade Mínima" required class="form-control">
            </div>

            <div>
                <label for="num_jogadores" class="form-label">Número de Jogadores:</label>
                <input type="number" name="num_jogadores" placeholder="Número de Jogadores" required class="form-control">
            </div>

            <div>
                <label for="fabricante" class="form-label">Fabricante:</label>
                <input type="text" name="fabricante" placeholder="Fabricante" required class="form-control">
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

   

    <script src="../../src/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5j7p2Ak7e4BEm9vNT3d4mDa3dFic01d7U2Twk8lJQ" crossorigin="anonymous"></script>
</body>
</html>
