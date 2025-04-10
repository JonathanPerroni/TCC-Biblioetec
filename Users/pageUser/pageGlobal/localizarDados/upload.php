<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlfile'])) {
    // Cria o diretório se não existir
    if (!file_exists('arquivoXML')) {
        mkdir('arquivoXML', 0777, true);
    }

    $file = $_FILES['xmlfile'];
    $fileName = basename($file['name']);
    $targetPath = "arquivoXML/" . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $_SESSION['message'] = "Arquivo enviado com sucesso!";
        header('Location: parser.php?file=' . urlencode($fileName));
        exit;
    } else {
        $_SESSION['error'] = "Erro ao enviar arquivo.";
        header('Location: index.php');
        exit;
    }
}
?>