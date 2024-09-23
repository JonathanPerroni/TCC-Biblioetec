<?php
$servidor = "localhost";
$username = "root";
$bdpassword = "root";
$bdname = "bdescola";
$port = 3306;

// validação da conexão
$conn = new mysqli($servidor, $username, $bdpassword, $bdname, $port);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}