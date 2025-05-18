<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === 0) {
    $ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
    $nome = md5(basename($_FILES['arquivo']['name']) . time()) . '.' . $ext;
    $destino = 'xml/' . $nome;

    if (!is_dir('xml')) mkdir('xml', 0777, true);

    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
        $_SESSION['arquivo_enviado'] = $nome;
        $_SESSION['msg'] = "Arquivo enviado com sucesso.";
    } else {
        $_SESSION['msg'] = "Erro ao enviar o arquivo.";
    }
}
header("Location: index.php");
exit;
