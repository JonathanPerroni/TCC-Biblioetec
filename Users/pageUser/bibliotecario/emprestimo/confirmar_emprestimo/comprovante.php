<?php
session_start();
require '../../../../../conexao/conexao.php';

$emprestimo_id = $_GET['id'] ?? 0;

// Consulta o empréstimo
$sql_emprestimo = "SELECT e.*, a.nome as aluno_nome, a.ra_aluno, a.nome_curso, a.periodo,
                  b.nome as bibliotecario_nome
                  FROM tbemprestimo e
                  JOIN tbalunos a ON e.ra_aluno = a.ra_aluno
                  JOIN tbbibliotecario b ON e.id_bibliotecario = b.codigo
                  WHERE e.id_emprestimo = ?";
$stmt = $conn->prepare($sql_emprestimo);
$stmt->bind_param("i", $emprestimo_id);
$stmt->execute();
$emprestimo = $stmt->get_result()->fetch_assoc();

// Consulta os livros emprestados
$sql_livros = "SELECT l.titulo, l.autor, l.isbn_falso, l.tombo 
              FROM tblivros l
              WHERE l.isbn_falso = ?";
$stmt = $conn->prepare($sql_livros);
$stmt->bind_param("s", $emprestimo['isbn_falso']);
$stmt->execute();
$livros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comprovante de Empréstimo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .info { margin: 20px 0; }
        .info-item { margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; }
        .assinatura { margin-top: 50px; text-align: center; }
        .assinatura-line { width: 300px; border-top: 1px solid #000; margin: 0 auto; }
        @media print {
            .no-print { display: none; }
            body { font-size: 12pt; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Biblioteca Escolar</h1>
        <h2>Comprovante de Empréstimo</h2>
        <p>Nº <?= str_pad($emprestimo_id, 6, '0', STR_PAD_LEFT) ?></p>
    </div>
    
    <div class="info">
        <div class="info-item"><strong>Aluno:</strong> <?= htmlspecialchars($emprestimo['aluno_nome']) ?></div>
        <div class="info-item"><strong>RA:</strong> <?= htmlspecialchars($emprestimo['ra_aluno']) ?></div>
        <div class="info-item"><strong>Curso:</strong> <?= htmlspecialchars($emprestimo['nome_curso']) ?></div>
        <div class="info-item"><strong>Período:</strong> <?= htmlspecialchars($emprestimo['periodo']) ?></div>
        <div class="info-item"><strong>Data Empréstimo:</strong> <?= date('d/m/Y H:i', strtotime($emprestimo['data_emprestimo'])) ?></div>
        <div class="info-item"><strong>Devolução Prevista:</strong> <?= date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista'])) ?></div>
        <div class="info-item"><strong>Bibliotecário:</strong> <?= htmlspecialchars($emprestimo['bibliotecario_nome']) ?></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Autor</th>
                <th>ISBN Falso</th>
                <th>Tombo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livros as $index => $livro): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($livro['titulo']) ?></td>
                <td><?= htmlspecialchars($livro['autor']) ?></td>
                <td><?= htmlspecialchars($livro['isbn_falso']) ?></td>
                <td><?= htmlspecialchars($livro['tombo']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="assinatura">
        <div class="assinatura-line"></div>
        <p>Assinatura do Bibliotecário</p>
    </div>

    <div class="footer no-print">
        <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
        <a href="pesquisa_aluno.php" class="btn btn-secondary">Voltar</a>
    </div>

    <script>
        window.onload = function() {
            // Imprime automaticamente e redireciona após 3 segundos
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    window.location.href = 'pesquisa_aluno.php';
                }, 3000);
            }, 500);
        };
    </script>
</body>
</html>