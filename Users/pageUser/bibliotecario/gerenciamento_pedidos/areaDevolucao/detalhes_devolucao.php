<?php
include_once("../../../../../conexao/conexao.php");
include_once("../../seguranca.php"); // verifica login + CSRF

// --------------------------------------------------------------------
// 1. Validação e sanitização do n_emprestimo
// --------------------------------------------------------------------
// --- MODIFICAÇÃO: Recebe n_emprestimo em vez de id ---
$n_emprestimo = isset($_GET["n_emprestimo"]) ? $_GET["n_emprestimo"] : null;

if ($n_emprestimo === null) {
    // Tenta pegar da sessão se não veio via GET (fallback, pode não ser ideal)
    // Ou redireciona se não encontrado
    // Por enquanto, vamos assumir que ele DEVE vir via GET
    $_SESSION["msg"] = "Número do grupo de empréstimo inválido ou não fornecido.";
    // Idealmente redirecionar para a lista de devoluções
    // header("Location: ../lista_devolucao.php"); 
    echo "<script>alert('Número do grupo de empréstimo inválido ou não fornecido.'); window.history.back();</script>";
    exit();
}

// --------------------------------------------------------------------
// 2. Busca dos dados de TODOS os itens para este n_emprestimo
//    Vamos buscar da tbemprestimos, pois ela terá o status de devolução atualizado
// --------------------------------------------------------------------
$sql_itens = "SELECT 
                    id_emprestimo, n_emprestimo, ra_aluno, nome_aluno, 
                    isbn_falso, isbn, tombo, nome_livro, 
                    data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva, 
                    emprestado_por, id_bibliotecario, tipo
                FROM tbemprestimos 
                WHERE n_emprestimo = ? 
                ORDER BY data_devolucao_efetiva ASC, nome_livro, tombo"; // Ordena por status e nome

$stmt_itens = $conn->prepare($sql_itens);
if (!$stmt_itens) {
    echo "Erro ao preparar consulta de itens: " . $conn->error;
    exit();
}

$stmt_itens->bind_param("s", $n_emprestimo); // Assumindo que n_emprestimo pode ser string ou int
$stmt_itens->execute();
$result_itens = $stmt_itens->get_result();

if ($result_itens->num_rows === 0) {
    echo "<script>alert('Nenhum item encontrado para este número de empréstimo.'); window.history.back();</script>";
    exit();
}

// Pega todos os itens do empréstimo
$itens_emprestimo = $result_itens->fetch_all(MYSQLI_ASSOC);
$stmt_itens->close();

// Pega os dados gerais do primeiro item (serão iguais para todos no grupo)
$dados_gerais = $itens_emprestimo[0];

// Verifica se todos os itens foram devolvidos
$todos_devolvidos = true;
foreach ($itens_emprestimo as $item_check) {
    if (empty($item_check["data_devolucao_efetiva"])) {
        $todos_devolvidos = false;
        break;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Devolução - Empréstimo Nº <?= htmlspecialchars($dados_gerais["n_emprestimo"]) ?></title>
    <!-- Tailwind CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-4">Detalhes da Devolução - Empréstimo Nº: <?= htmlspecialchars($dados_gerais["n_emprestimo"]) ?></h1>

        <!-- Dados gerais -->
        <div class="mb-6 p-4 border rounded shadow-sm bg-gray-50">
            <h2 class="text-lg font-semibold mb-2">Informações Gerais</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1 text-sm">
                <div><strong>RA do Aluno:</strong> <?= htmlspecialchars($dados_gerais["ra_aluno"]) ?></div>
                <div><strong>Nome do Aluno:</strong> <?= htmlspecialchars($dados_gerais["nome_aluno"]) ?></div>
                <div><strong>Data do Empréstimo:</strong> <?= date("d/m/Y H:i", strtotime($dados_gerais["data_emprestimo"])) ?></div>
                <div><strong>Emprestado por:</strong> <?= htmlspecialchars($dados_gerais["emprestado_por"]) ?></div>
            </div>
        </div>

        <!-- Lista de livros devolvidos/pendentes -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Itens do Empréstimo (<?= count($itens_emprestimo) ?>)</h2>
            <div class="overflow-x-auto">
                <table class="table-auto border w-full text-sm shadow-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Título</th>
                            <th class="border px-4 py-2 text-left">Tombo</th>
                            <th class="border px-4 py-2 text-left">ISBN Falso</th>
                            <th class="border px-4 py-2 text-left">Devolução Prevista</th>
                            <th class="border px-4 py-2 text-left">Devolução Efetiva</th>
                            <!-- Remover coluna de ações daqui se a devolução for feita em outra tela -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_emprestimo as $item): ?>
                        <?php 
                            $devolvido = !empty($item["data_devolucao_efetiva"]);
                            $data_devolucao_prev_formatada = date("d/m/Y", strtotime($item["data_devolucao_prevista"]));
                            $data_devolucao_efetiva_formatada = $devolvido ? date("d/m/Y H:i", strtotime($item["data_devolucao_efetiva"])) : "Pendente";
                        ?>
                        <tr class="<?= $devolvido ? "bg-green-50" : "" ?>">
                            <td class="border px-4 py-2"><?= htmlspecialchars($item["nome_livro"]) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($item["tombo"]) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($item["isbn_falso"]) ?></td>
                            <td class="border px-4 py-2"><?= $data_devolucao_prev_formatada ?></td>
                            <td class="border px-4 py-2 <?= $devolvido ? "text-green-700 font-semibold" : "text-red-600" ?>"><?= $data_devolucao_efetiva_formatada ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex gap-2">
            <button onclick="window.history.back()"
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                Voltar
            </button>
            
            <?php if (!$todos_devolvidos): ?>
                <!-- Botão para ir para a tela de realizar devolução (se for separada) -->
                <!-- Ou manter os botões individuais de devolução/adiar como no detalhes_emprestimo.php -->
                <!-- Exemplo: -->
                 <button onclick="window.location.href=\"../areaEmprestimo/detalhes_emprestimo.php?n_emprestimo=<?= urlencode($n_emprestimo) ?>\""
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Gerenciar Devoluções/Adiamentos
                </button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

