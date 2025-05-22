<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');// já verifica login e carrega CSRF
// Adicione no início do arquivo
header('Content-Type: application/json');


$query = $_GET['q'] ?? '';

if(strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    if(is_numeric($query)) {
        $sql = "SELECT nome, ra_aluno FROM tbalunos WHERE ra_aluno LIKE ? LIMIT 10";
        $param = "%$query%";
    } else {
        $sql = "SELECT nome, ra_aluno FROM tbalunos WHERE nome LIKE ? LIMIT 10";
        $param = "%$query%";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    $alunos = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($alunos);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>