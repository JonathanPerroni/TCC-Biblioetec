<?php
session_start();
include_once("../conexao.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header class="containerHeader">
        <div class="user-login">
            <?php
            if(!empty($_SESSION['email'])){
                echo "Ola " .$_SESSION['nome']. ", Bem Vindo";
            }else{
                $_SESSION['msg'] = "Faça o Login!!";
                header("Location: ../loginDev.php");
                exit();
            }
            ?>

            <div class="btnSair">
                <a href="logout.php">SAIR</a>
            </div>
        </div>


    </header>
     <h2>hello world</h2>
</body>
</html>