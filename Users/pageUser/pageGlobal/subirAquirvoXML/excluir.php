<?php
session_start();
$dir = 'xml/';
$files = glob($dir . '*');

foreach ($files as $file) {
    if (is_file($file)) unlink($file);
}

unset($_SESSION['arquivo_enviado']);
$_SESSION['msg'] = "Todos os arquivos foram excluídos com sucesso!";
header("Location: index.php");
exit;
