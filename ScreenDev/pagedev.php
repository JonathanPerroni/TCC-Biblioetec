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
    <style>
                    body {
                    display: flex;
                    align-items: center;
                    max-height: 100vh;
                    flex-wrap: wrap;
                    position: relative; /* Adicionado para permitir o uso do pseudo-elemento e desfoque */
                
                }

                :root {
                    --primary-emphasis: #B20000;
                    --secondary-emphasis: #3A5461;
                    --dark-grey: #273336;
                    --off-black: #141617;
                    --off-white: #EFEFEF;
                    --Gainsboro: #DCDCDC;
                    --all-white: #f5f5f5;
                }

                header {
                    max-width: 90rem;
                    width: 100%;
                    height: 5em;
                    gap: 10px;
                    display: flex;
                }

                .sidebar {
                    position: absolute; /* Modificado de 'relative' para 'absolute' para que o sidebar não afete a posição do conteúdo ao expandir */
                    width: 70px;
                    height: 100vh;
                    box-shadow: 10px 0 0 var(--primary-emphasis);
                    display: flex;
                    background-color: var(--off-black);
                    border-left: 10px solid var(--dark-grey);
                    overflow-x: hidden;
                    transition: width 1.5s; /* Modificado para animar apenas a largura */
                    z-index: 2; /* Certifica-se de que o sidebar fique acima do conteúdo */
                }

                .sidebar:hover {
                    width: 205px; /* Expande o sidebar ao passar o mouse */
                    
                }




                .sidebar ul {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    padding-left: 5px;
                    padding-top: 40px;
                }

                .sidebar ul li {
                    position: relative;
                    list-style: none;
                    width: 100%;
                    border-top-left-radius: 20px;
                    border-bottom-left-radius: 20px;
                }

                .sidebar ul li a {
                    position: relative;
                    display: flex;
                    width: 100%;
                    text-decoration: none;
                    color: var(--off-white);
                }

                .sidebar ul li a .icon-sidebar {
                    position: relative;
                    display: block;
                    min-width: 60px;
                    height: 60px;
                    text-align: center;
                    line-height: 70px;
                }

                .sidebar ul li a .icon-sidebar ion-icon {
                    position: relative;
                    font-size: 1.5rem;
                    z-index: 1;
                }

                .sidebar ul li a .title-navegation {
                    position: relative;
                    display: block;
                    padding-left: 10px;
                    height: 60px;
                    line-height: 60px;
                    white-space: nowrap;
                }

                .sidebar ul li.active {
                    background: var(--primary-emphasis);
                }

                .sidebar ul li.active a::before {
                    content: '';
                    position: absolute;
                    top: -30px;
                    right: 0;
                    width: 30px;
                    height: 30px;
                    background: var(--off-black);
                    border-radius: 50%;
                    box-shadow: 15px 15px 0 var(--primary-emphasis);
                }

                .sidebar ul li.active a::after {
                    content: '';
                    position: absolute;
                    bottom: -30px;
                    right: 0;
                    width: 30px;
                    height: 30px;
                    background: var(--off-black);
                    border-radius: 50%;
                    box-shadow: 15px -15px 0 var(--primary-emphasis);
                }

                .menu-nav {    
                    position: relative;
                    width: 100%;
                    background-color: var(--off-white);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px;
                    box-shadow: 1px 1px 4px rgba(0, 0, 0, .8);    
                    padding-left: 6%;z-index: 1;
                }


                .menu-nav .logo {    
                    
                    max-width: 17%;
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-left: 5px;
                    transition: transform 1.5s;
                }

                .sidebar:hover ~ .menu-nav .logo {
                    transform: translateX(135px);
                }
                .menu-nav .destaque{
                    color: var(--off-black);
                }

                #brand-title {
                    color: var(--primary-emphasis);
                    font-weight: bold;
                    font-size: clamp(18px, 4vw, 42px);
                    letter-spacing: .1rem;
                    
                }

                .menu-nav .perfil {
                    
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                    cursor: pointer;
                    user-select: none;
                    border: 1px solid;
                    border-top-left-radius: 0.625em;
                    border-bottom-left-radius: 0.625em;
                    border-top-right-radius: 1.5625em;
                    border-bottom-right-radius: 1.5625em;
                    height: 70%;
                    max-width: 20%;
                    width: 100%;
                }

                .menu-nav .perfil h2 {
                    text-align: end;
                    line-height: 1rem;
                    margin-right: 5px;
                    width: 100%;
                    padding: 0 2px 0 0;
                
                }

                #login-title {    
                    color: var(--off-black);
                    font-size: clamp(10px, 1.5vw, 20px);
                    font-weight: bold;
                    width: 100%;

                }

                .menu-nav .perfil h2 span {  
                    font-size: clamp(8px, 1.2vw, 12px);
                    color: var(--primary-emphasis);
                    font-weight: bold;
                }

                .menu-nav .perfil .perfilFoto {
                    position: relative;
                    width: 55px;
                    height: 55px;
                    border: 1px solid;
                    border-radius: 50%;
                    overflow: hidden;
                }

                .menu-nav .perfil .perfilFoto img {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    top: 0;
                    left: 0;
                    object-fit: cover;
                }

                .menu-nav .menuPerfil {
                    position: absolute;
                    top: calc(100% + 30px);
                    right: 0;
                    width: 200px;
                    min-height: 100px;
                    background-color: var(--all-white);
                    box-shadow: 0 50px 50px rgba(0, 0, 0, .2);
                    user-select: none;
                    opacity: 0;
                    visibility: hidden;
                    transition: .3s;
                    
                }

                .menu-nav .menuPerfil.ativo {
                    opacity: 1;
                    visibility: visible;
                }

                .menu-nav .menuPerfil::before {
                    content: '';
                    position: absolute;
                    top: -8px;
                    right: 35px;
                    width: 20px;
                    height: 20px;
                    background-color: var(--all-white);
                    z-index: -1;
                    transform: rotate(45deg);
                }

                .menu-nav .menuPerfil ul {
                    display: flex;
                    flex-direction: column;
                    background-color: var(--all-white);
                    list-style: none;
                }

                .menu-nav .menuPerfil ul li {
                    padding: 15px 20px;
                    background-color: var(--all-white);
                    transition: .5s;
                }

                .menu-nav .menuPerfil ul li:hover {
                    background-color: var(--off-white);
                    transition: 0s;
                }

                .menu-nav .menuPerfil ul li a {
                    text-decoration: none;
                    color: var(--secondary-emphasis);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .menu-nav .menuPerfil ul li a ion-icon {
                    font-size: 1.25rem;
                    color: var(--dark-grey);
                }


                @media screen and (max-width: 1024px) {
                    .menu-nav .perfil{
                        height: 70%;
                        max-width: 100%;
                        width: 230px;
                    
                    }
                    .logoTitle{
                        margin-left: 100px;
                    }

                    .sidebar:hover ~ .menu-nav .logo {
                        transform: translateX(135px);
                    }
                }


                @media screen and (max-width: 768px) {
                    .menu-nav .perfil{
                        height: 70%;
                        max-width: 100%;
                        width: 170px;
                        
                    }

                }

                @media screen and (max-width: 608px) {
                
                    .menu-nav .perfil{
                        height: 70%;
                        max-width: 100%;
                        width: 170px;
                    }

                    .perfil h2 span{
                        font-size: 10px;
                    }

                    .logoTitle{
                        margin-left: 125px;
                    }
                    .sidebar:hover ~ .menu-nav .logo {
                        transform: translateX(135px);
                    }

                }

                @media screen and (max-width: 523px) {
                
                    .menu-nav .perfil{
                        height: 70%;
                        max-width: 100%;
                        width: 160px;
                    }
                    .menu-nav .menuPerfil {
                        width: 150px;
                    
                    }

                }

                @media screen and (width < 456px) {



                    .menu-nav .perfil{
                        height: 70%;
                        max-width: 100%;
                        width: 160px;
                    }
                    .logoTitle{
                        margin-left: 140px;
                    }
                
                    .sidebar:hover ~ .menu-nav .logo {
                        transform: translateX(-100px);
                        
                    }

                
                }
    </style>

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
                    <a href="#">
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
                    <h1 id="brand-title">Biblio<span class="destaque" >Etec</span></h1>
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

    <main class="maincontainer">
    <div class="content" id="content">
        <h1>menu responsivo - jhow zitos</h1>
    </div>

    <div class="btnSair">
        <a href="logout.php">SAIR</a>
    </div>

    <h2>hello world</h2>

    </main>



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
