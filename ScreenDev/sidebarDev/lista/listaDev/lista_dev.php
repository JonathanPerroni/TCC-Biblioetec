<?php
session_start();
include_once("../../../../conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Definir o charset para UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Dev</title>
    <link rel="stylesheet" href="../../../../style/defaults.css">
    <link rel="stylesheet" href=" lista_devStyle.css">
</head>

<body>
<header>
    <div class="sidebar">
        <ul>
            <li class="list active">
                <a href="../../../pagedev.php">
                    <span class="icon-sidebar"><ion-icon name="home-outline"></ion-icon></span>
                    <span class="title-navegation">Inicio</span>
                </a>
            </li>

            <li class="list dropdown">
                <a href="#" class="dropdown-toggle">
                    <span class="icon-sidebar"><ion-icon name="business-outline"></ion-icon></span>
                    <span class="title-navegation">Listas</span>
                    <span class="icon-sidebar setamenor"><ion-icon name="chevron-down-outline"></ion-icon></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="sidebarDev/lista/listaDev/lista_dev.php">Lista Dev</a></li>
                    <li><a href="sidebarDev/cadastro/cadAdmin/cadastrar_admin.php">Lista Admin</a></li>
                    <li><a href="sidebarDev/cadastro/cadEscola/cadastrar_escola.php">Lista Escola</a></li>
                </ul>
            </li>

            <li class="list dropdown">
                <a href="#" class="dropdown-toggle">
                    <span class="icon-sidebar"><ion-icon name="save-outline"></ion-icon></span>
                    <span class="title-navegation">Cadastro</span>
                    <span class="icon-sidebar setamenor"><ion-icon name="chevron-down-outline"></ion-icon></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="sidebarDev/cadastro/cadDev/cadastrar_dev.php">Cadastrar Dev</a></li>
                    <li><a href="sidebarDev/cadastro/cadAdmin/cadastrar_admin.php">Cadastrar Admin</a></li>
                    <li><a href="sidebarDev/cadastro/cadEscola/cadastrar_escola.php">Cadastrar Escola</a></li>
                </ul>
            </li>

            <li class="list">
                <a href="#">
                    <span class="icon-sidebar"><ion-icon name="reader-outline"></ion-icon></span>
                    <span class="title-navegation">Relatorio</span>
                </a>
            </li>
        </ul>
    </div>
    <nav class="menu-nav">
        <div class="logo Lleft">
            <p class="logoTitle">
                <h1 id="brand-title">Biblio<span class="destaque">Etec</span></h1>
            </p>
        </div>
        <div class="logo Lright">
            <p class="logoTitle">
                <h1 id="brand-title">Lista<span class="destaque">Dev</span></h1>
            </p>
        </div>
    </nav>
</header>
<main class="container">
    <section class="container-table">
        <table class="user-dev"  border="1">
            <thead>
            <tr class="trGeral">
                    
                    <th class="codigoNumerico">#</th>
                    <th class="codigoStatus">Status</th>
                    <th class="espacoCurto">Nome</th>
                    <th class="espacoCurto">Email</th>
                    <th  class="espacoCurto">Acesso</th>
                    <th class="areaAcao">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Puxar os dados da tabela
            $sqlRegistro = mysqli_query($conn, "SELECT codigo, statusDev, nome, email, acesso FROM tbdev ORDER BY codigo ASC");

            // Verifica se há resultados na consulta
            if ($sqlRegistro && mysqli_num_rows($sqlRegistro) > 0) {
                // Loop para exibir cada registro
             $linha = 1;
                
                while ($dadosDev = mysqli_fetch_assoc($sqlRegistro)) {
                    $codigo = htmlspecialchars($dadosDev["codigo"]);
                    $statusDev = htmlspecialchars($dadosDev["statusDev"]);
                    $nome = htmlspecialchars($dadosDev["nome"], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($dadosDev["email"]);
                    $acesso = htmlspecialchars($dadosDev["acesso"], ENT_QUOTES, 'UTF-8');

                    // verifica o status e define o botão correspondente
                    $statusDevText = ($statusDev == 1) ? 'Ativado' : 'Bloqueado';
                    $botaoDevText = ($statusDev == 1) ? 'On' : "Off";
                    $novaAcaoDev = ($statusDev == 1) ? 0 : 1;


                    ?>
                    <tr class="trGeral">                         
                        <td class="codigoNumerico"><?php echo $linha++; ?></td>
                        <td class="codigoStatus"><?php echo $statusDevText; ?></td>
                        <td class="espacoCurto"><?php echo $nome; ?></td>
                        <td class="espacoCurto"><?php echo $email; ?></td>
                        <td class="espacoCurto"><?php echo $acesso; ?></td>
                        
                        <td class="areaBtnAcao"> 
                            <div> <!-- Botão "Ação"  que o display dele fica visivel quando chega 768px-->
                                    <button class="dropdown-btn">Ação</button>
                                    
                                    <!-- Dropdown -->
                                    <ul class="dropdown-menu">
                                        <li class="btnEdit1">
                                            <div class="btn-acao1 ">
                                                <span>                                                    
                                                
                                                <a href="editar-dev.php?codigo=<?php echo $codigo; ?>" class="btn-text">Editar</a>
                                                </span>
                                            </div>
                                        </li>
                                        <li class="btnDelet1">
                                            <div class="btn-acao1 ">
                                                <span>
                                                <a href="excluir-dev.php?codigo=<?php echo $codigo; ?>" class="btn-text">Excluir</a>
                                                </span>
                                            </div>
                                        </li>
                                        <li class="btnOnOff1">  
                                            <div class="btn-acao1 "> 
                                                <span >
                                                 <a href="ativar-desativar-dev.php?codigo=<?= $codigo; ?>&acao=<?= $novaAcaoDev; ?>" ><?= $botaoDevText; ?>
                                                </a>
                                                </span>
                                            </div>

                                        </li>
                                      </ul>
                            </div>            


                             <!-- area do botao antes de chegar a 768px para telas maiores  -->
                            <div class="btn-acao"><span class="btnEdit"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA8ElEQVR4nL2SsUpDQRBFR2Ms0jxh5t6bV9qmsfIbTCNYKlqks0pjZWeTQhAM2IiiSRUQ1C8QtLSx84fCxie8Ku4D9cLC7oHDzM6uWW56vfUgzwN8c3JSFMWGNZP1DGiQjgB2ArppJAf46eReRVcCfM2XyQMzawd07dJukCcOnebJ0n6NrlWdTM1sdZnedurJyaM6dPI4oJmZtf5cPvx32Zx8DOqizgANcwZmZVl2AnpPlYI8aySnANhy8irtHXxYvHeubGaWPkpAL07eB/nh0G22/FVVIwB9SZs/DuvX49Blan9xhW53u2Ljb7ZsOXg3BzzqPsompsDpAAAAAElFTkSuQmCC"></span> <a href="editar-dev.php?codigo=<?php echo $codigo; ?>" class="btn-text">Editar </a></div>
                            <div class="btn-acao"><span class="btnDelet"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAXElEQVR4nGNgoAUQERNrExEV+ykiJv4fRAuLireQZoCo2E8+GRkhEBtEC4uK/SDCVvH/xGK8hjAQsISgKxiIoEcNGNyBKIpIgeiAqBQJSvPwPICOIXmiGa8BAwIAxCVRq6ihQTkAAAAASUVORK5CYII=" alt="Delete Icon"></span><a href="excluir-dev.php?codigo=<?php echo $codigo; ?>" class="btn-text">Excluir</a></div>                         
                            <div class="btn-acao">
                                <a href="ativar-desativar-dev.php?codigo=<?php echo $codigo; ?>&acao=<?php echo $novaAcaoDev; ?>" class="btn-link">
                                    <span class="btn-icon <?php echo ($statusDev == 1) ? 'btn-on' : 'btn-off'; ?>">
                                    <?php if ($statusDev == 1): ?>
                                        <!-- Ícone para 'On' -->
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAACXBIWXMAAAsTAAALEwEAmpwYAAABk0lEQVR4nO3cUUoDMRSF4UCfy6ncO0nbBxXBlbqUrsB16EN3IO5DUIQWVKQoaHPa/B/Mezp/wqRhmFIAAAAAAAAAAAAAAAAAdDRfryOndt9au+45DpRSpMuLrPUxa3vNqT7XWm+4MQ4x9hdRjGIQxTAGUY7/AI/atgdj7K6Y6tNiubw68hDHoZ+sjI9BatuuVqvsPe6zJGL4IIYRYhghhhFiGCGGEWIYEVtbHyKGDxHDh4jhQ8TwIWL4EDF8iBg+RAwfIoYPEcOHiOFDxPAhYvjQ4DFmMbW79/eVyom9N5W7GC5j/wuznNrGZZZp9JWxj+HwA0WMzzF6RhExvo/RI4qIcTjGMaNo8BhF0sJlBzMffDf1dVY+9JyVGn1lOEURMXyiiBg+UUQMnygihk8UEcMniojhE0XE8IkiYvhEETF8omTmLf/A/9Fvz5uytpcc/WzKbaUkZ1OnFSXO/aDwlKIEMXyiBDF8ogQxfKIEMXyiBDF8ogQxfKIEMXyiBDGsvg694TgEAAAAAAAAAAAAAFD6egMmATxOOQjdywAAAABJRU5ErkJggg==" alt="On Icon">
                                    <?php else: ?>
                                        <!-- Ícone para 'Off' -->
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEHUlEQVR4nO1aTa8UVRCtIGii8obpqjrV8yS4eStcsNEl/AfRiMYQtorRnRtRF6JAQoLu1AUxxhAVg7JxpVFciODX8qlxpRHRqAGMRvMS0NS8menbPQ3TPdMfs5iT3GQyPX3rnq66datOD9ECCyzQGOI4VtX4flZ7noF3GDgjwJc+/DMDJ/2aqt7X6/WE5gmdTmcLqz0minMCuyaw/wqOa6L2GWu8v9vtdlolIMAhgV0psfjrjcvuqW7ThBh4mNV+yV2U4kdROyHAAVbdG6nuFrM9IvYEqx0U2ClfeN69DLsowEO1E1DV20XtjbEFKH4T4EjX7K6CU20UkZ2i9jIr/s0h9bqZ3VbbRhbgi4zBP1ntyVmMMvMyqx0T2FrGs5+7zTpIfJM2ZB9EUXRHVTaY47sF+DZDZrUyMv1wGvMEDhPRBqolA+LXDJnzvV7v1pknz+4JDyWqA9u33yxqp/OTAF6bOTvleKIREpz1jNmDU80dRdESw34OXPwREd1UOQmiTaL2XsYD7y9t3RqFe8bTvYde6dkHh90oO1W5sW8YTmqn/Xu/zHF8T5jN/NCc4tRODq1a9sUEEkOw2kthBVDq9O/XTklI/e6ZixoIp5WVlVuyPxSRHiv+SR6qPlrYyqAAHG7wI214IoSovRr89iwVP/ySKrZE2VELCYdIvCu452qhFsD7iSCsfqCWSQywKdyz3s/QJHhmCAydoPZJ9CFq7wbJ5yBNgnd2wf44QHNAInscMPA2TQIDnwQZYi/NAQmH9zMBkTM0CQz7enhDBNxLDaTYIhBvzpJ5vipFRMQemAcSjn6nWY5IElqq9ji1GE4hIo33lQwtnJy6tqmJhEOAZ4PK+C2ahIFAMFzIKZoDEg5WvBk84Oeo1IEIu+xCQdskHAL7aZSEVHfTJPjx72VAsuFlZ9skVHVHWKJsXl7mQje6Ahgs6pU2STgEdjTZ6PYpTVPGu+50naaq8hSbB28h1nWzkY1HqCi8eck0Vsfa8IRDgKcDO5dKy6qp4hG25rpT0yS6vd42VvxVKlvliQ8CuxDE5vd9QaCBcBpgg6h9GNi/OLXI7YJyatFj4lk9nnCw2ospW9OXS+twQTk1YQMkBHgq4/XjM0/qArULylnPdDrbulQ9No55QnGuEsl01McrVtNPyb5z3akSA0S0JY7vXH9VlyKxWvkruv6JrzifCbE1151mEe+YebOnWFb8neOJet4zuotdUM7ul/7LGpdsJN5VqDZLyo6jovhjbD7geGXhdCO4oJzShdPjiqdoVnuh356a7XHVY72fwDODKnaU1jPjwszZqSw8pw9K/tz3gSXHJT/soihaapTEOKF4vyuAqap58rjqBaBLoK2+ns6Db07vFdxTLtmI4uPhHwb8s3/n1/w3hUvxBRZYgKrA/7lkldodIPk0AAAAAElFTkSuQmCC" alt="Off Icon">
                                    <?php endif; ?>
                                    </span>
                                    <span class="btn-text"><?php echo $botaoDevText; ?></span>
                                </a>
                            </div>
                            



                        </td>
                    </tr>
                    <?php
                } 

            } else {
                echo "<tr><td colspan='6'>Nenhum desenvolvedor encontrado.</td></tr>";
            }
 // Fechar a conexão
 mysqli_close($conn);
 ?>
        
            </tbody>
        </table>
    </section>
</main>

<!-- area do sidebar-->
<script>
    // JS para o dropdown na sidebar
    document.addEventListener('DOMContentLoaded', function () {
        const dropdowns = document.querySelectorAll('.dropdown');
        const sidebar = document.querySelector('.sidebar');

        // Alternar a visibilidade do dropdown ao clicar
        dropdowns.forEach(dropdown => {
            dropdown.querySelector('.dropdown-toggle').addEventListener('click', function (e) {
                e.preventDefault();
                const menu = dropdown.querySelector('.dropdown-menu');
                const isActive = dropdown.classList.contains('active');
                // Alternar a classe 'active' no dropdown
                dropdowns.forEach(d => d.classList.remove('active')); // Fecha todos os dropdowns
                if (!isActive) {
                    dropdown.classList.add('active'); // Abre o dropdown clicado
                }
            });
        });

        // Fechar dropdown quando clicar fora
        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target)) {
                dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
            }
        });

        // Fechar dropdown quando a sidebar for fechada
        sidebar.addEventListener('mouseleave', function () {
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        });
    });

    // JS do menu de opção
    const list = document.querySelectorAll('.list');
    function activeLink() {
        list.forEach((item) =>
            item.classList.remove('active'));
        this.classList.add('active');
    }
    list.forEach((item) =>
        item.addEventListener('click', activeLink));


//botao que esconder outros botoes 
document.addEventListener('DOMContentLoaded', function() {
    var btnAcao = document.getElementById('btn-acao');
    var dropdownAcao = document.querySelector('.dropdown-acao');

    btnAcao.addEventListener('click', function() {
        dropdownAcao.classList.toggle('active');
    });
});


</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownButtons = document.querySelectorAll('.dropdown-btn');

    dropdownButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Encontra o dropdown-menu associado
            const dropdownMenu = this.nextElementSibling;

            // Alterna a visibilidade do dropdown
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            } else {
                dropdownMenu.classList.add('show');
            }
        });
    });

    // Fecha o dropdown se clicar fora
    document.addEventListener('click', function (event) {
        if (!event.target.matches('.dropdown-btn')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
});
</script>

<!-- Áreas dos ícones -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
