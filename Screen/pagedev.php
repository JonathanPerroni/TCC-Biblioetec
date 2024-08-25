<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../conexao.php");


//validação de login so entra se logar
if(empty($_SESSION['email'])){
    // echo  $_SESSION['nome'];
     //echo  $_SESSION['acesso'];
     $_SESSION['msg'] = "Faça o Login!!";
     header("Location: ../loginDev.php");
     exit();
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/defaults.css">
    <link rel="stylesheet" href="../style/pageDev.css">
</head>
<body>   
       
       <header>
       <nav class="menu-nav">
            <p class="logoTitle"><h1 id="brand-title">Biblietec</h1></p>
                    <div class="perfil">                
                        <h2 class="perfilName" id="login-title"> <?php echo  $_SESSION['nome']; ?><br><span><?php echo $_SESSION['acesso'];?></span></h2>
                    
                        <div class="perfilFoto">
                        <img src="../img/perfil.jpg" alt="...">
                        </div>               
                    </div>
                    <div class="menuPerfil">
                        <ul>
                            <li><a href="#">
                            <ion-icon name="person-outline"></ion-icon>
                                Pefil
                            </a></li>
                            <li><a href="#">
                            <ion-icon name="help-circle-outline"></ion-icon>
                                Ajuda
                            </a></li>
                            <li><a href="#">
                            <ion-icon name="log-out-outline"></ion-icon>
                                Deslogar
                            </a></li>
                        </ul>
                    </div>
               

                 </nav> 
       </header>
    
 <div class="btnSair">
<a href="logout.php">SAIR</a>
 </div>

    
     <h2>hello world</h2>

     <script>
        const perfil = document.querySelector('.perfil');
        const menuPerfil = document.querySelector('.menuPerfil');

        perfil.onclick = () => {
        menuPerfil.classList.toggle('ativo');
}
     </script>
<!-- areas do icons-->
 
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html> 