<?php
session_start();

ob_start(); // Inicia o buffer de saída
include("../../../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');



$codigo = (int) $_GET['codigo']; // Converte para inteiro

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta segura com prepared statement
$stmt = $conn->prepare("SELECT * FROM tbmidias WHERE codigo = ?");
$stmt->bind_param("i", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Registro não encontrado!");
}

// Validação de login
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location:  ../../../../../../../login/login.php");
    exit();
}

// Mensagem de sucesso
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
    <title>Editar Jornal/Revista</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../../UserCss/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../../../list/acervo/listamidiaNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="" class="nav-link fs-3 fw-medium text-primary">Editar Midia </a>
    </header>
    <div class="container-sm my-4 bg-white shadow p-4 rounded-3 w-50">
        <p class="text-primary">
            <?php
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>
        </p>
        <form action="attMidia.php" method="POST" class="d-flex flex-column gap-4">
    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['codigo']); ?>">

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="classe" class="form-label">Classe:</label>
            <input type="text" name="classe" placeholder="Classe" required class="form-control" value="<?php echo htmlspecialchars($row['classe']); ?>">
        </div>
        <div class="w-100">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" name="titulo" placeholder="Título" required class="form-control" value="<?php echo htmlspecialchars($row['titulo']); ?>">
        </div>
        <div class="w-100">
            <label for="data_lancamento" class="form-label">Data de Lançamento:</label>
            <input type="date" name="data_lancamento" required class="form-control" value="<?php echo htmlspecialchars($row['data_lancamento']); ?>">
        </div>
    </div>

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="genero" class="form-label">Gênero:</label>
            <input type="text" name="genero" placeholder="Gênero" required class="form-control" value="<?php echo htmlspecialchars($row['genero']); ?>">
        </div>
        <div class="w-100">
            <label for="diretor_artista" class="form-label">Diretor/Artista:</label>
            <input type="text" name="diretor_artista" placeholder="Diretor ou Artista" required class="form-control" value="<?php echo htmlspecialchars($row['diretor_artista']); ?>">
        </div>
        <div class="w-100">
            <label for="duracao" class="form-label">Duração:</label>
            <input type="text" name="duracao" placeholder="Duração" required class="form-control" value="<?php echo htmlspecialchars($row['duracao']); ?>">
        </div>
    </div>

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="data_adicao" class="form-label">Data de Adição:</label>
            <input type="date" name="data_adicao" required class="form-control" value="<?php echo htmlspecialchars($row['data_adicao']); ?>">
        </div>
        <div class="w-100">
            <label for="estante" class="form-label">Estante:</label>
            <input type="text" name="estante" placeholder="Estante" required class="form-control" value="<?php echo htmlspecialchars($row['estante']); ?>">
        </div>
        <div class="w-100">
            <label for="prateleira" class="form-label">Prateleira:</label>
            <input type="text" name="prateleira" placeholder="Prateleira" required class="form-control" value="<?php echo htmlspecialchars($row['prateleira']); ?>">
        </div>
    </div>

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="quantidade" class="form-label">Quantidade:</label>
            <input type="number" name="quantidade" placeholder="Quantidade" required class="form-control" value="<?php echo htmlspecialchars($row['quantidade']); ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Atualizar</button>
</form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
