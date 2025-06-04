<?php
include_once("../../../../../conexao/conexao.php");
include_once("../../seguranca.php"); // Inclui segurança

session_start(); // Garante que a sessão está ativa para mensagens

// --- MODIFICAÇÃO: Recebe dados do item específico a ser devolvido ---
$id_emprestimo_item = isset($_POST["id_emprestimo_item"]) ? intval($_POST["id_emprestimo_item"]) : 0;
$tombo_item = isset($_POST["tombo_item"]) ? trim($_POST["tombo_item"]) : null;
$isbn_falso_item = isset($_POST["isbn_falso_item"]) ? trim($_POST["isbn_falso_item"]) : null;

// Validação básica
if ($id_emprestimo_item === 0 || $tombo_item === null || $isbn_falso_item === null) {
    $_SESSION["msg"] = "Erro: Dados inválidos para devolução.";
    // Idealmente redirecionar para a página de detalhes anterior
    // Usando JS para voltar pode ser uma alternativa simples
    echo "<script>alert('Erro: Dados inválidos para devolução.'); window.history.back();</script>";
    exit();
}

// Obtém a data e hora atual
date_default_timezone_set("America/Sao_Paulo");
$data_devolucao_efetiva = date("Y-m-d H:i:s");

// Inicia transação
$conn->begin_transaction();

try {
    // 1. Atualiza a data de devolução efetiva na tabela tbemprestimos PARA ESTE ITEM ESPECÍFICO
    //    (Identificado pelo id_emprestimo que é único por linha/item agora)
    $sql_update_emprestimo = "UPDATE tbemprestimos 
                                SET data_devolucao_efetiva = ? 
                                WHERE id_emprestimo = ? AND data_devolucao_efetiva IS NULL"; // Só atualiza se não foi devolvido ainda
    $stmt_update = $conn->prepare($sql_update_emprestimo);
    if (!$stmt_update) throw new Exception("Erro ao preparar update empréstimo: " . $conn->error);
    $stmt_update->bind_param("si", $data_devolucao_efetiva, $id_emprestimo_item);
    if (!$stmt_update->execute()) {
        throw new Exception("Erro ao atualizar data de devolução: " . $stmt_update->error);
    }
    $affected_rows = $stmt_update->affected_rows;
    $stmt_update->close();

    // Se nenhuma linha foi afetada, pode ser que já foi devolvido ou ID inválido
    if ($affected_rows === 0) {
         // Verifica se já foi devolvido
         $sql_check = "SELECT data_devolucao_efetiva FROM tbemprestimos WHERE id_emprestimo = ?";
         $stmt_check = $conn->prepare($sql_check);
         $stmt_check->bind_param("i", $id_emprestimo_item);
         $stmt_check->execute();
         $res_check = $stmt_check->get_result();
         $row_check = $res_check->fetch_assoc();
         $stmt_check->close();
         if ($row_check && !empty($row_check["data_devolucao_efetiva"])) {
             throw new Exception("Este item já foi devolvido anteriormente.");
         } else {
             throw new Exception("Item de empréstimo não encontrado ou erro inesperado.");
         }
    }

    // 2. Busca os dados atualizados do empréstimo (para log em tbdevolucao)
    $sql_select = "SELECT * FROM tbemprestimos WHERE id_emprestimo = ?";
    $stmt_select = $conn->prepare($sql_select);
    if (!$stmt_select) throw new Exception("Erro ao preparar select empréstimo: " . $conn->error);
    $stmt_select->bind_param("i", $id_emprestimo_item);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    if ($result_select->num_rows === 0) {
        throw new Exception("Empréstimo não encontrado após atualização.");
    }
    $emprestimo = $result_select->fetch_assoc();
    $stmt_select->close();

    // 3. Insere os dados na tabela de devolução (como log)
    $sql_insert_devolucao = "INSERT INTO tbdevolucao (id_emprestimo, n_emprestimo, ra_aluno, nome_aluno, isbn_falso, isbn, tombo, nome_livro, qntd_livros, data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva, tipo) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_devolucao = $conn->prepare($sql_insert_devolucao);
    if (!$stmt_devolucao) throw new Exception("Erro ao preparar insert devolução: " . $conn->error);
    // Note que qntd_livros aqui será sempre 1, pois estamos devolvendo item a item
    $qntd_item = 1; 
    $stmt_devolucao->bind_param("isssssssissss", 
        $emprestimo["id_emprestimo"],
        $emprestimo["n_emprestimo"], 
        $emprestimo["ra_aluno"], 
        $emprestimo["nome_aluno"], 
        $emprestimo["isbn_falso"], 
        $emprestimo["isbn"], 
        $emprestimo["tombo"], 
        $emprestimo["nome_livro"], 
        $qntd_item, // Sempre 1
        $emprestimo["data_emprestimo"], 
        $emprestimo["data_devolucao_prevista"], 
        $emprestimo["data_devolucao_efetiva"], 
        $emprestimo["tipo"]
    );
    if (!$stmt_devolucao->execute()) {
        throw new Exception("Erro ao registrar log de devolução: " . $stmt_devolucao->error);
    }
    $stmt_devolucao->close();

    // --- MODIFICAÇÃO: Remover o DELETE --- 
    // // Exclui o registro da tabela tbemprestimos
    // $query_delete = "DELETE FROM tbemprestimos WHERE id_emprestimo = ?";
    // $stmt_delete = $conn->prepare($query_delete);
    // $stmt_delete->bind_param("i", $id_emprestimo_item);
    // $stmt_delete->execute();
    // $stmt_delete->close();

    // --- MODIFICAÇÃO: Atualizar o estoque --- 
    $sql_update_estoque = "UPDATE tblivro_estoque SET total_exemplares = total_exemplares + 1 WHERE isbn_falso = ?";
    $stmt_estoque = $conn->prepare($sql_update_estoque);
    if (!$stmt_estoque) throw new Exception("Erro ao preparar update estoque: " . $conn->error);
    $stmt_estoque->bind_param("s", $isbn_falso_item);
    if (!$stmt_estoque->execute()) {
        // Logar o erro, mas talvez não interromper a devolução por isso?
        error_log("Erro ao atualizar estoque para ISBN Falso " . $isbn_falso_item . ": " . $stmt_estoque->error);
        // Dependendo da regra de negócio, pode-se lançar a exceção ou apenas logar.
        // throw new Exception("Erro ao atualizar estoque: " . $stmt_estoque->error);
    }
    $stmt_estoque->close();

    // Se tudo deu certo, commita a transação
    $conn->commit();

    $_SESSION["msg"] = "Devolução do item (Tombo: " . htmlspecialchars($tombo_item) . ") registrada com sucesso!";
    // Redireciona de volta para a página de detalhes daquele n_emprestimo
    header("Location: detalhes_emprestimo.php?n_emprestimo=" . urlencode($emprestimo["n_emprestimo"]));
    exit();

} catch (Exception $e) {
    // Desfaz a transação em caso de erro
    $conn->rollback();
    error_log("Erro ao realizar devolução: " . $e->getMessage());
    $_SESSION["msg"] = "Erro ao realizar devolução: " . $e->getMessage();

    // Exibe o alerta e volta para a página anterior
    echo "<script>alert(" . json_encode("Erro ao realizar devolução: " . $e->getMessage()) . "); window.history.back();</script>";
    exit();
}

$conn->close();
?>
