<?php

ob_start();
date_default_timezone_set('America/Sao_Paulo');
include_once("../../../../conexao/conexao.php");
include_once('../seguranca.php');// já verifica login e carrega CSRF
$token_csrf = gerarTokenCSRF(); // usa token no formulário

// Adicione a função aqui:
function gerarNumeroEmprestimo($conn) {
    $stmt = $conn->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbemprestimos'");
    $row = $stmt->fetch_assoc();
    $proximoId = $row['AUTO_INCREMENT'] ?? rand(1000, 9999);
    return "np00" . $proximoId;
}



// Pega usuário logado (se tiver)
$usuarioLogado = $_SESSION['usuario'] ?? 'Desconhecido';

// Processa o POST quando o form for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    $livros = $_SESSION['livros'];
    $dataEmprestimo = $_POST['data_emprestimo'] ?? date('Y-m-d');
    $dataDevolucaoPrevista = $_POST['data_devolucao'] ?? date('Y-m-d', strtotime('+7 weekdays'));

    $raAluno     = $_SESSION['aluno']['ra_aluno'];
    $nomeAluno   = $_SESSION['aluno']['nome'];
    $tipo        = 'emprestado';

    foreach ($livros as $livro) {
        $isbn_falso = $livro['isbn_falso'];

        // Consulta quantidade total
        $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM tblivros WHERE isbn_falso = ?");
        $stmtTotal->bind_param("s", $isbn_falso);
        $stmtTotal->execute();
        $stmtTotal->bind_result($total);
        $stmtTotal->fetch();
        $stmtTotal->close();

        // Consulta quantidade emprestada
        $stmtEmprestados = $conn->prepare("SELECT COUNT(*) FROM tbemprestimos WHERE isbn_falso = ? AND data_devolucao_efetiva IS NULL");
        $stmtEmprestados->bind_param("s", $isbn_falso);
        $stmtEmprestados->execute();
        $stmtEmprestados->bind_result($emprestados);
        $stmtEmprestados->fetch();
        $stmtEmprestados->close();

        $disponiveis = max(0, $total - $emprestados);

        // Verifica se o bibliotecário autorizou manualmente
        $forcados = $_POST['forcar_emprestimo'] ?? [];
        $foiForcado = in_array($isbn_falso, $forcados);

        $quantidadeSolicitada = $livro['quantidade_solicitada'] ?? 1;

        if ($disponiveis < $quantidadeSolicitada && !$foiForcado) {
            $_SESSION['msg'] = "O livro \"{$livro['titulo']}\" não possui exemplares suficientes. Solicitado: $quantidadeSolicitada | Disponíveis: $disponiveis.";
            $_SESSION['etapa'] = 3;
            header("Location: emprestimo.php");
            exit;
        }
    }

    $conn->begin_transaction();
    try {
        // Primeiro insere o primeiro livro com n_emprestimo NULL
        $livro0 = $livros[0];

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
            VALUES (NULL,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            'sssssssss',
            $raAluno,
            $nomeAluno,
            $livro0['isbn_falso'],
            $livro0['isbn'],
            $livro0['titulo'],
            $livro0['quantidade_solicitada'],
            $dataEmprestimo,
            $dataDevolucaoPrevista,
            $tipo
        );

        $stmt->execute();
        $idEmprestimo = $conn->insert_id;
        $nEmprestimo = "np00" . $idEmprestimo;

        // Atualiza o primeiro com o número gerado
        $conn->query("UPDATE tbemprestimos SET n_emprestimo = '$nEmprestimo' WHERE id_emprestimo = $idEmprestimo");

        // Insere os demais livros
        if (count($livros) > 1) {
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

            for ($i = 1; $i < count($livros); $i++) {
                $livro = $livros[$i];

                $stmt->bind_param(
                    'ssssssssss',
                    $nEmprestimo,
                    $raAluno,
                    $nomeAluno,
                    $livro['isbn_falso'],
                    $livro['isbn'],
                    $livro['titulo'],
                    $livro['quantidade_solicitada'],
                    $dataEmprestimo,
                    $dataDevolucaoPrevista,
                    $tipo
                );
                $stmt->execute();
            }
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
<!-- Botão para voltar e cancelar -->
<a href="cancelar_emprestimo.php" class="btn btn-danger">Voltar para a página do bibliotecário</a>
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
            <p><strong>Disponíveis:</strong> <?= $disponiveis ?> livro<?= $disponiveis == 1 ? '' : 's' ?></p>


            <?php
            $isbn_falso = $livro['isbn_falso'];

            // Consulta total e emprestados
            $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM tblivros WHERE isbn_falso = ?");
            $stmtTotal->bind_param("s", $isbn_falso);
            $stmtTotal->execute();
            $stmtTotal->bind_result($total);
            $stmtTotal->fetch();
            $stmtTotal->close();

            $stmtEmp = $conn->prepare("SELECT COUNT(*) FROM tbemprestimos WHERE isbn_falso = ? AND data_devolucao_efetiva IS NULL");
            $stmtEmp->bind_param("s", $isbn_falso);
            $stmtEmp->execute();
            $stmtEmp->bind_result($emprestados);
            $stmtEmp->fetch();
            $stmtEmp->close();

            $disponiveis = max(0, $total - $emprestados);
            ?>

            <p><strong>Disponíveis:</strong> <?= $disponiveis ?></p>

            <?php if ($disponiveis <= 0): ?>
                <p style="color:red;"><strong>Nenhum exemplar disponível.</strong></p>
                <label>
                    <input type="checkbox" name="forcar_emprestimo[]" value="<?= htmlspecialchars($livro['isbn_falso']) ?>">
                    Autorizar empréstimo mesmo sem disponibilidade
                </label>
            <?php elseif ($disponiveis == 1): ?>
                <p style="color:orange;"><strong>Apenas 1 exemplar disponível.</strong></p>
            <?php endif; ?>

            <hr>
        <?php endforeach; ?>

        <?php else: ?>
            <p>Nenhum livro selecionado.</p>
        <?php endif; ?>
    </div>

    <!-- Número do Empréstimo -->
<?php
$numeroEmprestimoHTML = $_SESSION['numero_emprestimo'] ?? gerarNumeroEmprestimo($conn);
?>
<p><strong>Número do Empréstimo:</strong> <?= htmlspecialchars($numeroEmprestimoHTML) ?></p>
<input type="hidden" name="nEmprestimo" value="<?= htmlspecialchars($numeroEmprestimoHTML) ?>
    <!-- Autorizado por -->
    <p><strong>Autorizado por:</strong> <?= htmlspecialchars($usuarioLogado) ?></p>

    <!-- Datas -->
    <label for="data_emprestimo">Data do Empréstimo:</label>
    <input type="date" id="data_emprestimo" name="data_emprestimo"
           value="<?= date('Y-m-d') ?>" readonly required><br><br>

    <label for="data_devolucao">Data da Devolução:</label>
    <input type="date" id="data_devolucao" name="data_devolucao" required><br><br>

    <!-- Campo oculto para o número do empréstimo -->
    <input type="hidden" name="nEmprestimo" value="<?= htmlspecialchars($numeroEmprestimo) ?>">

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
