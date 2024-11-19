
<?php
session_start();

ob_start(); // Inicia o buffer de saída
include("../../../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $codigo = $conn->real_escape_string($codigo);

    $sql = "SELECT * FROM tblivros WHERE codigo = $codigo";
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
    header("Location:  ../../../../../../../login/login.php");
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
    <title>Cadastro de Aluno</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../../UserCss/defaults.css">
   
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../../../list/acervo/listalivroNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="" class="nav-link fs-3 fw-medium text-primary">Editar Cadastro</a>
    </header>
    <div class="container-sm my-4 bg-white shadow p-4 rounded-3 w-50">
        <p class="text-primary">   <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?>
            </p>

    <form action="attLivro.php" method="POST" class=" d-flex flex-column gap-4 ">
    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['codigo']); ?>">

    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
       
       <div class="w-100 " >
            <label for="titulo" class="form-label">Titulo do livro:</label>
            <input type="text" name="titulo" placeholder="Dados do livro - titulo do livro" required class="form-control" value="<?php echo htmlspecialchars($row['titulo']);?>">
       </div>
       <div class="w-100">
            <label for="isbn" class="form-label">Nº ISBN:</label>
            <input type="text" name="isbn" placeholder="Dados do livro - numero ISBN" required class="form-control" value="<?php echo htmlspecialchars($row['isbn']);?>">
        </div>
       
        <div class="w-100">
           <label for="autor" class="form-label">Autor:</label>
           <input type="text" name="autor" placeholder="Dados do livro - Nome do autor" required class="form-control" value="<?php echo htmlspecialchars($row['autor']);?>">
        </div>

      
      
   </div>

   <div class="breakable-row d-flex flex-wrap justify-between gap-4">
   <div class="w-100">
            <label for="editora" class="form-label">Editora:</label>
            <input type="text" name="editora" placeholder="Dados do livro - Nome da editora" required class="form-control" value="<?php echo htmlspecialchars($row['editora']);?>">
        </div>
       
        <div class="w-100">
            <label for="ano_publicacao" class="form-label">Ano de Publicação:</label>
            <input type="text" name="ano_publicacao" placeholder="Dados do livro - Ano de publicacao" required class="form-control" value="<?php echo htmlspecialchars($row['ano_publicacao']);?>">
        </div>

        <div class="w-100">
            <label for="quantidade" class="form-label">Quantidade:</label>
            <input type="text" name="quantidade" placeholder="Insira situação" required class="form-control" value="<?php echo htmlspecialchars($row['quantidade']);?>">
        </div>

        
   </div>
   <div class="breakable-row d-flex  flex-wrap justify-between gap-4">
        <div class="w-100">
             <label for="classe" class="form-label">Classe:</label>
             <input type="text" name="classe" placeholder="Dadis do livro - Classe" required class="form-control" value="<?php echo htmlspecialchars($row['classe']);?>">
        </div>
       <div class="w-100">
           <label for="genero" class="form-label">Genero:</label>
           <input type="text" name="genero" placeholder="Dados do livro - Genero" required class="form-control" value="<?php echo htmlspecialchars($row['genero']);?>">
        </div>
        <div class="w-100">
           <label for="edicao" class="form-label">Edição:</label>
           <input type="text" name="edicao" placeholder="Dados do livro - edicao" required class="form-control" value="<?php echo htmlspecialchars($row['edicao']);?>">
        </div>
       
       
   </div>        
   <div class="breakable-row d-flex flex-wrap justify-between gap-4">
        <div class="w-100">
            <label for="num_paginas" class="form-label">Numero de  Paginas:</label>
            <input type="text" name="num_paginas" placeholder="Dados do livro - Numero de paginas" required class="form-control" value="<?php echo htmlspecialchars($row['num_paginas']);?>">
        </div>
       
        <div class="w-100">
            <label for="idioma" class="form-label">Idioma:</label>
            <input type="text" name="idioma" placeholder="Dados do livro - Idiomas" required class="form-control" value="<?php echo htmlspecialchars($row['idioma']);?>">
        </div>
       
    </div>         
    
    <div class="breakable-row d-flex flex-wrap justify-between gap-4">
    <div class="w-100">
            <label for="estante" class="form-label">Estante:</label>
            <input type="text" name="estante" placeholder="Dados do livro - Estante " required class="form-control" value="<?php echo htmlspecialchars($row['estante']);?>">
        </div>
        <div class="w-100">
            <label for="prateleira" class="form-label">Prateleira:</label>
            <input type="text" name="prateleira" placeholder="Dados do livro - Prateleira" required class="form-control" value="<?php echo htmlspecialchars($row['prateleira']);?>">
        </div>
        
        
    </div>         

    <button type="submit" class="btn btn-primary">Atualizar</button>
  
</form>
    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzc"> </script>

    </body>
