<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../conexao.php");

// Validação de login, só entra se estiver logado
if (empty($_SESSION['email'])) {
    // echo  $_SESSION['nome'];
    // echo  $_SESSION['acesso'];
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
    <div class="sidebar">
            <ul>
                <li class="list active">
                    <a href="pagedev.php">
                    <span class="icon-sidebar"><ion-icon name="home-outline"></ion-icon></span>
                    <span class="title-navegation">Inicio</span>
                    </a>
                </li>
                <li class="list">
                    <a href="#">
                    <span class="icon-sidebar"><ion-icon name="glasses-outline"></ion-icon></span>
                    <span class="title-navegation">Lista ADM</span>
                    </a>
                </li>
                <li class="list">
                    <a href="#">
                    <span class="icon-sidebar"><ion-icon name="business-outline"></ion-icon></span>
                    <span class="title-navegation">Lista Escola</span>
                    </a>
                </li>
                <li class="list">
                    <a href="../../User_etec/cadastrar_escola.php">
                    <span class="icon-sidebar"><ion-icon name="save-outline"></ion-icon></span>
                    <span class="title-navegation">Cadastro</span>
                    </a>
                </li>
                <li class="list">
                    <a href="#">
                    <span class="icon-sidebar"><ion-icon name="reader-outline"></ion-icon></span>
                    <span class="title-navegation">Relatorio</span>
                    </a>
                </li>
                <li class="list">
                    <a href="#">
                    <span class="icon-sidebar"><ion-icon name="code-working-outline"></ion-icon></span>
                    <span class="title-navegation">Devs</span>
                    </a>
                </li>
                
            </ul>
    </div>
        <nav class="menu-nav">
        <div class="logo">
                <p class="logoTitle">
                    <h1 id="brand-title">BiblioEtec</h1>
                </p>
            </div>

            <div class="perfil">
                <h2 class="perfilName" id="login-title">
                    <?php echo $_SESSION['nome']; ?><br>
                    <span><?php echo $_SESSION['acesso']; ?></span>
                </h2>
                <div class="perfilFoto">
                    <img src="../img/perfil12.jpg" alt="...">
                </div>
            </div>

            <div class="menuPerfil">
                <ul>
                    <li><a href="#">
                        <ion-icon name="person-outline"></ion-icon>
                        Perfil
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

    <div class="content" id="content">
        <h1>menu responsivo - jhow zitos</h1>
    </div>

    <div class="btnSair">
        <a href="logout.php">SAIR</a>
    </div>

    <h2>hello world</h2>

    <script>
        //js do menu perfil
        const perfil = document.querySelector('.perfil');
        const menuPerfil = document.querySelector('.menuPerfil');

        perfil.onclick = () => {
            menuPerfil.classList.toggle('ativo');
        }
   
    </script>
    <script>
           //js do menu de opção
           const list = document.querySelectorAll('.list');
        function activeLink(){
            list.forEach((item) =>
            item.classList.remove('active'));
            this.classList.add('active');
         
        }
        list.forEach((item)=> 
        item.addEventListener('click', activeLink));
    </script>

    <!-- Áreas dos ícones -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
