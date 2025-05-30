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

$query = $_GET["q"] ?? "";

if (strlen($query) < 2) {
    ob_end_clean(); // Limpa qualquer saída inesperada do buffer antes do JSON
    echo json_encode([]);
    exit();
}

try {
    if (is_numeric($query)) {
        $sql = "SELECT nome, ra_aluno FROM tbalunos WHERE ra_aluno LIKE ? LIMIT 10";
        $param = "%$query%";
    } else {
        $sql = "SELECT nome, ra_aluno FROM tbalunos WHERE nome LIKE ? LIMIT 10";
        $param = "%$query%";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    $alunos = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    ob_end_clean(); // Limpa o buffer antes de enviar o JSON final
    echo json_encode($alunos);
} catch (Exception $e) {
    ob_end_clean(); // Limpa o buffer em caso de erro também
    // Loga o erro para o servidor
    error_log("Erro em busca_alunos.php: " . $e->getMessage());
    // Retorna um erro JSON genérico para o cliente
    echo json_encode(["error" => "Erro ao buscar alunos."]);
}
exit(); // Garante que nada mais seja executado
?>
