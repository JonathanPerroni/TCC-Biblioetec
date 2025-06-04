<?php
include_once("../../../../../conexao/conexao.php");
include_once("../../seguranca.php"); // já verifica login e carrega CSRF

$search = isset($_GET["query"]) ? trim($_GET["query"]) : "";
?>

<!-- Formulário de Pesquisa -->
<form method="GET" class="mb-4">
    <div class="flex gap-2">
        <input 
            type="text" 
            name="query" 
            placeholder="Buscar por número, RA ou nome do aluno..." 
            class="border rounded px-4 py-2 w-full"
            value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Buscar
        </button>
    </div>
</form>

<?php
// --- MODIFICAÇÃO: Consulta principal agrupada por n_emprestimo ---
// Assumindo que tbdevolucao também terá múltiplas linhas por n_emprestimo se a devolução for item a item
// Ou se tbdevolucao for um resumo, ajustar a query conforme a estrutura real.
// ESTA QUERY ASSUME QUE tbdevolucao PODE TER MÚLTIPLAS LINHAS POR n_emprestimo
$base_query = "SELECT 
                    n_emprestimo, 
                    ra_aluno, 
                    nome_aluno, 
                    MIN(data_emprestimo) as data_primeiro_emprestimo, 
                    MAX(data_devolucao_efetiva) as data_ultima_devolucao, 
                    COUNT(*) as total_itens_devolvidos 
                FROM tbdevolucao"; // Ajustar nome da tabela se necessário

$where_clause = "";
$params = [];
$types = "";

if ($search !== "") {
    $search_param = "%" . $search . "%";
    // Ajustar colunas de busca conforme a tabela tbdevolucao
    $where_clause = " WHERE n_emprestimo LIKE ? OR ra_aluno LIKE ? OR nome_aluno LIKE ?"; 
    $params = [$search_param, $search_param, $search_param];
    $types = "sss";
}

$group_by_clause = " GROUP BY n_emprestimo, ra_aluno, nome_aluno";
$order_by_clause = " ORDER BY MAX(data_devolucao_efetiva) DESC"; // Ordena pela devolução mais recente

$final_query = $base_query . $where_clause . $group_by_clause . $order_by_clause;

$stmt = $conn->prepare($final_query);

if (!$stmt) {
    echo "Erro ao preparar consulta de devoluções: " . $conn->error;
    // Lidar com o erro apropriadamente
} else {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($search !== "" && $result->num_rows === 0) {
        echo "<div class=\"bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative\" role=\"alert\">
                <strong class=\"font-bold\">Aviso!</strong>
                <span class=\"block sm:inline\"> Nenhuma devolução encontrada para \"" . htmlspecialchars($search) . "\".</span>
              </div>";
    }

    // Exibe tabela se houver resultados ou se não houver busca
    if ($result && $result->num_rows > 0) {
        echo "<table class=\"table-auto w-full border text-sm\">
                <thead class=\"bg-gray-100\">
                    <tr>
                        <th class=\"border px-4 py-2\">Nº Empréstimo</th>
                        <th class=\"border px-4 py-2\">RA Aluno</th>
                        <th class=\"border px-4 py-2\">Nome do Aluno</th>
                        <th class=\"border px-4 py-2\">Data Primeiro Empréstimo</th>
                        <th class=\"border px-4 py-2\">Data Última Devolução</th>
                        <th class=\"border px-4 py-2\">Qtd. Itens Devolvidos</th>
                        <th class=\"border px-4 py-2\">Ações</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            // --- MODIFICAÇÃO: Usa n_emprestimo para o link de detalhes ---
            $n_emprestimo_link = $row["n_emprestimo"];
            $data_primeiro_emp_formatada = $row["data_primeiro_emprestimo"] ? date("d/m/Y", strtotime($row["data_primeiro_emprestimo"])) : "N/A";
            $data_ultima_dev_formatada = $row["data_ultima_devolucao"] ? date("d/m/Y H:i", strtotime($row["data_ultima_devolucao"])) : "N/A";

            echo "<tr>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["n_emprestimo"]) . "</td>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["ra_aluno"]) . "</td>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["nome_aluno"]) . "</td>
                    <td class=\"border px-4 py-2\">" . $data_primeiro_emp_formatada . "</td>
                    <td class=\"border px-4 py-2\">" . $data_ultima_dev_formatada . "</td>
                    <td class=\"border px-4 py-2 text-center\">" . htmlspecialchars($row["total_itens_devolvidos"]) . "</td>
                    <td class=\"border px-4 py-2\">
                        <button class=\"bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600\" 
                                onclick=\"window.location.href=\"areaDevolucao/detalhes_devolucao.php?n_emprestimo=" . urlencode($n_emprestimo_link) . "\"\">
                            Detalhes
                        </button>
                    </td>
                  </tr>";
        }

        echo "</tbody></table>";
        $stmt->close();
    } elseif ($search === "") {
         // Mensagem se não houver busca e nenhuma devolução encontrada
         echo "<div class=\"bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative\" role=\"alert\">
                <span class=\"block sm:inline\"> Nenhuma devolução registrada encontrada.</span>
              </div>";
    }
}
?>
