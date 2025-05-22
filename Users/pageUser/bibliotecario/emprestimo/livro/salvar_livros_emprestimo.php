<?php
session_start();
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

if(!isset($_SESSION['aluno_emprestimo']) || !isset($_POST['livros'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

try {
    $livros = json_decode($_POST['livros'], true);
    
    if(json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Erro ao decodificar livros');
    }
    
    $_SESSION['livros_emprestimo'] = $livros;
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>