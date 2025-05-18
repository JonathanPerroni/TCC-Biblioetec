<?php
session_start();
include_once("../../../../conexao/conexao.php");
include_once("funcoes.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['arquivo_enviado'])) {
    $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];
    $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
    $data_cadastro = date('Y-m-d H:i:s');

    if (!file_exists($arquivo)) {
        $_SESSION['msg'] = "Arquivo não encontrado.";
        header("Location: index.php");
        exit;
    }

    try {
        $linhas = processarXML($arquivo, $conn, $cadastrado_por, $data_cadastro);
        $_SESSION['msg'] = "Linhas processadas: $linhas";
    } catch (Exception $e) {
        $_SESSION['msg'] = "Erro: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
