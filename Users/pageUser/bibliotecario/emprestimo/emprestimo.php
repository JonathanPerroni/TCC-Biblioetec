<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../../../../conexao/conexao.php");

// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: ../../login/login.php");
    exit();
}

//verifica se  esta criada a sessão para controlar a etapas
if(!isset($_SESSION['etapa'])){

    // Criar a sessçai oara armazenar a etapa
    $_SESSION['etapa'] = 1;
}

$_SESSION['etapa'] = 2;




?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Admin</title>

    <link rel="stylesheet" href="../../../UserCss/defaults.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>


   <main class="mx-1 sm:mx-16 my-8">
       <?php
       if($_SESSION['etapa'] == 1){
         
        //incluir o formulario da pagina formulario_pesquisa_aluno_livro.php
        include_once './formulario_pesquisa_aluno.php';

       }elseif($_SESSION['etapa'] == 2){
              //incluir o formulario da pagina formulario_confirmação_pesquisa.php
              include_once './formulario_pesquisa_livro.php';
              
       }

       
       ?>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  
</body>
</html>
