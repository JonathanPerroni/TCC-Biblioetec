<?php
session_start();
ob_start();
unset($_SESSION['codigo'],$_SESSION['nome'],$_SESSION['cpf'],$_SESSION['email'],$_SESSION['confirma_email'],$_SESSION['password'],$_SESSION['telefone'],$_SESSION['celular'],$_SESSION['acesso']);
header("Location: login.php")

?>