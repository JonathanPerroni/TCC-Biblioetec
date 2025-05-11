<?php
include_once("../../../../../conexao/conexao.php");
include_once('../../seguranca.php'); // já verifica login e carrega CSRF

$search = isset($_GET['query']) ? trim($_GET['query']) : '';
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
if ($search !== '') {
    $query = "SELECT id_devolucao, n_emprestimo, ra_aluno, nome_aluno, data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva 
              FROM tbdevolucao 
              WHERE n_emprestimo LIKE '%$search%' 
                 OR ra_aluno LIKE '%$search%' 
                 OR nome_aluno LIKE '%$search%'
              ORDER BY data_emprestimo DESC";

    $result = $conn->query($query);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $id = $row['id_devolucao'];
        echo "<script>window.location.href = 'areaDevolucao/detalhes_devolucao.php?id=$id';</script>";
        exit;
    } elseif ($result->num_rows === 0) {
        echo "<script>alert('Nenhuma devolução encontrada.');</script>";
    }
}
?>

<?php
if ($search === '' || ($result && $result->num_rows > 1)) {

    if (!isset($result)) {
        $query = "SELECT id_devolucao, n_emprestimo, ra_aluno, nome_aluno, data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva 
                  FROM tbdevolucao 
                  ORDER BY data_emprestimo DESC";
        $result = $conn->query($query);
    }

    echo "<table class='table-auto w-full border text-sm'>
            <thead class='bg-gray-100'>
                <tr>
                    <th class='border px-4 py-2'>Número Empréstimo</th>
                    <th class='border px-4 py-2'>RA Aluno</th>
                    <th class='border px-4 py-2'>Nome do Aluno</th>
                    <th class='border px-4 py-2'>Data Empréstimo</th>
                    <th class='border px-4 py-2'>Prevista</th>
                    <th class='border px-4 py-2'>Efetiva</th>
                    <th class='border px-4 py-2'>Ações</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $result->fetch_assoc()) {
        $id = $row['id_devolucao'];
        echo "<tr>
                <td class='border px-4 py-2'>{$row['n_emprestimo']}</td>
                <td class='border px-4 py-2'>{$row['ra_aluno']}</td>
                <td class='border px-4 py-2'>{$row['nome_aluno']}</td>
                <td class='border px-4 py-2'>{$row['data_emprestimo']}</td>
                <td class='border px-4 py-2'>{$row['data_devolucao_prevista']}</td>
                <td class='border px-4 py-2'>{$row['data_devolucao_efetiva']}</td>
                <td class='border px-4 py-2'>
                    <button class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600' 
                            onclick=\"window.location.href='areaDevolucao/detalhes_devolucao.php?id=$id'\">
                        Detalhes
                    </button>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
}
?>
