<?php
session_start();

require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');

if (!isset($_SESSION['aluno_emprestimo']) || !isset($_SESSION['livros_emprestimo'])) {
    $_SESSION['msg'] = "Dados do empréstimo não encontrados!";
    $_SESSION['msg_type'] = "danger";
    header('Location: ../aluno/pesquisa_aluno.php');
    exit;
}

$email_sessao = $_SESSION['email'];

// Consulta o nome do bibliotecário
$sql = "SELECT nome FROM tbbibliotecario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_sessao);
$stmt->execute();
$result = $stmt->get_result();
$bibliotecario = $result->fetch_assoc();



$aluno = $_SESSION['aluno_emprestimo'];
$livros = $_SESSION['livros_emprestimo'];


// Consulta dados completos do aluno
$sql_aluno = "SELECT * FROM tbalunos WHERE codigo = ?";
$stmt = $conn->prepare($sql_aluno);
$stmt->bind_param("i", $aluno['codigo']);
$stmt->execute();
$aluno_completo = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .livro-card { border-left: 4px solid #0d6efd; margin-bottom: 15px; }
        .header-confirmacao { background-color: #198754; color: white; }
    </style>
</head> 
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header header-confirmacao">
                <h4>Confirmação de Empréstimo</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Dados do Aluno</h5>
                        <table class="table table-bordered">
                            <tr><th>Nome</th><td><?= htmlspecialchars($aluno_completo['nome']) ?></td></tr>
                            <tr><th>RA</th><td><?= htmlspecialchars($aluno_completo['ra_aluno']) ?></td></tr>
                            <tr><th>Curso</th><td><?= htmlspecialchars($aluno_completo['nome_curso']) ?></td></tr>
                            <tr><th>Período</th><td><?= htmlspecialchars($aluno_completo['periodo']) ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Responsável</h5>
                        <table class="table table-bordered">
                            <tr><th>Bibliotecário</th><td><?= htmlspecialchars($bibliotecario['nome']) ?></td></tr>
                            <tr><th>Data</th><td><?= date('d/m/Y H:i') ?></td></tr>
                        </table>
                    </div>
                </div>

                <h5 class="mt-4">Livros Selecionados</h5>
                <?php foreach($livros as $index => $livro): ?>
                <div class="card livro-card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><?= htmlspecialchars($livro['titulo']) ?></h6>
                                <p><strong>Autor:</strong> <?= htmlspecialchars($livro['autor']) ?></p>
                                <p><strong>ISBN Falso:</strong> <?= htmlspecialchars($livro['isbn_falso']) ?></p>
                               <p><strong>Tombo:</strong> <?= isset($livro['tombo']) ? htmlspecialchars($livro['tombo']) : 'Não informado' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Data Devolução:</strong> <?= date('d/m/Y', strtotime($livro['data_devolucao'])) ?></p>
                                <p><strong>Disponíveis:</strong> <?= isset($livro['disponiveis']) ? $livro['disponiveis'] : 0 ?> de <?= $livro['total_exemplares'] ?></p>
                                <?php if($livro['sobrepor_regra']): ?>
                                <span class="badge bg-warning">Regra de 1 exemplar sobrescrita</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-between mt-4">
                    <a href="../livro/pesquisa_livro.php" class="btn btn-secondary">Corrigir</a>
                    <button id="btn-confirmar" class="btn btn-success">Confirmar Empréstimo</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.getElementById('btn-confirmar').addEventListener('click', async function() {
    const btn = this;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processando...';
    btn.disabled = true;

    try {
        const response = await fetch('finalizar_emprestimo.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ confirmacao: true })
        });

        let data;
        const responseClone = response.clone(); // declara aqui, antes do try

        try {
            data = await response.json();
        } catch (jsonError) {
            const text = await responseClone.text();
            throw new Error('Resposta não é JSON válido: ' + text);
        }

        if (!response.ok) {
            throw new Error(data.error || 'Erro na requisição');
        }

        if (data.success) {
            window.location.href = 'comprovante.php?id_emprestimo=' + data.emprestimo_id;
        } else {
            throw new Error(data.error || 'Erro ao finalizar empréstimo');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert(error.message);
        btn.innerHTML = 'Confirmar Empréstimo';
        btn.disabled = false;
    }
});
    </script>
</body>
</html>