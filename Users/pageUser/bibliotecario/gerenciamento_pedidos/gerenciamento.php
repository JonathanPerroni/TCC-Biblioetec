<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../../../../conexao/conexao.php");
include_once('../seguranca.php');// já verifica login e carrega CSRF

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>     
    <title>Document</title>
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

    <ul id="tabs">
        <li class="tab-button active" data-tab="lista_emprestimo">EMPRESTIMO</li>
        <li class="tab-button" data-tab="devolucao">Devolução</li>
        <li class="tab-button" data-tab="lista_espera">Lista de Espera</li>
        <li data-tab="pedidos.php">PEDIDOS</li>
        <li data-tab="separacao.php">SEPARAÇÃO</li>
        <li data-tab="">DEVOLUÇÃO</li>
    </ul>

    <div id="tab-content" class="conteudo-tab">
        <!-- conteudo sera mostrado aqui -->
    </div>

    <script src="./js/tabs.js"></script>
</body>
</html>