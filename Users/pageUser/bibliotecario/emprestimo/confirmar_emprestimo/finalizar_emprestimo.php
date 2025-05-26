<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

session_start();
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

set_exception_handler(function ($exception) {
    error_log("Exceção não capturada: " . $exception->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erro interno no servidor']);
    exit;
});

if (!isset($_SESSION['bibliotecario']) || !isset($_SESSION['aluno_emprestimo']) || !isset($_SESSION['livros_emprestimo'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado ou sessão inválida']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Dados inválidos recebidos');
    }

    $aluno = $_SESSION['aluno_emprestimo'];
    $bibliotecario = $_SESSION['bibliotecario'];
    $livros = $_SESSION['livros_emprestimo'];

    if (empty($livros)) {
        throw new Exception('Nenhum livro selecionado para empréstimo');
    }

    if (empty($aluno['ra_aluno'])) {
        throw new Exception('RA do aluno não encontrado na sessão');
    }

    $qntd_livros = count($livros);
    $data_devolucao_geral = date('Y-m-d', strtotime('+7 days'));

    $conn->begin_transaction();

    // Inserir empréstimo principal (nome_aluno, emprestado_por, id_bibliotecario, qntd_livros)
$sql_emprestimo = "INSERT INTO tbemprestimos (
    ra_aluno, nome_aluno, qntd_livros, data_emprestimo,
    data_devolucao_prevista, emprestado_por, id_bibliotecario
) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_emprestimo);
    if (!$stmt) {
        throw new Exception("Erro ao preparar empréstimo: " . $conn->error);
    }
    $stmt->bind_param("ssissi",
        $aluno['ra_aluno'],
        $aluno['nome'],
        $qntd_livros,
        $data_devolucao_geral,
        $bibliotecario['nome'],
        $bibliotecario['codigo']
    );
    if (!$stmt->execute()) {
        throw new Exception("Erro ao registrar empréstimo: " . $stmt->error);
    }
    $emprestimo_id = $conn->insert_id;
    $stmt->close();

    // Atualiza o n_emprestimo com o próprio id_emprestimo
    $sql_n_emprestimo = "UPDATE tbemprestimos SET n_emprestimo = ? WHERE id_emprestimo = ?";
    $stmt = $conn->prepare($sql_n_emprestimo);
    if (!$stmt) {
        throw new Exception("Erro ao atualizar número do empréstimo: " . $conn->error);
    }
    $stmt->bind_param("ii", $emprestimo_id, $emprestimo_id);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao atualizar n_emprestimo: " . $stmt->error);
    }
    $stmt->close();

    // Processar cada livro
    foreach ($livros as $livro) {
        // Verificar disponibilidade
        $sql_verifica = "SELECT 
                            (e.total_exemplares - 
                                (SELECT COUNT(*) 
                                FROM tbitens_emprestimo i 
                                WHERE i.isbn_falso = l.isbn_falso 
                                AND i.data_devolucao_efetiva IS NULL)
                            ) AS disponiveis 
                        FROM tblivros l
                        JOIN tblivro_estoque e ON l.isbn_falso = e.isbn_falso
                        WHERE l.isbn_falso = ? FOR UPDATE";
        $stmt = $conn->prepare($sql_verifica);
        if (!$stmt) {
            throw new Exception("Erro ao verificar estoque: " . $conn->error);
        }
        $stmt->bind_param("s", $livro['isbn_falso']);
        if (!$stmt->execute()) {
            throw new Exception("Erro na verificação: " . $stmt->error);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result || $result['disponiveis'] <= 0) {
            throw new Exception("Livro {$livro['titulo']} não disponível");
        }

        // Registrar item do empréstimo
        $sql_item = "INSERT INTO tbitens_emprestimo (
            id_emprestimo, isbn_falso, tombo, data_devolucao_prevista
        ) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_item);
        if (!$stmt) {
            throw new Exception("Erro ao preparar item: " . $conn->error);
        }
        $data_devolucao = date('Y-m-d', strtotime($livro['data_devolucao'] ?? '+7 days'));
        $stmt->bind_param("isss", $emprestimo_id, $livro['isbn_falso'], $livro['tombo'], $data_devolucao);
        if (!$stmt->execute()) {
            error_log("Erro ao registrar item: " . $stmt->error);
            throw new Exception("Erro ao registrar item: " . $stmt->error);
        }
        $stmt->close();

        // Atualizar estoque
        $sql_atualiza = "UPDATE tblivro_estoque SET total_exemplares = total_exemplares - 1 WHERE isbn_falso = ?";
        $stmt = $conn->prepare($sql_atualiza);
        if (!$stmt) {
            throw new Exception("Erro ao atualizar estoque: " . $conn->error);
        }
        $stmt->bind_param("s", $livro['isbn_falso']);
        if (!$stmt->execute()) {
            throw new Exception("Erro na atualização: " . $stmt->error);
        }
        $stmt->close();
    }

    $conn->commit();
    unset($_SESSION['livros_emprestimo']);

    echo json_encode([
        'success' => true,
        'emprestimo_id' => $emprestimo_id,
        'message' => 'Empréstimo registrado com sucesso'
    ]);

} catch (Exception $e) {
    if (isset($conn) && method_exists($conn, 'rollback') && $conn->in_transaction) {
        $conn->rollback();
    }
    error_log("ERRO FINALIZAR EMPRESTIMO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar: ' . $e->getMessage()
    ]);
    exit;
}
?>