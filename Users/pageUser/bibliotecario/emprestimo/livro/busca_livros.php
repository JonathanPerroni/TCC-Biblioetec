<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

// Habilita CORS para evitar problemas de cross-origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// Para debug - descomente estas linhas durante testes
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$query = $_GET['q'] ?? '';

if(empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Verifique primeiro se a conexão está funcionando
    if($conn->connect_error) {
        throw new Exception("Erro na conexão: " . $conn->connect_error);
    }

    // Defina o charset para UTF-8
    $conn->set_charset("utf8mb4");

    // Consulta SQL corrigida
    $sql = "SELECT titulo, autor, isbn_falso 
            FROM tblivros 
            WHERE titulo LIKE CONCAT('%', ?, '%') 
               OR isbn_falso LIKE CONCAT('%', ?, '%')
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if(!$stmt) {
        throw new Exception("Erro na preparação: " . $conn->error);
    }

    $stmt->bind_param("ss", $query, $query);
    if(!$stmt->execute()) {
        throw new Exception("Erro na execução: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $livros = $result->fetch_all(MYSQLI_ASSOC);

    // Verifique se há resultados
    if(empty($livros)) {
        echo json_encode([]);
    } else {
        echo json_encode($livros);
    }

} catch(Exception $e) {
    // Log do erro no servidor
    error_log("ERRO em busca_livros.php: " . $e->getMessage());
    
    // Retorne um array vazio em caso de erro para o frontend não quebrar
    echo json_encode([]);
}
?>