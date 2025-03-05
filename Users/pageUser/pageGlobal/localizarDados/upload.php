<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlfile'])) {
    $file = $_FILES['xmlfile']['tmp_name'];
    if (move_uploaded_file($file, 'uploaded.xml')) {
        header('Location: parser.php');
        exit;
    } else {
        echo "Erro ao enviar arquivo.";
    }
}
?>