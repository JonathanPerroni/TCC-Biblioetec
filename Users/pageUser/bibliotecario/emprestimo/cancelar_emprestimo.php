<?php
session_start();
unset($_SESSION['livros']); // Limpa os livros
unset($_SESSION['aluno']);  // Limpa o aluno
header("Location: ../pagebibliotecario.php"); // Redireciona para a página principal
exit;
?>
