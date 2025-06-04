<?php
include_once("../../../../../conexao/conexao.php");
include_once("../../seguranca.php"); // Inclui segurança

// Verifica se o n_emprestimo foi passado
if (!isset($_GET["n_emprestimo"]) || empty($_GET["n_emprestimo"])) {
    echo "<script>alert('Número do grupo de empréstimo não fornecido.'); window.history.back();</script>";
    exit();
}

$n_emprestimo = $_GET["n_emprestimo"]; // Usar como string ou int dependendo da coluna

// Busca os dados de todos os livros para este n_emprestimo
// Usando prepared statement para segurança
$sql = "SELECT 
            id_emprestimo, n_emprestimo, ra_aluno, nome_aluno, 
            isbn_falso, isbn, tombo, nome_livro, 
            data_emprestimo, data_devolucao_prevista, data_devolucao_efetiva, 
            emprestado_por, id_bibliotecario, tipo
        FROM tbemprestimos 
        WHERE n_emprestimo = ? 
        ORDER BY nome_livro, tombo"; // Ordena para melhor visualização

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Erro ao preparar consulta: " . $conn->error;
    exit();
}

$stmt->bind_param("s", $n_emprestimo); // Assumindo que n_emprestimo pode ser string ou int
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Nenhum empréstimo encontrado para este número.'); window.history.back();</script>";
    exit();
}

// Pega todos os itens do empréstimo
$itens_emprestimo = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Pega os dados gerais do primeiro item (serão iguais para todos no grupo)
$dados_gerais = $itens_emprestimo[0];
?>

<h1 class="text-2xl font-bold mb-6">Detalhes do Empréstimo Nº: <?= htmlspecialchars($dados_gerais["n_emprestimo"]) ?></h1>

<div class="mb-6 p-4 border rounded shadow-sm bg-gray-50">
    <h2 class="text-lg font-semibold mb-2">Informações Gerais</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1 text-sm">
        <div><strong>RA do Aluno:</strong> <?= htmlspecialchars($dados_gerais["ra_aluno"]) ?></div>
        <div><strong>Nome do Aluno:</strong> <?= htmlspecialchars($dados_gerais["nome_aluno"]) ?></div>
        <div><strong>Data do Empréstimo:</strong> <?= date("d/m/Y H:i", strtotime($dados_gerais["data_emprestimo"])) ?></div>
        <div><strong>Emprestado por:</strong> <?= htmlspecialchars($dados_gerais["emprestado_por"]) ?></div>
    </div>
</div>

<div class="mb-6">
    <h2 class="text-lg font-semibold mb-2">Livros Emprestados (<?= count($itens_emprestimo) ?>)</h2>
    <div class="overflow-x-auto">
        <table class="table-auto border w-full text-sm shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Título</th>
                    <th class="border px-4 py-2 text-left">Tombo</th>
                    <th class="border px-4 py-2 text-left">ISBN Falso</th>
                    <th class="border px-4 py-2 text-left">Devolução Prevista</th>
                    <th class="border px-4 py-2 text-left">Devolução Efetiva</th>
                    <th class="border px-4 py-2 text-center">Ações</th> 
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
                    <td class="border px-4 py-2 text-center">
                        <?php if (!$devolvido): ?>
                            <!-- Botão Devolução Individual -->
                            <button 
                                type="button"
                                onclick="abrirModalDevolucao(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" 
                                class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs mr-1 mb-1"
                                title="Registrar Devolução deste item"
                            >
                                Devolver
                            </button>
                            
                            <!-- Botão Adiar Individual -->
                             <button 
                                type="button"
                                onclick="abrirModalAdiar(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" 
                                class="btn-abrir-modal bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-xs mb-1"
                                title="Adiar Devolução deste item"
                            >
                                Adiar
                            </button>
                        <?php else: ?>
                            <span class="text-gray-500 text-xs">Devolvido</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Botão Voltar -->
<button onclick="window.location.href='../gerenciamento.php';" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
    Voltar
</button>

<!-- MODAL DE CONFIRMAÇÃO DE DEVOLUÇÃO (será preenchido via JS) -->
<div id="modalConfirmarDevolucao" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h2 class="text-xl font-bold mb-4">Confirmar Devolução do Item</h2>
        <p class="mb-4">Tem certeza que deseja registrar a devolução do seguinte item?</p>
        <div id="detalhesDevolucaoModal" class="mb-4 text-sm space-y-1"></div>
        
        <form id="formDevolucao" action="realizar_devolucao.php" method="POST">
            <input type="hidden" name="id_emprestimo_item" id="id_emprestimo_item_modal">
            <input type="hidden" name="tombo_item" id="tombo_item_modal">
            <input type="hidden" name="isbn_falso_item" id="isbn_falso_item_modal">
            <!-- Adicionar token CSRF se estiver usando -->
            
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-fechar-modal px-4 py-2 rounded bg-gray-300 hover:bg-gray-400" onclick="fecharModal("modalConfirmarDevolucao")">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    Confirmar Devolução
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADIAR EMPRÉSTIMO (será preenchido via JS) -->
<div id="modalAdiarEmprestimo" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h2 class="text-xl font-bold mb-4">Adiar Devolução do Item</h2>
         <div id="detalhesAdiarModal" class="mb-4 text-sm space-y-1"></div>
        <form id="formAdiar" action="adiar_emprestimo.php" method="POST" class="space-y-4">
            <input type="hidden" name="id_emprestimo_item_adiar" id="id_emprestimo_item_adiar_modal">
            <input type="hidden" name="tombo_item_adiar" id="tombo_item_adiar_modal">
            <!-- Adicionar token CSRF se estiver usando -->

            <div>
                <label for="nova_data_adiar" class="block text-sm font-semibold mb-1">Nova Data de Devolução</label>
                <input type="date" id="nova_data_adiar" name="nova_data" required class="border px-3 py-2 rounded w-full">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn-fechar-modal px-4 py-2 rounded bg-gray-300 hover:bg-gray-400" onclick="fecharModal("modalAdiarEmprestimo")">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 rounded bg-yellow-600 text-white hover:bg-yellow-700">
                    Confirmar Adiamento
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Tailwind (temporário para teste) -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<!-- Estilos básicos para Modal -->
<style>
    .modal.hidden {
        display: none;
    }
</style>

<!-- JavaScript para Modais -->
<script>
function fecharModal(modalId) {
    document.getElementById(modalId).classList.add("hidden");
}

function abrirModalDevolucao(item) {
    // Preenche os detalhes no modal
    const detalhesDiv = document.getElementById("detalhesDevolucaoModal");
    detalhesDiv.innerHTML = `
        <p><strong>Título:</strong> ${item.nome_livro || "N/A"}</p>
        <p><strong>Tombo:</strong> ${item.tombo || "N/A"}</p>
        <p><strong>Devolução Prevista:</strong> ${new Date(item.data_devolucao_prevista + "T00:00:00").toLocaleDateString("pt-BR")}</p>
    `;
    
    // Preenche os campos hidden do formulário
    document.getElementById("id_emprestimo_item_modal").value = item.id_emprestimo;
    document.getElementById("tombo_item_modal").value = item.tombo;
    document.getElementById("isbn_falso_item_modal").value = item.isbn_falso;

    // Mostra o modal
    document.getElementById("modalConfirmarDevolucao").classList.remove("hidden");
}

function abrirModalAdiar(item) {
    // Preenche os detalhes no modal
    const detalhesDiv = document.getElementById("detalhesAdiarModal");
     detalhesDiv.innerHTML = `
        <p><strong>Título:</strong> ${item.nome_livro || "N/A"}</p>
        <p><strong>Tombo:</strong> ${item.tombo || "N/A"}</p>
        <p><strong>Devolução Prevista Atual:</strong> ${new Date(item.data_devolucao_prevista + "T00:00:00").toLocaleDateString("pt-BR")}</p>
    `;
    
    // Preenche os campos hidden do formulário
    document.getElementById("id_emprestimo_item_adiar_modal").value = item.id_emprestimo;
    document.getElementById("tombo_item_adiar_modal").value = item.tombo;
    
    // Define a data mínima para o input date (dia seguinte à data prevista atual)
    const dataPrevista = new Date(item.data_devolucao_prevista + "T00:00:00");
    dataPrevista.setDate(dataPrevista.getDate() + 1);
    const minDate = dataPrevista.toISOString().split("T")[0];
    document.getElementById("nova_data_adiar").min = minDate;
    document.getElementById("nova_data_adiar").value = minDate; // Sugere o dia seguinte

    // Mostra o modal
    document.getElementById("modalAdiarEmprestimo").classList.remove("hidden");
}

// Adiciona event listener para fechar modais clicando fora ou no botão fechar
document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", (event) => {
        // Fecha se clicar no fundo escuro (o próprio modal)
        if (event.target === modal) {
            fecharModal(modal.id);
        }
    });
});
document.querySelectorAll(".btn-fechar-modal").forEach(button => {
    button.addEventListener("click", (event) => {
        const modal = event.target.closest(".modal");
        if (modal) {
            fecharModal(modal.id);
        }
    });
});

</script>

