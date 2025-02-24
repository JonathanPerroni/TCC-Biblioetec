<?php
session_start();
include_once("../../../conexao/conexao.php");

// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: ../../login/login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Empréstimo não encontrado!";
    exit();
}

// Consulta para obter detalhes do empréstimo
$query = "SELECT e.id, a.nome AS aluno, a.cpf, l.isbn, l.titulo, 
          DATE(e.data_emprestimo) AS data_emprestimo, e.data_entrega 
          FROM tbemprestimos e
          JOIN tbalunos a ON e.cpf_aluno = a.cpf
          JOIN tblivros l ON e.isbn_livro = l.isbn
          WHERE e.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$emprestimo = $result->fetch_assoc();

if (!$emprestimo) {
    echo "Empréstimo não encontrado!";
    exit();
}

// Verifica se a data de entrega é válida
$data_entrega = $emprestimo['data_entrega'];
if ($data_entrega && $data_entrega !== '0000-00-00') {
    $data_entrega_formatada = date("d/m/Y", strtotime($data_entrega));
} else {
    $data_entrega_formatada = "Data não definida";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Empréstimo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Detalhes do Empréstimo</h2>
    <p><strong>Aluno:</strong> <?= $emprestimo['aluno']; ?></p>
    <p><strong>CPF:</strong> <?= $emprestimo['cpf']; ?></p>
    <p><strong>ISBN:</strong> <?= $emprestimo['isbn']; ?></p>
    <p><strong>Título do Livro:</strong> <?= $emprestimo['titulo']; ?></p>
    <p><strong>Data Empréstimo:</strong> <?= date("d/m/Y", strtotime($emprestimo['data_emprestimo'])); ?></p>
    <p><strong>Data Entrega:</strong> <?= $data_entrega_formatada; ?></p>

    <form method="POST" action="processa_emprestimo.php">
        <input type="hidden" name="id" value="<?= $emprestimo['id']; ?>">
        <input type="hidden" name="isbn" value="<?= $emprestimo['isbn']; ?>">

        <button type="submit" name="devolver">Devolver Livro</button>
        <button type="submit" name="estender">Estender Prazo</button>
    </form>
</body>
</html>
