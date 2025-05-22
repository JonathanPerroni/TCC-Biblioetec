<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');// já verifica login e carrega CSRF
$ra = $_GET['ra'] ?? '';

if (empty($ra)) {
    echo json_encode(['error' => 'RA não fornecido']);
    exit;
}

// Verifica empréstimos não devolvidos
$sql = "SELECT COUNT(*) as total 
        FROM tbemprestimos 
        WHERE ra_aluno = ? AND data_devolucao_efetiva IS NULL";
$stmt = $conn->prepare($sql);
$stmt->execute([$ra]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['total'] > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Aluno possui ' . $result['total'] . ' empréstimo(s) pendente(s).'
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Aluno está regular e pode realizar empréstimo.'
    ]);
}
?>