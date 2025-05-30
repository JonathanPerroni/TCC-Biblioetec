<?php
session_start();
ob_start(); // Inicia o buffer de saída

date_default_timezone_set("America/Sao_Paulo");

// *** CORREÇÃO: Define que esta é uma requisição AJAX ANTES de incluir segurança ***
define("IS_AJAX_REQUEST", true);

require "../../../../../conexao/conexao.php";
include_once "../../seguranca.php"; // Agora não vai gerar o token CSRF

// Garante que o tipo de conteúdo seja JSON
header("Content-Type: application/json");

$ra = $_GET["ra"] ?? "";

if (empty($ra)) {
    ob_end_clean(); // Limpa o buffer
    echo json_encode(["error" => "RA não fornecido"]);
    exit();
}

try {
    $sql = "SELECT codigo, nome, ra_aluno, situacao, nome_curso, tipo_ensino, status 
            FROM tbalunos 
            WHERE ra_aluno = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $ra);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();
    $stmt->close();

    if (!$aluno) {
        ob_end_clean(); // Limpa o buffer
        echo json_encode(["error" => "Aluno não encontrado"]);
        exit();
    }

    ob_end_clean(); // Limpa o buffer antes de enviar o JSON final
    echo json_encode($aluno);
    exit(); // Garante que nada mais seja executado

} catch (Exception $e) {
    ob_end_clean(); // Limpa o buffer em caso de erro também
    // Loga o erro para o servidor
    error_log("Erro em carrega_aluno.php: " . $e->getMessage());
    // Retorna um erro JSON genérico para o cliente
    echo json_encode(["error" => "Erro ao carregar dados do aluno."]);
    exit();
}
?>
