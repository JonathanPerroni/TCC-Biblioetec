<?php
session_start();
include_once("../../../conexao/conexao.php");

// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: ../../login/login.php");
    exit();
}

$query = "SELECT e.id, a.nome AS aluno, l.isbn, l.titulo, e.data_emprestimo, e.data_entrega 
          FROM tbemprestimos e
          JOIN tbalunos a ON e.cpf_aluno = a.cpf
          JOIN tblivros l ON e.isbn_livro = l.isbn
          WHERE e.status = 'emprestado'
          ORDER BY e.data_emprestimo DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empréstimos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Lista de Empréstimos</h2>
    <table border="1">
        <tr>
            <th>Nº Empréstimo</th>
            <th>Aluno</th>
            <th>ISBN</th>
            <th>Título do Livro</th>
            <th>Data Empréstimo</th>
            <th>Data Entrega</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['aluno']; ?></td>
            <td><?= $row['isbn']; ?></td>
            <td><?= $row['titulo']; ?></td>
            <td><?= date("d/m/Y", strtotime($row['data_emprestimo'])); ?></td>
            <td>
                <?php 
                    // Verifica se data_entrega é válida antes de exibir
                    if ($row['data_entrega']) {
                        echo date("d/m/Y", strtotime($row['data_entrega']));
                    } else {
                        echo "Data não definida";
                    }
                ?>
            </td>
            <td>
                <a href="detalhes_emprestimo.php?id=<?= $row['id']; ?>">Visualizar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
