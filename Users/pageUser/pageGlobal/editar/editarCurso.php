<?php
session_start();

ob_start(); // Inicia o buffer de saída
include("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $codigo = $conn->real_escape_string($codigo);

    $sql = "SELECT * FROM tbcursos WHERE codigo = $codigo";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Registro não encontrado!";
        exit;
    }
} else {
    echo "ID não fornecido!";
    exit;
}


// Validação de login, só entra se estiver logado
if (empty($_SESSION['email'])) {
    // echo  $_SESSION['nome'];
    // echo  $_SESSION['acesso'];
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location:  ../../../../loginDev.php");
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
    <title>Dados Escola</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../UserCss/defaults.css">
    <link rel="stylesheet" href="../../DevCss/cadastrar_admin.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../list/listacursoNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="" class="nav-link fs-3 fw-medium text-primary">Dados Escola</a>
    </header>
    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
 <p class="text-primary">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?></p>

    <form action="attCurso.php" method="POST" class="d-flex flex-column gap-4">
    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['codigo']); ?>">

    <div class="breakable-row d-flex justify-between gap-4">
            <div class="w-100">
                <label for="nome_curso" class="form-label">Nome do Curso:</label>
                <input type="text" name="nome_curso" placeholder="nome da escola" required class="form-control" maxlength="14" value="<?php echo htmlspecialchars($row['nome_curso']);?>">
            </div>
        
            <div class="w-100">
                <label for="tempo_curso" class="form-label">Tempo do Curso:</label>
                <input type="text" name="tempo_curso" placeholder="tempo_curso" required class="form-control"  value="<?php echo htmlspecialchars($row['tempo_curso']); ?>">
            </div>
    </div>

    <button type="submit" class="btn btn-primary">Atualizar</button>
  
</form>
    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzc"> </script>
        
    </body>