<?php
session_start();
require '../../../../../conexao/conexao.php';

$emprestimo_id = $_GET['id'] ?? 0;

// Consulta o empréstimo
$sql_emprestimo = "SELECT e.*, a.nome as aluno_nome, a.ra_aluno 
                  FROM tbemprestimos e
                  JOIN tbalunos a ON e.ra_aluno = a.ra_aluno
                  WHERE e.id_emprestimo = ?";
$stmt = $conn->prepare($sql_emprestimo);
$stmt->bind_param("i", $emprestimo_id);
$stmt->execute();
$emprestimo = $stmt->get_result()->fetch_assoc();

// Consulta os livros
$sql_livros = "SELECT l.* FROM tblivros l
              JOIN tbemprestimos e ON l.isbn_falso = e.isbn_falso
              WHERE e.id_emprestimo = ?";
$stmt = $conn->prepare($sql_livros);
$stmt->bind_param("i", $emprestimo_id);
$stmt->execute();
$livros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comprovante de Empréstimo</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .assinatura { margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Biblioteca Escolar</h2>
        <h3>Comprovante de Empréstimo</h3>
        <p>Nº <?= $emprestimo_id ?></p>
    </div>
    
    <div class="info">
        <p><strong>Aluno:</strong> <?= htmlspecialchars($emprestimo['aluno_nome']) ?></p>
        <p><strong>RA:</strong> <?= htmlspecialchars($emprestimo['ra_aluno']) ?></p>
        <p><strong>Data Empréstimo:</strong> <?= date('d/m/Y H:i', strtotime($emprestimo['data_emprestimo'])) ?></p>
        <p><strong>Devolução Prevista:</strong> <?= date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista'])) ?></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tombo/ISBN</th>
                <th>Título</th>
                <th>Autor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livros as $livro): ?>
            <tr>
                <td><?= htmlspecialchars($livro['isbn_falso']) ?></td>
                <td><?= htmlspecialchars($livro['titulo']) ?></td>
                <td><?= htmlspecialchars($livro['autor']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="assinatura">
        <p>___________________________</p>  <p>___________________________</p>
           <p>Ass. do Bibliotecário</p>            <p>Ass. do Aluno</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.location.href = 'pesquisa_aluno.php';
            }, 1000);
        }
    </script>
</body>
</html>