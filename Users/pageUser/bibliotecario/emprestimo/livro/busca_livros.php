<?php
session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0); // Não exibe erros na tela
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Salva erros em um arquivo



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
    $sql = "SELECT titulo, autor, isbn_falso, tombo
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

}catch(Exception $e) {
    error_log("ERRO em busca_livros.php: " . $e->getMessage());
    
    // Retorne um JSON com estrutura clara
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
?>