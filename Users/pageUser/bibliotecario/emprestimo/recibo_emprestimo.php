<?php
session_start();
ob_start();
date_default_timezone_set('America/Sao_Paulo');
include_once("../../../../conexao/conexao.php");

// DEBUG: mostre o que chegou em GET['n']
if (!isset($_GET['n'])) {
    echo "Número de empréstimo não informado.";
    exit;
}
$nEmprestimo = trim($_GET['n']);
echo "<pre>DEBUG: GET['n'] = '{$nEmprestimo}'</pre>";

// DEBUG: liste os últimos 10 valores de n_emprestimo na tabela
$resultAll = $conn->query("
    SELECT DISTINCT n_emprestimo
    FROM tbemprestimos
    ORDER BY id_emprestimo DESC
    LIMIT 10
");
$recent = $resultAll->fetch_all(MYSQLI_ASSOC);
echo "<pre>DEBUG: Últimos 10 n_emprestimo na tabela:\n";
foreach ($recent as $r) {
    echo " - {$r['n_emprestimo']}\n";
}
echo "</pre>";

// Agora sua query normal
$stmt = $conn->prepare("
    SELECT *
    FROM tbemprestimos
    WHERE n_emprestimo = ?
    ORDER BY id_emprestimo
");
$stmt->bind_param("s", $nEmprestimo);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

// DEBUG: quantas linhas retornou
echo "<pre>DEBUG: count(rows) = " . count($rows) . "</pre>";

if (count($rows) === 0) {
    echo "Dados do empréstimo não encontrados.";
    exit;
}
// Usa a primeira linha para cabeçalho
$emprestimo = $rows[0];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Empréstimo</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .recibo { width: 80%; margin: 20px auto; padding: 20px; border: 2px dashed #000; }
        .assinaturas { margin-top: 40px; display: flex; justify-content: space-between; }
        .assinatura { width: 45%; text-align: center; }
        .linha { margin-top: 60px; border-top: 1px solid #000; }
        .copias { page-break-after: always; }
        @media print { button { display: none; } }
    </style>
</head>
<body>

<button onclick="window.print()">Imprimir 2 Vias</button>
<!-- Botão para iniciar novo empréstimo -->
<button
  type="button"
  onclick="window.location.href='emprestimo.php';"
  style="margin-top: 10px;"
>
  Novo Empréstimo
</button>

<?php for ($i = 1; $i <= 2; $i++): ?>
    <div class="recibo copias">
        <h2>Recibo de Empréstimo de Livro</h2>
        <p><strong>Número do Empréstimo:</strong> <?= htmlspecialchars($emprestimo['n_emprestimo']) ?></p>
        <p><strong>Aluno:</strong>
           <?= htmlspecialchars($emprestimo['nome_aluno']) ?>
           (RA: <?= htmlspecialchars($emprestimo['ra_aluno']) ?>)
        </p>
        <p><strong>Data do Empréstimo:</strong>
           <?= date('d/m/Y', strtotime($emprestimo['data_emprestimo'])) ?>
        </p>
        <p><strong>Data de Devolução Prevista:</strong>
           <?= date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista'])) ?>
        </p>

        <h3>Livros Emprestados</h3>
        <ul>
            <?php foreach ($rows as $livro): ?>
                <li>
                  <?= htmlspecialchars($livro['nome_livro']) ?>
                  – Tombo: <?= htmlspecialchars($livro['tombo']) ?>
                  – ISBN: <?= htmlspecialchars($livro['isbn_falso']) ?>
                  – Qtde: <?= htmlspecialchars($livro['qntd_livros']) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="assinaturas">
            <div class="assinatura">
                <div class="linha"></div>
                <p>Assinatura do Aluno</p>
            </div>
            <div class="assinatura">
                <div class="linha"></div>
                <p>Assinatura do Bibliotecário</p>
            </div>
        </div>
    </div>
<?php endfor; ?>

</body>
</html>
