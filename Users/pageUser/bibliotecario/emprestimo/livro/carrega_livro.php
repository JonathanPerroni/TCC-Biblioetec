<?php
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

$isbn = $_GET['isbn'] ?? '';

if(empty($isbn)) {
    echo json_encode(['error' => 'ISBN não fornecido']);
    exit;
}

try {
    // Obter informações básicas do livro
    $sqlLivro = "SELECT titulo, autor, isbn_falso, tombo
                 FROM tblivros 
                 WHERE isbn_falso = ? 
                 LIMIT 1";
    
    $stmt = $conn->prepare($sqlLivro);
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $livro = $result->fetch_assoc();

    if(!$livro) {
        echo json_encode(['error' => 'Livro não encontrado']);
        exit;
    }

    // Obter total de exemplares
    $sqlEstoque = "SELECT total_exemplares 
                   FROM tblivro_estoque 
                   WHERE isbn_falso = ?";
    
    $stmt = $conn->prepare($sqlEstoque);
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $estoque = $result->fetch_assoc();
    
    // ADICIONE AQUI O CÁLCULO DE DISPONÍVEIS E GARANTIA DE VALORES NUMÉRICOS
    $livro['disponiveis'] = (int)($estoque['total_exemplares'] ?? 0) - (int)($emprestimos['total'] ?? 0);
    $livro['total_exemplares'] = (int)($estoque['total_exemplares'] ?? 0);
    // Calcular empréstimos ativos
    $sqlEmprestimos = "SELECT COUNT(*) as total
                       FROM tbemprestimos e
                       LEFT JOIN tbdevolucao d ON e.id_emprestimo = d.id_emprestimo
                       WHERE e.isbn_falso = ? AND d.data_devolucao_efetiva IS NULL";
    
    $stmt = $conn->prepare($sqlEmprestimos);
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $emprestimos = $result->fetch_assoc();
    
    $livro['emprestados'] = $emprestimos['total'] ?? 0;

    echo json_encode($livro);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>