<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');// já verifica login e carrega CSRF



$email_sessao = $_SESSION['email'];

// Consulta o nome do bibliotecário
$sql = "SELECT nome FROM tbbibliotecario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_sessao);
$stmt->execute();
$result = $stmt->get_result();
$bibliotecario = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff; 
            border-bottom: 1px solid #d4d4d4; 
        }
        .autocomplete-items div:hover {
            background-color: #e9e9e9; 
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Pesquisar Aluno</h4>
                         <small>Bibliotecário: <?= htmlspecialchars($bibliotecario['nome']) ?></small>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['msg'])): ?>
                            <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
                                <?= $_SESSION['msg'] ?>
                            </div>
                            <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
                        <?php endif; ?>

                        <form id="formPesquisa" method="post">
                            <div class="mb-3">
                                <label for="pesquisa" class="form-label">Digite nome ou RA do aluno:</label>
                                <input type="text" class="form-control" id="pesquisa" name="pesquisa" autocomplete="off">
                                <div id="autocomplete-results" class="autocomplete-items"></div>
                            </div>
                            <button type="button" id="btnConfirmar" class="btn btn-primary" disabled>Confirmar</button>
                        </form>

                        <div id="detalhesAluno" class="mt-4" style="display:none;">
                            <h5>Informações do Aluno</h5>
                            <div id="alunoInfo"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('pesquisa').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('autocomplete-results');
            
            if(query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            fetch('busca_alunos.php?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if(data.length === 0) {
                        resultsContainer.innerHTML = '<div>Nenhum aluno encontrado</div>';
                        return;
                    }

                    data.forEach(aluno => {
                        const div = document.createElement('div');
                        div.innerHTML = `<strong>${aluno.nome}</strong> (RA: ${aluno.ra_aluno})`;
                        div.addEventListener('click', () => {
                            document.getElementById('pesquisa').value = aluno.nome;
                            resultsContainer.innerHTML = '';
                            carregarDetalhesAluno(aluno.ra_aluno);
                        });
                        resultsContainer.appendChild(div);
                    });
                });
        });

        function carregarDetalhesAluno(ra) {
            fetch('carrega_aluno.php?ra=' + ra)
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert(data.error);
                        return;
                    }

                    const detalhesDiv = document.getElementById('detalhesAluno');
                    const alunoInfoDiv = document.getElementById('alunoInfo');
                    
                    alunoInfoDiv.innerHTML = `
                        <table class="table table-bordered">
                            <tr><th>Nome:</th><td>${data.nome}</td></tr>
                            <tr><th>RA:</th><td>${data.ra_aluno}</td></tr>
                            <tr><th>Curso:</th><td>${data.nome_curso}</td></tr>
                            <tr><th>Situação:</th><td>${data.situacao}</td></tr>
                            <tr><th>Status:</th><td>${data.status == 1 ? 'Ativo' : 'Bloqueado'}</td></tr>
                        </table>
                    `;
                    
                    detalhesDiv.style.display = 'block';
                    document.getElementById('btnConfirmar').disabled = false;
                    
                    // Armazena RA do aluno selecionado
                    document.getElementById('btnConfirmar').onclick = function() {
                        window.location.href = 'processa_aluno.php?ra=' + ra;
                    };
                });
        }
    </script>
</body>
</html>