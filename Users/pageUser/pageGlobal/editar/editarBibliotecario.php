<?php
session_start();

ob_start(); // Inicia o buffer de saída
include("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $codigo = $conn->real_escape_string($codigo);

    $sql = "SELECT * FROM tbbibliotecario WHERE codigo = $codigo";
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
    header("Location:  ../../../../../login/login.php");
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
    <title>Cadastro de Bibliotecario</title>
    <link rel="stylesheet" href="../../../../../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../UserCss/defaults.css">
   
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../list/listabibliotecarioNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="" class="nav-link fs-3 fw-medium text-primary">Editar Cadastro</a>
    </header>
    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
 <p class="text-primary">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?></p>

    <form action="attBibliotecario.php" method="POST" class="d-flex flex-column gap-4">
    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['codigo']); ?>">

    <div>
        <label for="nome" class="form-label">Nome completo:</label>
        <input type="text" name="nome" placeholder="Insira o nome completo" required class="form-control" value="<?php echo htmlspecialchars($row['nome']);?>">
    </div>

    <div>
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" placeholder="Insira o email" required class="form-control" value="<?php echo htmlspecialchars($row['email']);?>">
    </div>

    <div>
        <label for="password" class="form-label">Senha:</label>
        <?php
            $senha_visivel = htmlspecialchars($row['password']);
        ?>
        <input type="password" name="password" placeholder="Insira a senha" required class="form-control"  value="<?php echo $senha_visivel; ?>">
    </div>

    <div>
        <label for="password2" class="form-label">Confirme a Senha:</label>
        <input type="password" name="password2" placeholder="Confirme a senha" required class="form-control"  value="<?php echo $senha_visivel; ?>">
    </div>

    <div class="breakable-row d-flex justify-between gap-4">
        <div class="w-100">
            <label for="telefone" class="form-label">Telefone:</label>
            <input type="tel" name="telefone" placeholder="Insira o número de telefone" class="form-control" value="<?php echo htmlspecialchars($row['telefone']);?>">
        </div>
        <div class="w-100">
            <label for="celular" class="form-label">Celular:</label>
            <input type="tel" name="celular" placeholder="Insira o número de celular" required class="form-control" value="<?php echo htmlspecialchars($row['celular']);?>">
        </div>
    </div>

    <div class="breakable-row d-flex justify-between gap-4">
        <div class="w-100">
            <label for="cpf" class="form-label">CPF:</label>
            <input type="text" name="cpf" placeholder="Insira o CPF" required class="form-control" value="<?php echo htmlspecialchars($row['cpf']);?>">
        </div>
        <div class="w-50">
            <label for="codigo_escola" class="form-label">Código da ETEC:</label>
            <input type="text" name="codigo_escola" placeholder="Insira o código da ETEC" required class="form-control" value="<?php echo htmlspecialchars($row['codigo_escola']);?>">
        </div>
    </div>
    <div class="breakable-row d-flex justify-between gap-4">
        <div class="w-50">
            <label for="acesso" class="form-label">Confirme o acesso:</label>
            <select name="acesso" id="acesso" required class="form-select">
                <option value="bibliotecario"><?php echo htmlspecialchars($row['acesso']);?></option>
            </select>  
        </div>
        <div class="w-50">
            <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" class="form-control " onchange="this.form.submit()">
                    <option value="1" <?php echo ($row['status'] == '1') ? 'selected' : ''; ?>>Acesso Ativo</option>
                    <option value="0" <?php echo ($row['status'] == '0') ? 'selected' : ''; ?>>Acesso Bloqueado</option>
                </select> 
        </div>
    </div>

   

   
    <button type="submit" class="btn btn-primary">Atualizar</button>
  
</form>
    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzc"> </script>

    </body>