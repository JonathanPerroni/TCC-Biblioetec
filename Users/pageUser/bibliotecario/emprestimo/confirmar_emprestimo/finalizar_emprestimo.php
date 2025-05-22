<?php
session_start();
require '../../../../../conexao/conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['bibliotecario']) || !isset($_SESSION['livros_emprestimo'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $aluno_id = $input['aluno_id'] ?? 0;
    $bibliotecario_id = $_SESSION['bibliotecario']['codigo'];
    $livros = $_SESSION['livros_emprestimo'];

    if (empty($livros)) {
        throw new Exception('Nenhum livro selecionado');
    }

    // Consulta dados do aluno
    $sql_aluno = "SELECT codigo, nome, ra_aluno FROM tbalunos WHERE codigo = ?";
    $stmt = $conn->prepare($sql_aluno);
    $stmt->bind_param("i", $aluno_id);
    $stmt->execute();
    $aluno = $stmt->get_result()->fetch_assoc();

    if (!$aluno) {
        throw new Exception('Aluno não encontrado');
    }

    $conn->begin_transaction();

    // 1. Cria o registro do empréstimo
    $sql_emprestimo = "INSERT INTO tbemprestimos 
                      (ra_aluno, nome_aluno, data_emprestimo, data_devolucao_prevista) 
                      VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql_emprestimo);
    
    // Calcula data de devolução (padrão 7 dias)
    $data_devolucao = date('Y-m-d', strtotime('+7 days'));
    $stmt->bind_param("iss", 
        $aluno['ra_aluno'],
        $aluno['nome'],
        $data_devolucao
    );
    $stmt->execute();
    $emprestimo_id = $conn->insert_id;

    // 2. Atualiza estoque e registra livros
    foreach ($livros as $livro) {
        // Atualiza estoque
        $sql_estoque = "UPDATE tblivros_estoque 
                       SET total_exemplares = total_exemplares - 1 
                       WHERE isbn_falso = ?";
        $stmt = $conn->prepare($sql_estoque);
        $stmt->bind_param("s", $livro['isbn_falso']);
        $stmt->execute();

        // Registra livro emprestado
        $sql_livro = "INSERT INTO tblivros 
                     (isbn_falso, titulo, autor, data_edicao, edicao, quantidade) 
                     VALUES (?, ?, ?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     titulo = VALUES(titulo), autor = VALUES(autor)";
        $stmt = $conn->prepare($sql_livro);
        $stmt->bind_param("sssssi",
            $livro['isbn_falso'],
            $livro['titulo'],
            $livro['autor'],
            date('Y-m-d'),
            '1ª Edição',
            1
        );
        $stmt->execute();
    }

    $conn->commit();
    unset($_SESSION['livros_emprestimo']);

    echo json_encode([
        'success' => true,
        'emprestimo_id' => $emprestimo_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>