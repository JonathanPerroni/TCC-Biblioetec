<?php
include_once("../../../../../conexao/conexao.php");

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Empréstimo não encontrado.'); window.history.back();</script>";
    exit;
}

$id = intval($_GET['id']);

// Busca os dados do empréstimo
$query = "SELECT * FROM tbemprestimos WHERE id_emprestimo = $id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "<script>alert('Empréstimo não encontrado.'); window.history.back();</script>";
    exit;
}

$emprestimo = $result->fetch_assoc();
?>

<h1 class="text-xl font-bold mb-4">Detalhes do Empréstimo</h1>

<table class="table-auto border w-full text-sm mb-6">
    <tbody>
        <tr><td class="border px-4 py-2 font-semibold">Número do Empréstimo:</td><td class="border px-4 py-2"><?= $emprestimo['n_emprestimo'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">RA do Aluno:</td><td class="border px-4 py-2"><?= $emprestimo['ra_aluno'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Nome do Aluno:</td><td class="border px-4 py-2"><?= $emprestimo['nome_aluno'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Nome do Livro:</td><td class="border px-4 py-2"><?= $emprestimo['nome_livro'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">ISBN:</td><td class="border px-4 py-2"><?= $emprestimo['isbn'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">ISBN Falso:</td><td class="border px-4 py-2"><?= $emprestimo['isbn_falso'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Tombo:</td><td class="border px-4 py-2"><?= $emprestimo['tombo'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Quantidade de Livros:</td><td class="border px-4 py-2"><?= $emprestimo['qntd_livros'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Data do Empréstimo:</td><td class="border px-4 py-2"><?= $emprestimo['data_emprestimo'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Data Prevista de Devolução:</td><td class="border px-4 py-2"><?= $emprestimo['data_devolucao_prevista'] ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Data de Devolução Efetiva:</td><td class="border px-4 py-2"><?= $emprestimo['data_devolucao_efetiva'] ?: 'Ainda não devolvido' ?></td></tr>
        <tr><td class="border px-4 py-2 font-semibold">Tipo:</td><td class="border px-4 py-2"><?= $emprestimo['tipo'] ?></td></tr>
    </tbody>
</table>

<div class="flex gap-4">
    <!-- Botão Devolução -->
    <form action="realizar_devolucao.php" method="POST">
        <input type="hidden" name="id_emprestimo" value="<?= $emprestimo['id_emprestimo'] ?>">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Realizar Devolução
        </button>
    </form>
<!-- Modal Confirmação Devolução -->
<div id="modalConfirmarDevolucao" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h2 class="text-xl font-bold mb-4">Confirmar Devolução</h2>
        <p>Por favor, confirme os dados abaixo antes de realizar a devolução:</p>
        <table class="table-auto border w-full text-sm mb-6">
            <tbody>
                <tr><td class="border px-4 py-2 font-semibold">Número do Empréstimo:</td><td class="border px-4 py-2"><?= $emprestimo['n_emprestimo'] ?></td></tr>
                <tr><td class="border px-4 py-2 font-semibold">Nome do Aluno:</td><td class="border px-4 py-2"><?= $emprestimo['nome_aluno'] ?></td></tr>
                <tr><td class="border px-4 py-2 font-semibold">Nome do Livro:</td><td class="border px-4 py-2"><?= $emprestimo['nome_livro'] ?></td></tr>
                <tr><td class="border px-4 py-2 font-semibold">Data de Empréstimo:</td><td class="border px-4 py-2"><?= $emprestimo['data_emprestimo'] ?></td></tr>
                <tr><td class="border px-4 py-2 font-semibold">Data Prevista de Devolução:</td><td class="border px-4 py-2"><?= $emprestimo['data_devolucao_prevista'] ?></td></tr>
            </tbody>
        </table>
        
        <div class="flex justify-end gap-2">
            <button type="button" class="btn-fechar-modal px-4 py-2 rounded bg-gray-300 hover:bg-gray-400" onclick="fecharModal()">
                Cancelar
            </button>
            <form action="realizar_devolucao.php" method="POST">
                <input type="hidden" name="id_emprestimo" value="<?= $emprestimo['id_emprestimo'] ?>">
                <input type="hidden" name="n_emprestimo" value="<?= $emprestimo['n_emprestimo'] ?>">
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    Confirmar Devolução
                </button>
            </form>
        </div>
    </div>
</div>





  <!-- Botão Adiar -->
<button 
    type="button"
    data-modal-id="modalAdiar<?= $emprestimo['id_emprestimo'] ?>"
    class="btn-abrir-modal bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600"
>
    Adiar Empréstimo
</button>

<!-- Modal Adiar Empréstimo -->
<div id="modalAdiar<?= $emprestimo['id_emprestimo'] ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h2 class="text-xl font-bold mb-4">Adiar Empréstimo</h2>
        <form action="adiar_emprestimo.php" method="POST" class="space-y-4">
            <input type="hidden" name="id_emprestimo" value="<?= $emprestimo['id_emprestimo'] ?>">

            <div>
                <label class="block text-sm font-semibold mb-1">Data Atual Prevista</label>
                <input type="text" value="<?= date('Y-m-d') ?>" disabled class="border px-3 py-2 rounded w-full bg-gray-100">
            </div>

            <div>
                <label for="nova_data" class="block text-sm font-semibold mb-1">Nova Data de Devolução</label>
                <input type="date" name="nova_data" required class="border px-3 py-2 rounded w-full">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn-fechar-modal px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 rounded bg-yellow-600 text-white hover:bg-yellow-700">
                    Adiar
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Tailwind (temporário para teste) -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<!-- Força o modal a ser invisível inicialmente -->
<style>
    .hidden {
        display: none !important;
    }
</style>


<script src="./adiar.js"></script>