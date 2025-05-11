<?php
include_once("../../../../../conexao/conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_emprestimo = intval($_POST['id_emprestimo']);
    $nova_data = $_POST['nova_data'];

    // Define o fuso horário para São Paulo
    date_default_timezone_set('America/Sao_Paulo');
    // Obtém a data e hora atual
    $data_atual = date('Y-m-d H:i:s');

    // Verifica se a nova data está no formato correto (opcional, mas recomendado)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nova_data)) {
        echo "<script>alert('Data inválida.'); window.history.back();</script>";
        exit;
    }

    // Atualiza o registro no banco
    $query = "UPDATE tbemprestimos 
              SET 
                data_emprestimo = ?, 
                data_devolucao_prevista = ?
              WHERE id_emprestimo = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $data_atual, $nova_data, $id_emprestimo);
    
    if ($stmt->execute()) {
        echo "<script>alert('Empréstimo adiado com sucesso!'); window.location.href = 'detalhes_emprestimo.php?id=$id_emprestimo';</script>";
    } else {
        echo "<script>alert('Erro ao adiar o empréstimo.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Requisição inválida.'); window.history.back();</script>";
}
?>
