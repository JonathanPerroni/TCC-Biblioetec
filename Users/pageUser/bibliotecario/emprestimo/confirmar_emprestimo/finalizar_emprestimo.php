<?php
session_start();
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

// Verificação robusta de acesso
if (!isset($_SESSION['bibliotecario']) || !isset($_SESSION['aluno_emprestimo']) || !isset($_SESSION['livros_emprestimo'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado ou sessão inválida']);
    exit;
}

try {
    // Decodifica os dados JSON recebidos
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
        throw new Exception('RA do aluno não encontrado na sessão.');
    }

    // Inicia transação
    $conn->begin_transaction();

    // Inserir empréstimo
    $sql_emprestimo = "INSERT INTO tbemprestimos (ra_aluno, emprestado_por, data_emprestimo) VALUES (?, ?, NOW())";
    if (!$stmt = $conn->prepare($sql_emprestimo)) {
        throw new Exception("Erro no prepare tbemprestimos: " . $conn->error);
    }
    if (!$stmt->bind_param("ss", $aluno['ra_aluno'], $bibliotecario['codigo'])) {
        throw new Exception("Erro no bind_param tbemprestimos: " . $stmt->error);
    }
    if (!$stmt->execute()) {
        throw new Exception("Erro no execute tbemprestimos: " . $stmt->error);
    }
    $emprestimo_id = $conn->insert_id;
    $stmt->close();

    // Processar cada livro
    foreach ($livros as $livro) {
        // Verificar disponibilidade com FOR UPDATE
        $sql_verifica = "SELECT disponiveis FROM tblivros WHERE isbn_falso = ? FOR UPDATE";
        if (!$stmt = $conn->prepare($sql_verifica)) {
            throw new Exception("Erro no prepare tblivros: " . $conn->error);
        }
        if (!$stmt->bind_param("s", $livro['isbn_falso'])) {
            throw new Exception("Erro no bind_param tblivros: " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception("Erro no execute tblivros: " . $stmt->error);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result || $result['disponiveis'] <= 0) {
            throw new Exception("Livro {$livro['titulo']} não disponível para empréstimo");
        }

        // Inserir item empréstimo
        $sql_item = "INSERT INTO tbitens_emprestimo (id_emprestimo, isbn_falso, data_devolucao_prevista) VALUES (?, ?, ?)";
        if (!$stmt = $conn->prepare($sql_item)) {
            throw new Exception("Erro no prepare tbitens_emprestimo: " . $conn->error);
        }
        $data_devolucao = date('Y-m-d', strtotime($livro['data_devolucao']));
        if (!$stmt->bind_param("iss", $emprestimo_id, $livro['isbn_falso'], $data_devolucao)) {
            throw new Exception("Erro no bind_param tbitens_emprestimo: " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception("Erro no execute tbitens_emprestimo: " . $stmt->error);
        }
        $stmt->close();

        // Atualizar estoque
        $sql_atualiza = "UPDATE tblivros SET disponiveis = disponiveis - 1 WHERE isbn_falso = ?";
        if (!$stmt = $conn->prepare($sql_atualiza)) {
            throw new Exception("Erro no prepare UPDATE tblivros: " . $conn->error);
        }
        if (!$stmt->bind_param("s", $livro['isbn_falso'])) {
            throw new Exception("Erro no bind_param UPDATE tblivros: " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception("Erro no execute UPDATE tblivros: " . $stmt->error);
        }
        $stmt->close();
    }

    // Commit se tudo ocorrer bem
    $conn->commit();

    // Limpa a sessão dos livros
    unset($_SESSION['livros_emprestimo']);

    echo json_encode([
        'success' => true,
        'emprestimo_id' => $emprestimo_id,
        'message' => 'Empréstimo registrado com sucesso'
    ]);

} catch (Exception $e) {
    if ($conn->in_transaction) {
        $conn->rollback();
    }
    error_log("Erro no empréstimo: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar empréstimo: ' . $e->getMessage()
    ]);
}
?>
