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
$base_query = "SELECT 
                    e.n_emprestimo, 
                    e.ra_aluno, 
                    e.nome_aluno, 
                    MIN(e.data_emprestimo) as data_emprestimo, 
                    MAX(e.data_devolucao_prevista) as data_devolucao_prevista, 
                    COUNT(e.id_emprestimo) as total_livros_emprestimo 
                FROM tbemprestimos e"; // Removi alias 'a' desnecessário aqui se não usar dados do aluno

// --- INÍCIO DA MODIFICAÇÃO --- 
// Sempre começa filtrando por empréstimos ativos
$where_clause = " WHERE e.data_devolucao_efetiva IS NULL"; 
$params = [];
$types = "";

// Se houver busca, adiciona as condições com AND
if ($search !== "") {
    $search_param = "%" . $search . "%";
    // Adiciona parênteses para garantir a ordem correta com AND/OR
    $where_clause .= " AND (e.n_emprestimo LIKE ? OR e.ra_aluno LIKE ? OR e.nome_aluno LIKE ?)"; 
    $params = [$search_param, $search_param, $search_param];
    $types = "sss";
}
// --- FIM DA MODIFICAÇÃO ---

$group_by_clause = " GROUP BY n_emprestimo, ra_aluno, nome_aluno";
$order_by_clause = " ORDER BY MIN(data_emprestimo) DESC";

$final_query = $base_query . $where_clause . $group_by_clause . $order_by_clause;

$stmt = $conn->prepare($final_query);

if (!$stmt) {
    echo "Erro ao preparar consulta: " . $conn->error;
    // Lidar com o erro apropriadamente
} else {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // --- Lógica de redirecionamento removida, pois agora agrupamos ---
    // A busca agora sempre mostrará a tabela ou nada.
    if ($search !== "" && $result->num_rows === 0) {
        echo "<div class=\"bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative\" role=\"alert\">
                <strong class=\"font-bold\">Aviso!</strong>
                <span class=\"block sm:inline\"> Nenhum empréstimo encontrado para \"" . htmlspecialchars($search) . "\".</span>
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
                        <th class=\"border px-4 py-2\">Data Empréstimo</th>
                        <th class=\"border px-4 py-2\">Devolução Prevista</th>
                        <th class=\"border px-4 py-2\">Qtd. Livros</th>
                        <th class=\"border px-4 py-2\">Ações</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            // --- MODIFICAÇÃO: Usa n_emprestimo para o link de detalhes ---
            $n_emprestimo_link = $row["n_emprestimo"];
            $data_emprestimo_formatada = date("d/m/Y H:i", strtotime($row["data_emprestimo"]));
            $data_devolucao_formatada = date("d/m/Y", strtotime($row["data_devolucao_prevista"]));

            echo "<tr>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["n_emprestimo"]) . "</td>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["ra_aluno"]) . "</td>
                    <td class=\"border px-4 py-2\">" . htmlspecialchars($row["nome_aluno"]) . "</td>
                    <td class=\"border px-4 py-2\">" . $data_emprestimo_formatada . "</td>
                    <td class=\"border px-4 py-2\">" . $data_devolucao_formatada . "</td>
                    <td class=\"border px-4 py-2 text-center\">" . htmlspecialchars($row["total_livros_emprestimo"]) . "</td>
                    <td class=\"border px-4 py-2\">
                        <button class=\"bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600\" 
                                onclick=\"window.location.href='areaEmprestimo/detalhes_emprestimo.php?n_emprestimo=" . urlencode($n_emprestimo_link) . "'\">
                            Detalhes
                        </button>
                    </td>
                  </tr>";
        }

        echo "</tbody></table>";
        $stmt->close();
    } elseif ($search === "") {
         // Mensagem se não houver busca e nenhum empréstimo ativo
         echo "<div class=\"bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative\" role=\"alert\">
                <span class=\"block sm:inline\"> Nenhum empréstimo ativo encontrado.</span>
              </div>";
    }
}
?>
