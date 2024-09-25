<?php
$servidor = "localhost";
$username = "root";
$bdpassword = "";
$bdname = "bdescola";

// validação da conexão
$conn = new mysqli($servidor, $username, $bdpassword, $bdname, 3306);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}