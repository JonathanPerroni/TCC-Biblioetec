<?php
session_start();
unset($_SESSION['livros']);
unset($_SESSION['aluno']);
unset($_SESSION['etapa']);   // Adicionado: limpa a etapa atual
header("Location: ../pagebibliotecario.php");
exit;
?>
