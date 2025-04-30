<?php

ob_start();
date_default_timezone_set('America/Sao_Paulo');
include_once("../../../../conexao/conexao.php");

// Função para gerar o número de empréstimo (gera só 1 vez por carregamento)
function gerarNumeroEmprestimo($conn) {
    $row = $conn
        ->query("SELECT MAX(n_emprestimo) AS ultimo FROM tbemprestimos")
        ->fetch_assoc();
    $ultInt = (int) substr($row['ultimo'], -5);
    $novo   = str_pad($ultInt + 1, 5, "0", STR_PAD_LEFT);
    return "emp-" . date("d-m-y") . "-np" . $novo;
}

// Gera o número que será exibido e usado no INSERT e no redirect
$pseudoNumero = gerarNumeroEmprestimo($conn);

// Pega usuário logado (se tiver)
$usuarioLogado = $_SESSION['usuario'] ?? 'Desconhecido';

// Processa o POST quando o form for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura as datas
  

    // Dados do aluno e lista de livros
    $raAluno     = $_SESSION['aluno']['ra_aluno'];
    $nomeAluno   = $_SESSION['aluno']['nome'];
    $livros      = $_SESSION['livros'];
    $nEmprestimo = $pseudoNumero;     // usa sempre o mesmo gerado acima
    $tipo        = 'emprestado';

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("
            INSERT INTO tbemprestimos
              (n_emprestimo,
               ra_aluno,
               nome_aluno,
               isbn_falso,
               isbn,
               nome_livro,
               qntd_livros,
               data_emprestimo,
               data_devolucao_prevista,
               tipo)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");

        foreach ($livros as $livro) {
            $isbn_falso  = $livro['isbn_falso']  ?? '';
            $isbn        = $livro['isbn']        ?? '';
            $nomeLivro   = $livro['titulo']      ?? '';
            $quantidade  = $livro['quantidade']  ?? 1;

            $stmt->bind_param(
                'ssssssssss',
                $nEmprestimo,
                $raAluno,
                $nomeAluno,
                $isbn_falso,
                $isbn,
                $nomeLivro,
                $quantidade,
                $dataEmprestimo,
                $dataDevolucaoPrevista,
                $tipo
            );
            $stmt->execute();
        }

        $conn->commit();

     

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Erro ao realizar o empréstimo: " . addslashes($e->getMessage()) . "');
              </script>";
    }
}
?>

<!-- Formulário HTML -->
<h2>Confirmar Empréstimo</h2>
<form action="" method="post">
    <div class="dadosAlunos">
        <h3>Aluno Selecionado</h3>
        <?php if (isset($_SESSION['aluno'])): ?>
            <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['aluno']['nome']) ?></p>
            <p><strong>RA:</strong> <?= htmlspecialchars($_SESSION['aluno']['ra_aluno']) ?></p>
        <?php else: ?>
            <p>Aluno não selecionado.</p>
        <?php endif; ?>
    </div>
    <hr>
    <div class="dadosLivro">
        <h3>Livros Selecionados</h3>
        <?php if (!empty($_SESSION['livros'])): ?>
            <?php foreach ($_SESSION['livros'] as $livro): ?>
                <p><strong>Título:</strong> <?= htmlspecialchars($livro['titulo']) ?></p>
                <p><strong>Tombo:</strong> <?= htmlspecialchars($livro['tombo']) ?></p>
                <p><strong>Autor:</strong> <?= htmlspecialchars($livro['autor']) ?></p>
                <p><strong>Editora:</strong> <?= htmlspecialchars($livro['editora']) ?></p>
                <p><strong>ISBN Falso:</strong> <?= htmlspecialchars($livro['isbn_falso']) ?></p>
                <p><strong>Quantidade Disponível:</strong> <?= htmlspecialchars($livro['quantidade']) ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum livro selecionado.</p>
        <?php endif; ?>
    </div>

    <!-- Número do Empréstimo -->
    <p><strong>Número do Empréstimo:</strong> <?= htmlspecialchars($pseudoNumero) ?></p>

    <!-- Autorizado por -->
    <p><strong>Autorizado por:</strong> <?= htmlspecialchars($usuarioLogado) ?></p>

    <!-- Datas -->
    <label for="data_emprestimo">Data do Empréstimo:</label>
    <input type="date" id="data_emprestimo" name="data_emprestimo"
           value="<?= date('Y-m-d') ?>" readonly required><br><br>

    <label for="data_devolucao">Data da Devolução:</label>
    <input type="date" id="data_devolucao" name="data_devolucao" required><br><br>

    <button type="submit" name="confirmarEmprestimo" value="confirmarEmprestimo">
        Confirmar Empréstimo
    </button>
    <button type="button" id="cancelarEmprestimo">Cancelar</button>
</form>

<script>
document.getElementById("cancelarEmprestimo").addEventListener("click", function() {
    if (confirm("Tem certeza que deseja cancelar o empréstimo?")) {
        window.location.href = "cancelar_emprestimo.php";
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const dataEmp = document.getElementById("data_emprestimo");
    const dataDev = document.getElementById("data_devolucao");

    function ajustarDataDevolucao(d) {
        const dia = d.getDay();
        if (dia === 6) d.setDate(d.getDate() - 1);
        if (dia === 0) d.setDate(d.getDate() + 1);
        return d;
    }

    function definirDataPadrao() {
        let d = new Date(dataEmp.value);
        d.setDate(d.getDate() + 7);
        dataDev.value = ajustarDataDevolucao(d).toISOString().split('T')[0];
    }

    definirDataPadrao();

    dataDev.addEventListener("change", () => {
        const emp = new Date(dataEmp.value);
        let dev = new Date(dataDev.value);
        if (dev < emp) {
            alert("Data de devolução não pode ser anterior à de empréstimo.");
            definirDataPadrao();
            return;
        }
        if ([0,6].includes(dev.getDay())) {
            alert("Ajustando para evitar fim de semana.");
            dataDev.value = ajustarDataDevolucao(dev).toISOString().split('T')[0];
        }
    });
});
</script>
