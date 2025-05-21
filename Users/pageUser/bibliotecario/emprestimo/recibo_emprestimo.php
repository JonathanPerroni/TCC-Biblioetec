<?php
session_start();
ob_start();
date_default_timezone_set('America/Sao_Paulo');
include_once("../../../../conexao/conexao.php");

echo "Recebido via GET: " . htmlspecialchars($nEmprestimo) . "<br>";



// DEBUG: mostre o que chegou em GET['n']
if (!isset($_GET['n']) || empty(trim($_GET['n']))) {
    echo "Número de empréstimo não informado.";
    exit;
}
$nEmprestimo = preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['n']);

/*
echo "Recebido via GET: " . $nEmprestimo . "<br>";

// E opcionalmente veja os registros:
$resultDebug = $conn->query("SELECT n_emprestimo FROM tbemprestimos ORDER BY id_emprestimo DESC LIMIT 5");
while ($row = $resultDebug->fetch_assoc()) {
    echo "No banco: " . $row['n_emprestimo'] . "<br>";
}
exit;
*/
// Agora sua query normal
$stmt = $conn->prepare("
    SELECT *
    FROM tbemprestimos
    WHERE n_emprestimo = ?
    ORDER BY id_emprestimo
");

$resultDebug = $conn->query("SELECT n_emprestimo FROM tbemprestimos ORDER BY id_emprestimo DESC LIMIT 5");
while ($row = $resultDebug->fetch_assoc()) {
    echo "No banco: " . $row['n_emprestimo'] . "<br>";
}

$stmt->bind_param("s", $nEmprestimo);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);



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
