<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');// já verifica login e carrega CSRF
$ra = $_GET['ra'] ?? '';

if(empty($ra)) {
    echo json_encode(['error' => 'RA não fornecido']);
    exit;
}

try {
    $sql = "SELECT codigo, nome, ra_aluno, situacao, nome_curso, tipo_ensino, status 
            FROM tbalunos 
            WHERE ra_aluno = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ra);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();

    if(!$aluno) {
        echo json_encode(['error' => 'Aluno não encontrado']);
        exit;
    }

    echo json_encode($aluno);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>