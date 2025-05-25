<?php
session_start();
require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');

// Verifica se o aluno foi selecionado
if (!isset($_SESSION['aluno_emprestimo'])) {
    $_SESSION['msg'] = "Selecione um aluno primeiro";
    $_SESSION['msg_type'] = "danger";
    header('Location: pesquisa_aluno.php');
    exit;
}
$email_sessao = $_SESSION['email'];
$aluno = $_SESSION['aluno_emprestimo'];
// Consulta o nome do bibliotecário
$sql = "SELECT nome FROM tbbibliotecario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_sessao);
$stmt->execute();
$result = $stmt->get_result();
$bibliotecario = $result->fetch_assoc();
?>



?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Livros - Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .autocomplete-items {
            position: absolute;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            background: white;
            width: calc(100% - 30px);
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .autocomplete-item:hover {
            background-color: #f5f5f5;
        }
        .livro-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .disponibilidade {
            font-weight: bold;
        }
        .disponivel {
            color: green;
        }
        .indisponivel {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Pesquisar Livros para Empréstimo</h4>
                        <p class="mb-0">Aluno: <?= htmlspecialchars($aluno['nome']) ?> (RA: <?= htmlspecialchars($aluno['ra_aluno']) ?>)</p>
                          <small>Bibliotecário: <?= htmlspecialchars($bibliotecario['nome']) ?></small>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['msg'])): ?>
                            <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
                                <?= $_SESSION['msg'] ?>
                            </div>
                            <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
                        <?php endif; ?>

                        <div id="livros-selecionados" class="mb-4">
                            <h5>Livros Selecionados</h5>
                            <div id="lista-livros"></div>
                        </div>

                        <div class="mb-4">
                            <button id="btn-adicionar" class="btn btn-success">Adicionar Livro</button>
                            <button id="btn-confirmar" class="btn btn-primary float-end" disabled>Confirmar Empréstimo</button>
                        </div>

                        <div id="form-pesquisa" style="display: none;">
                            <div class="mb-3">
                                <label for="pesquisa-livro" class="form-label">Pesquisar por Título ou Tombo:</label>
                                <input type="text" class="form-control" id="pesquisa-livro" autocomplete="off">
                                <div id="autocomplete-results" class="autocomplete-items"></div>
                            </div>
                        </div>

                        <div id="detalhes-livro" style="display: none;">
                            <div class="livro-card">
                                <h5 id="livro-titulo"></h5>
                                <p id="livro-autor"></p>
                                <p id="livro-isbn"></p>
                                <p id="livro-disponibilidade" class="disponibilidade"></p>
                                
                                <div class="form-group mb-3">
                                    <label for="data-devolucao">Data de Devolução:</label>
                                    <input type="date" class="form-control" id="data-devolucao">
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sobrepor-regra">
                                    <label class="form-check-label" for="sobrepor-regra">
                                        Sobrepôr regra de 1 exemplar mínimo na biblioteca
                                    </label>
                                </div>
                                
                                <button id="btn-adicionar-livro" class="btn btn-primary">Adicionar ao Empréstimo</button>
                                <button id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const livrosSelecionados = [];
        const maxLivros = 3;
        let livroAtual = null;

        document.getElementById('btn-adicionar').addEventListener('click', function() {
            if(livrosSelecionados.length >= maxLivros && !confirm(`O limite padrão é ${maxLivros} livros. Deseja adicionar mais?`)) {
                return;
            }
            
            document.getElementById('form-pesquisa').style.display = 'block';
            document.getElementById('detalhes-livro').style.display = 'none';
            document.getElementById('pesquisa-livro').focus();
        });

  document.getElementById('pesquisa-livro').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsContainer = document.getElementById('autocomplete-results');
    
    if(query.length < 2) {
        resultsContainer.innerHTML = '';
        return;
    }

    resultsContainer.innerHTML = '<div class="autocomplete-item">Buscando livros...</div>';
    
    // TRECHO CORRIGIDO AQUI:
    fetch(`./busca_livros.php?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json().catch(() => {
                throw new Error("Resposta não é JSON válido");
            });
        })
        .then(data => {
            if (data.success === false) {
                throw new Error(data.error || "Erro desconhecido");
            }
            
            console.log('Dados recebidos:', data);
            resultsContainer.innerHTML = '';
            
            if(!data || data.length === 0) {
                resultsContainer.innerHTML = '<div class="autocomplete-item">Nenhum livro encontrado</div>';
                return;
            }

            data.forEach(livro => {
                const div = document.createElement('div');
                div.className = 'autocomplete-item';
                div.innerHTML = `
                    <strong>${livro.titulo || 'Sem título'}</strong>
                    ${livro.autor ? ' - ' + livro.autor : ''}
                    ${livro.isbn_falso ? ' (Tombo: ' + livro.isbn_falso + ')' : ''}
                `;
                div.addEventListener('click', () => {
                    document.getElementById('pesquisa-livro').value = livro.titulo;
                    resultsContainer.innerHTML = '';
                    if(livro.isbn_falso) {
                        carregarDetalhesLivro(livro.isbn_falso);
                    }
                });
                resultsContainer.appendChild(div);
            });
        })
        .catch(error => {
            console.error("Erro na busca:", error);
            resultsContainer.innerHTML = `<div class="autocomplete-item text-danger">${error.message}</div>`;
        });
});

       // Modifique a função carregarDetalhesLivro():
function carregarDetalhesLivro(isbn) {
    fetch(`carrega_livro.php?isbn=${isbn}`)
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
                return;
            }

            // Garantir que os valores numéricos existam
            data.total_exemplares = data.total_exemplares || 0;
            data.emprestados = data.emprestados || 0;
            data.disponiveis = data.total_exemplares - data.emprestados;


                    livroAtual = data;
                    document.getElementById('form-pesquisa').style.display = 'none';
                    document.getElementById('detalhes-livro').style.display = 'block';
                    
                    // Preencher detalhes do livro
                    document.getElementById('livro-titulo').textContent = data.titulo;
                    document.getElementById('livro-autor').textContent = `Autor: ${data.autor}`;
                    document.getElementById('livro-isbn').textContent = `Tombo/ISBN: ${data.isbn_falso}`;
                    
                    // Calcular e exibir disponibilidade
                    const disponivel = data.total_exemplares - data.emprestados;
                    const disponibilidadeEl = document.getElementById('livro-disponibilidade');
                    disponibilidadeEl.textContent = `Disponíveis: ${disponivel} de ${data.total_exemplares}`;
                    disponibilidadeEl.className = disponivel > 0 ? 'disponibilidade disponivel' : 'disponibilidade indisponivel';
                    
                    // Configurar data de devolução padrão (5 dias úteis)
                    const dataDevolucao = calcularDataDevolucao(5);
                    document.getElementById('data-devolucao').valueAsDate = dataDevolucao;
                });
        }

        function calcularDataDevolucao(dias) {
            const data = new Date();
            let diasAdicionados = 0;
            
            while(diasAdicionados < dias) {
                data.setDate(data.getDate() + 1);
                // Não conta fins de semana
                if(data.getDay() !== 0 && data.getDay() !== 6) {
                    diasAdicionados++;
                }
            }
            
            return data;
        }

        document.getElementById('btn-adicionar-livro').addEventListener('click', function() {
            if(!livroAtual) return;
            
            const dataDevolucao = document.getElementById('data-devolucao').value;
            const sobreporRegra = document.getElementById('sobrepor-regra').checked;
            
            // Verificar disponibilidade (exceto se sobreposta a regra)
            const disponivel = livroAtual.total_exemplares - livroAtual.emprestados;
            if(disponivel <= 0) {
                alert('Não há exemplares disponíveis deste livro');
                return;
            }
            
            if(disponivel === 1 && !sobreporRegra) {
                alert('Sobrepôr a regra do exemplar mínimo na biblioteca');
                return;
            }
            
            // Adicionar livro à lista
            livrosSelecionados.push({
                ...livroAtual,
                data_devolucao: dataDevolucao,
                sobrepor_regra: sobreporRegra
            });
            
            atualizarListaLivros();
            resetarFormulario();
        });

        function atualizarListaLivros() {
            const listaEl = document.getElementById('lista-livros');
            listaEl.innerHTML = '';
            
            if(livrosSelecionados.length === 0) {
                listaEl.innerHTML = '<p>Nenhum livro selecionado</p>';
                document.getElementById('btn-confirmar').disabled = true;
                return;
            }
            
            document.getElementById('btn-confirmar').disabled = false;
            
            livrosSelecionados.forEach((livro, index) => {
                const card = document.createElement('div');
                card.className = 'livro-card';
                card.innerHTML = `
                    <h6>${livro.titulo}</h6>
                    <p>Autor: ${livro.autor}</p>
                    <p>Tombo/ISBN: ${livro.isbn_falso}</p>
                    <p>Data de Devolução: ${new Date(livro.data_devolucao).toLocaleDateString()}</p>
                    <button class="btn btn-sm btn-danger btn-remover" data-index="${index}">Remover</button>
                `;
                listaEl.appendChild(card);
            });
            
            // Adicionar eventos aos botões de remover
            document.querySelectorAll('.btn-remover').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    livrosSelecionados.splice(index, 1);
                    atualizarListaLivros();
                });
            });
        }

        function resetarFormulario() {
            document.getElementById('pesquisa-livro').value = '';
            document.getElementById('autocomplete-results').innerHTML = '';
            document.getElementById('form-pesquisa').style.display = 'none';
            document.getElementById('detalhes-livro').style.display = 'none';
            livroAtual = null;
        }

        document.getElementById('btn-cancelar').addEventListener('click', resetarFormulario);

        document.getElementById('btn-confirmar').addEventListener('click', function() {
            // Salvar livros na sessão e redirecionar para confirmação
            const formData = new FormData();
            formData.append('livros', JSON.stringify(livrosSelecionados));
            
            fetch('salvar_livros_emprestimo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = '../confirmar_emprestimo/confirmar_emprestimo.php';
                } else {
                    alert('Erro ao salvar os livros: ' + data.error);
                }
            });
        });
    </script>
</body>
</html>