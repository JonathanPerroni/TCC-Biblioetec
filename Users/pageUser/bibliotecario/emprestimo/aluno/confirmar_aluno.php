<?php

ob_start();

date_default_timezone_set('America/Sao_Paulo');

require '../../../../../conexao/conexao.php';

if (!isset($_SESSION['aluno_emprestimo'])) {
    header('Location: pesquisa_aluno.php');
    exit;
}

$aluno = $_SESSION['aluno_emprestimo'];
$email_sessao = $_SESSION['email'];

// Consulta o nome do bibliotecário
$sql = "SELECT nome FROM tbbibliotecario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_sessao);
$stmt->execute();
$result = $stmt->get_result();
$bibliotecario = $result->fetch_assoc();

$_SESSION['bibliotecario'] = $bibliotecario;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Aluno - Sistema de Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Confirmar Dados do Aluno</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            Aluno encontrado e válido para empréstimo!
                        </div>

                        <h5>Dados do Aluno</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr><th>Nome</th><td><?= htmlspecialchars($aluno['nome']) ?></td></tr>
                                    <tr><th>RA</th><td><?= htmlspecialchars($aluno['ra_aluno']) ?></td></tr>
                                    <tr><th>Curso</th><td><?= htmlspecialchars($aluno['nome_curso']) ?></td></tr>
                                    <tr><th>Situação</th><td><?= htmlspecialchars($aluno['situacao']) ?></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mt-4">Bibliotecário Responsável</h5>
                        <p><?= htmlspecialchars($bibliotecario['nome']) ?></p>

                        <form action="pesquisa_livros.php" method="post" class="mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="pesquisa_aluno.php" class="btn btn-secondary">Voltar</a>
                                <button type="submit" class="btn btn-success">Confirmar e Buscar Livros</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>