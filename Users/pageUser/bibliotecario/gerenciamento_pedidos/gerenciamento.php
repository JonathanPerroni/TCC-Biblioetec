<?php
session_start();
ob_start();
date_default_timezone_set('America/Sao_Paulo');

include_once("../../../../conexao/conexao.php");
include_once('../seguranca.php'); // já verifica login e carrega CSRF
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>     
    <title>Gerenciamento de Pedidos</title>
    <style>
        /* styles.css */
        #tabs {
            display: flex;
            border-bottom: 1px solid #ccc;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        #tabs li {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-bottom: none;
            background: #f1f1f1;
            margin-right: 5px;
        }

        #tabs li.active {
            background: #b30000;
            color: white;
            font-weight: bold;
        }

        .tab-content {
            border: 1px solid #ccc;
            padding: 20px;
        }
    </style>
</head>
<body>

    <a href="../pagebibliotecario.php"
   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
    Página Principal
</a>
    <ul id="tabs">
        <li class="tab-button active" data-tab="areaEmprestimo/lista_emprestimo">Empréstimos</li>
        <li class="tab-button" data-tab="areaDevolucao/lista_devolucao">Devolução</li>
        <li class="tab-button" data-tab="lista_espera">Lista de Espera</li>
        <li class="tab-button" data-tab="pedidos.php">Pedidos</li>
        <li class="tab-button" data-tab="separacao.php">Separação</li>
        <li class="tab-button" data-tab="">Recusa</li>
    </ul>

    <div id="tab-content" class="conteudo-tab">
        <!-- conteúdo será mostrado aqui -->
    </div>

    <script src="./js/tabs.js"></script>
</body>
</html>
