<?php
include_once("../../../../../conexao/conexao.php");

// Verifica se o ID foi passado
if (!isset($_POST['id_emprestimo']) || empty($_POST['id_emprestimo'])) {
    echo "<script>alert('Empréstimo não encontrado.'); window.history.back();</script>";
    exit;
}

$id_emprestimo = intval($_POST['id_emprestimo']);
$n_emprestimo = $_POST['n_emprestimo'] ?? $emprestimo['n_emprestimo'];

// Obtém os dados do empréstimo
$query = "SELECT * FROM tbemprestimos WHERE id_emprestimo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_emprestimo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Empréstimo não encontrado.'); window.history.back();</script>";
    exit;
}

$emprestimo = $result->fetch_assoc();

// Obtém a data e hora atual
date_default_timezone_set('America/Sao_Paulo');
$data_devolucao_efetiva = date('Y-m-d H:i:s');

// Salva os dados na tabela de devolução
$query_devolucao = "INSERT INTO tbdevolucao (n_emprestimo, ra_aluno, nome_aluno, isbn, isbn_falso, tombo, nome_livro, qntd_livros, data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva, tipo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_devolucao = $conn->prepare($query_devolucao);
$stmt_devolucao->bind_param("ssssssssssss", 
    $n_emprestimo, 
    $emprestimo['ra_aluno'], 
    $emprestimo['nome_aluno'], 
    $emprestimo['isbn'], 
    $emprestimo['isbn_falso'], 
    $emprestimo['tombo'], 
    $emprestimo['nome_livro'], 
    $emprestimo['qntd_livros'], 
    $emprestimo['data_emprestimo'], 
    $emprestimo['data_devolucao_prevista'], 
    $data_devolucao_efetiva, 
    $emprestimo['tipo']);

if ($stmt_devolucao->execute()) {
    // Atualiza a data de devolução efetiva na tabela tbemprestimos
    $query_update = "UPDATE tbemprestimos SET data_devolucao_efetiva = ? WHERE id_emprestimo = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("si", $data_devolucao_efetiva, $id_emprestimo);
    
    if ($stmt_update->execute()) {
        // Exclui o registro da tabela tbemprestimos
        $query_delete = "DELETE FROM tbemprestimos WHERE id_emprestimo = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $id_emprestimo);
        $stmt_delete->execute();
        
        echo "<script>alert('Devolução realizada com sucesso!'); window.location.href = 'lista_emprestimos.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar a data de devolução.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Erro ao registrar a devolução.'); window.history.back();</script>";
}

$stmt_devolucao->close();
$stmt_update->close();
$stmt_delete->close();
$conn->close();
?>
