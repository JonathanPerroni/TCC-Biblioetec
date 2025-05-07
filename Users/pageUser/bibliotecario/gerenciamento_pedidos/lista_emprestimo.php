<?php

include_once("../../../../conexao/conexao.php");

$query = "SELECT id_emprestimo, n_emprestimo,  ra_aluno, nome_aluno, data_emprestimo, data_devolucao_prevista FROM tbemprestimos ORDER BY data_emprestimo DESC ";
$result = $conn->query($query);


echo "<table class='table-auto w-full border'>
        <thead>
            <tr>
                <th class='border px-4 py-2'>Numero Emprestimo</th>
                <th class='border px-4 py-2'>RA Aluno</th>
                <th class='border px-4 py-2'>Nome do Aluno</th>
                <th class='border px-4 py-2'>Data Emprestimo</th>
                <th class='border px-4 py-2'> Data de Devolução</th>
            </tr>
        </thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td class='border px-4 py-2'>{$row['n_emprestimo']}</td>
            <td class='border px-4 py-2'>{$row['ra_aluno']}</td>
            <td class='border px-4 py-2'>{$row['nome_aluno']}</td>
            <td class='border px-4 py-2'>{$row['data_emprestimo']}</td>
            <td class='border px-4 py-2'>{$row['data_devolucao_prevista']}</td>
        </tr>";
}

echo "</tbody></table>";


?>