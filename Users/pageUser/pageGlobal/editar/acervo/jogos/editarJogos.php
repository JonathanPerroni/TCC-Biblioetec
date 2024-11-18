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
$stmt = $conn->prepare("SELECT * FROM tbjogoseducativos WHERE codigo = ?");
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
    <title>Editar Jogos</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../../UserCss/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../../../list/acervo/listajogosNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="" class="nav-link fs-3 fw-medium text-primary">Editar Jogos  </a>
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
        <form action="attJogos.php" method="POST" class="d-flex flex-column gap-4">
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
            <label for="categoria" class="form-label">Categoria:</label>
            <input type="text" name="categoria" placeholder="Categoria" required class="form-control" value="<?php echo htmlspecialchars($row['categoria']); ?>">
        </div>
    </div>

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="idade_minima" class="form-label">Idade Mínima:</label>
            <input type="number" name="idade_minima" placeholder="Idade Mínima" required class="form-control" value="<?php echo htmlspecialchars($row['idade_minima']); ?>">
        </div>
        <div class="w-100">
            <label for="num_jogadores" class="form-label">Número de Jogadores:</label>
            <input type="number" name="num_jogadores" placeholder="Número de Jogadores" required class="form-control" value="<?php echo htmlspecialchars($row['num_jogadores']); ?>">
        </div>
        <div class="w-100">
            <label for="fabricante" class="form-label">Fabricante:</label>
            <input type="text" name="fabricante" placeholder="Fabricante" required class="form-control" value="<?php echo htmlspecialchars($row['fabricante']); ?>">
        </div>
    </div>

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="data_edicao" class="form-label">Data de Edição:</label>
            <input type="date" name="data_edicao" required class="form-control" value="<?php echo htmlspecialchars($row['data_edicao']); ?>">
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
