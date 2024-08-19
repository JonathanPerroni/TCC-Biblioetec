<?php
session_start();
unset($_SESSION['chave_recuperar_senha'],$_SESSION['email'],$_SESSION['nome'],$_SESSION['data_codigo_autenticada']);
header("Location: ../loginDev.php")

?>