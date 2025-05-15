<?php
include_once("../../../../../conexao/conexao.php");
include_once('../../seguranca.php'); // verifica login + CSRF

// --------------------------------------------------------------------
// 1. Validação e sanitização do ID
// --------------------------------------------------------------------
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    $_SESSION['msg'] = "ID da devolução inválido.";
    header("Location: ../lista_devolucao.php");
    exit;
}

// --------------------------------------------------------------------
// 2. Busca dos dados da devolução
// --------------------------------------------------------------------
$sql  = "SELECT id_devolucao,
                n_emprestimo,
                ra_aluno,
                nome_aluno,
                data_emprestimo,
                data_devolucao_prevista,
                data_devolucao_efetiva
         FROM tbdevolucao
         WHERE id_devolucao = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['msg'] = "Devolução não encontrada.";
    header("Location: ../lista_devolucao.php");
    exit;
}

$devolucao = $result->fetch_assoc();

// --------------------------------------------------------------------
// 3. (Opcional) Livros desse empréstimo
// --------------------------------------------------------------------
$sqlLivros = "SELECT nome_livro AS titulo, isbn, qntd_livros AS quantidade
              FROM tbemprestimos
              WHERE n_emprestimo = ?";
$stmtLivros = $conn->prepare($sqlLivros);
$stmtLivros->bind_param("s", $devolucao['n_emprestimo']);
$stmtLivros->execute();
$livros = $stmtLivros->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Devolução</title>
    <!-- Tailwind CDN apenas para prototipagem.
         Em produção, use build próprio para performance -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-4">Detalhes da Devolução</h1>

        <!-- Dados principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div><span class="font-medium">Número Empréstimo:</span> <?= htmlspecialchars($devolucao['n_emprestimo']) ?></div>
            <div><span class="font-medium">RA Aluno:</span> <?= htmlspecialchars($devolucao['ra_aluno']) ?></div>
            <div><span class="font-medium">Nome do Aluno:</span> <?= htmlspecialchars($devolucao['nome_aluno']) ?></div>
            <div><span class="font-medium">Data Empréstimo:</span>
                <?= htmlspecialchars(date('d/m/Y', strtotime($devolucao['data_emprestimo']))) ?>
            </div>
            <div><span class="font-medium">Devolução Prevista:</span>
                <?= htmlspecialchars(date('d/m/Y', strtotime($devolucao['data_devolucao_prevista']))) ?>
            </div>
            <div><span class="font-medium">Devolução Efetiva:</span>
                <?= $devolucao['data_devolucao_efetiva']
                    ? htmlspecialchars(date('d/m/Y', strtotime($devolucao['data_devolucao_efetiva'])))
                    : '<span class="text-red-600">— Pend. —</span>' ?>
            </div>
        </div>

        <!-- Lista de livros -->
        <?php if ($livros->num_rows > 0): ?>
            <h2 class="text-xl font-semibold mb-2">Livros Emprestados</h2>
            <table class="table-auto w-full border text-sm mb-6">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Título</th>
                        <th class="border px-4 py-2">ISBN</th>
                        <th class="border px-4 py-2">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($livro = $livros->fetch_assoc()): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= htmlspecialchars($livro['titulo']) ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($livro['isbn']) ?></td>
                        <td class="border px-4 py-2 text-center"><?= htmlspecialchars($livro['quantidade']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Ações -->
        <div class="flex gap-2">
            <button onclick="window.history.back()"
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                Voltar
            </button>

            <?php if (!$devolucao['data_devolucao_efetiva']): ?>
                <form method="POST" action="processa_devolucao.php" class="inline">
                    <input type="hidden" name="id_devolucao" value="<?= $devolucao['id_devolucao'] ?>">
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                            onclick="return confirm('Confirmar devolução?');">
                        Registrar Devolução
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
