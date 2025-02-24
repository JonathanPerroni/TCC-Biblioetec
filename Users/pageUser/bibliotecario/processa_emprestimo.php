<?php
session_start();
include_once("../../../conexao/conexao.php");

if (isset($_POST['devolver'])) {
    $id = $_POST['id'];
    $isbn = $_POST['isbn'];

    // Atualiza o status do empréstimo para "devolvido"
    $query = "UPDATE tbemprestimos SET status = 'devolvido' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Atualiza o estoque do livro
        $query_livro = "UPDATE tblivros SET quantidade = quantidade + 1 WHERE isbn = ?";
        $stmt = $conn->prepare($query_livro);
        $stmt->bind_param("s", $isbn);
        $stmt->execute();

        echo "<script>
                alert('Livro devolvido com sucesso!');
                window.location.href = 'lista_emprestimos.php';
              </script>";
    }
}

if (isset($_POST['estender'])) {
    $id = $_POST['id'];

    // Adiciona 7 dias ao prazo de entrega
    $query = "UPDATE tbemprestimos SET data_entrega = DATE_ADD(data_entrega, INTERVAL 7 DAY) WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Prazo estendido por mais 7 dias!');
                window.location.href = 'detalhes_emprestimo.php?id=$id';
              </script>";
    }
}
?>
