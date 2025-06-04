<?php
// ===========================
// comprovante.php (Corrigido v5 - Agrupamento por Tombo)
// ===========================

// Configurações de erro e sessão
ini_set("display_errors", 0); // Idealmente 0 em produção
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "php_errors.log"); // Certifique-se que o servidor web tem permissão para escrever neste arquivo

// Inclui segurança (que inicia sessão e verifica login)
require_once __DIR__ . 
    "/../../seguranca.php"; // Ajuste o caminho conforme necessário

// Inclui conexão com o banco
require_once __DIR__ . 
    "/../../../../../conexao/conexao.php"; // Ajuste o caminho conforme necessário

// Verifica se a conexão foi estabelecida em conexao.php
if (!isset($conn) || !($conn instanceof mysqli)) {
    error_log("Erro: Conexão com banco de dados não estabelecida em comprovante.php.");
    die("Erro crítico: Não foi possível conectar ao banco de dados.");
}

// --- Lógica para buscar e exibir o comprovante por NÚMERO DO GRUPO ---

$n_emprestimo_grupo = filter_input(INPUT_GET, "n_emprestimo", FILTER_VALIDATE_INT);
$emprestimo_geral_dados = null; // Dados gerais do empréstimo (pego da primeira linha)
$livros_emprestados = []; // Lista de livros individuais deste grupo

if (!$n_emprestimo_grupo) {
    $_SESSION["msg"] = "Número do grupo de empréstimo inválido ou não fornecido.";
    $_SESSION["msg_type"] = "danger";
    header("Location: ../aluno/pesquisa_aluno.php"); // Ajuste o caminho
    exit;
}

try {
    // 1. Busca os dados GERAIS do empréstimo pegando a PRIMEIRA linha do grupo
    $sql_geral = "SELECT 
                        e.id_emprestimo, e.n_emprestimo, e.ra_aluno, e.nome_aluno, 
                        e.data_emprestimo, e.data_devolucao_prevista, 
                        e.emprestado_por, e.id_bibliotecario,
                        a.nome_curso, a.periodo 
                    FROM tbemprestimos e
                    LEFT JOIN tbalunos a ON e.ra_aluno = a.ra_aluno
                    WHERE e.n_emprestimo = ? 
                    ORDER BY e.id_emprestimo ASC 
                    LIMIT 1";
    $stmt_geral = $conn->prepare($sql_geral);
    if (!$stmt_geral) {
        throw new Exception(
            "Erro ao preparar consulta geral do empréstimo: " . $conn->error
        );
    }
    $stmt_geral->bind_param("i", $n_emprestimo_grupo);
    $stmt_geral->execute();
    $result_geral = $stmt_geral->get_result();
    $emprestimo_geral_dados = $result_geral->fetch_assoc();
    $stmt_geral->close();

    if (!$emprestimo_geral_dados) {
        $_SESSION["msg"] = "Empréstimo com Nº Grupo {$n_emprestimo_grupo} não encontrado.";
        $_SESSION["msg_type"] = "warning";
        header("Location: ../aluno/pesquisa_aluno.php"); // Ajuste o caminho
        exit;
    }

    // 2. Busca os livros para este n_emprestimo, AGRUPANDO por tombo para evitar duplicatas
    $sql_livros = "SELECT 
                        e.isbn_falso, e.tombo, e.nome_livro, 
                        MIN(e.data_devolucao_prevista) AS data_devolucao_item, -- Pega a primeira data prevista se houver múltiplas
                        l.autor 
                    FROM tbemprestimos e
                    LEFT JOIN tblivros l ON e.isbn_falso = l.isbn_falso 
                    WHERE e.n_emprestimo = ?
                    GROUP BY e.tombo, e.isbn_falso, e.nome_livro, l.autor -- Agrupa para garantir unicidade por tombo
                    ORDER BY e.nome_livro";
    
    $stmt_livros = $conn->prepare($sql_livros);
    if (!$stmt_livros) {
        throw new Exception("Erro ao preparar consulta dos livros: " . $conn->error);
    }
    $stmt_livros->bind_param("i", $n_emprestimo_grupo);
    $stmt_livros->execute();
    $result_livros = $stmt_livros->get_result();
    while ($row = $result_livros->fetch_assoc()) {
        $livros_emprestados[] = $row;
    }
    $stmt_livros->close();

    // Calcula a quantidade total de livros únicos (baseado nas linhas agrupadas)
    $quantidade_total_livros = count($livros_emprestados);

} catch (Exception $e) {
    error_log("Erro ao buscar dados do comprovante (n_emprestimo: {$n_emprestimo_grupo}): " . $e->getMessage());
    $_SESSION["msg"] = "Erro ao carregar dados do comprovante. Tente novamente.";
    $_SESSION["msg_type"] = "danger";
    header("Location: ../aluno/pesquisa_aluno.php"); // Ajuste o caminho
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Empréstimo - Nº Grupo <?= htmlspecialchars($n_emprestimo_grupo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .comprovante-card { 
            max-width: 800px; 
            margin: 2rem auto; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: none;
        }
        .comprovante-header { background-color: #0d6efd; color: white; }
        .comprovante-footer { background-color: #e9ecef; }
        .table th { background-color: #f8f9fa; }
        .assinatura { margin-top: 40px; text-align: center; }
        .linha-assinatura { 
            display: inline-block; 
            width: 250px; 
            border-top: 1px solid #000; 
            margin-top: 5px; 
        }
        
        /* Esconde a segunda via e a linha de corte na tela normal */
        .no-screen {
            display: none;
        }
        .linha-corte {
            display: none;
            border: none;
            border-top: 2px dashed #aaa;
            margin: 20px 0;
        }

        @media print {
            body * { visibility: hidden; }
            .printable-area, .printable-area * { visibility: visible; }
            .printable-area { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                margin: 0; 
                padding: 0; 
                box-shadow: none;
                border: none;
            }
            .no-print { display: none; }
            .comprovante-card { max-width: 100%; }
            .assinatura { page-break-inside: avoid; } 
            
            /* === CSS PARA DUAS VIAS === */
            .no-screen {
                display: block; /* Mostra a segunda via na impressão */
            }
            .linha-corte {
                display: block; /* Mostra a linha de corte na impressão */
            }
            .comprovante-content {
                page-break-inside: avoid; /* Tenta manter cada via em uma página */
            }
            .via-aluno {
                 margin-bottom: 20px; /* Espaço antes da linha de corte */
            }
            /* === FIM CSS PARA DUAS VIAS === */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card comprovante-card printable-area">
            
            <!-- ======================= -->
            <!-- === INÍCIO VIA ALUNO === -->
            <!-- ======================= -->
            <div class="comprovante-content via-aluno">
                <div class="card-header comprovante-header text-center">
                    <h4>Comprovante de Empréstimo - Via Aluno</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION["msg"])): ?>
                        <div class="alert alert-<?= htmlspecialchars($_SESSION["msg_type"]) ?> alert-dismissible fade show no-print" role="alert">
                            <?= htmlspecialchars($_SESSION["msg"]) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION["msg"], $_SESSION["msg_type"]); ?>
                    <?php endif; ?>

                    <div class="text-center mb-4">
                        <h5>Empréstimo Nº Grupo: <?= htmlspecialchars($n_emprestimo_grupo) ?></h5> 
                        <p>Data do Empréstimo: <?= htmlspecialchars(date("d/m/Y H:i", strtotime($emprestimo_geral_dados["data_emprestimo"]))) ?></p>
                    </div>

                    <h6>Dados do Aluno</h6>
                    <table class="table table-bordered table-sm mb-4">
                        <tbody>
                            <tr><th style="width: 100px;">Nome</th><td><?= htmlspecialchars($emprestimo_geral_dados["nome_aluno"]) ?></td></tr>
                            <tr><th>RA</th><td><?= htmlspecialchars($emprestimo_geral_dados["ra_aluno"]) ?></td></tr>
                            <tr><th>Curso</th><td><?= htmlspecialchars($emprestimo_geral_dados["nome_curso"] ?? "N/A") ?></td></tr>
                            <tr><th>Período</th><td><?= htmlspecialchars($emprestimo_geral_dados["periodo"] ?? "N/A") ?></td></tr>
                        </tbody>
                    </table>

                    <h6>Livros Emprestados (Total: <?= htmlspecialchars($quantidade_total_livros) ?>)</h6>
                    <table class="table table-bordered table-striped table-sm mb-4">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>ISBN Falso</th>
                                <th>Tombo</th>
                                <th>Devolução Prevista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($livros_emprestados)): ?>
                                <tr><td colspan="5" class="text-center">Nenhum livro encontrado para este empréstimo.</td></tr>
                            <?php else: ?>
                                <?php foreach ($livros_emprestados as $livro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($livro["nome_livro"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["autor"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["isbn_falso"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["tombo"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($livro["data_devolucao_item"]))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <h6>Informações Adicionais</h6>
                    <table class="table table-bordered table-sm mb-4">
                        <tbody>
                            <tr><th style="width: 250px;">Data Prevista para Devolução Geral</th><td><?= htmlspecialchars(date("d/m/Y", strtotime($emprestimo_geral_dados["data_devolucao_prevista"]))) ?></td></tr>
                            <tr><th>Bibliotecário Responsável</th><td><?= htmlspecialchars($emprestimo_geral_dados["emprestado_por"]) ?></td></tr>
                        </tbody>
                    </table>

                    <div class="alert alert-warning" role="alert">
                        <strong>Atenção:</strong> A não devolução dos livros na data prevista implicará em penalidades conforme o regulamento da biblioteca.
                    </div>

                    <div class="row assinatura">
                        <div class="col-md-6">
                            <span class="linha-assinatura"></span>
                            <p class="mt-1">Assinatura do Aluno</p>
                        </div>
                        <div class="col-md-6">
                            <span class="linha-assinatura"></span>
                            <p class="mt-1">Assinatura do Bibliotecário</p>
                        </div>
                    </div>
                </div> 
            </div> 
            <!-- ===================== -->
            <!-- === FIM VIA ALUNO === -->
            <!-- ===================== -->

            <hr class="linha-corte">

            <!-- ============================= -->
            <!-- === INÍCIO VIA BIBLIOTECA === -->
            <!-- ============================= -->
            <div class="comprovante-content via-biblioteca no-screen">
                 <div class="card-header comprovante-header text-center">
                    <h4>Comprovante de Empréstimo - Via Biblioteca</h4>
                </div>
                 <div class="card-body p-4">
                    <!-- Conteúdo duplicado -->
                    <?php if (isset($_SESSION["msg"])): /* Re-exibir msg aqui pode ser redundante, mas mantém consistência */ ?>
                        <div class="alert alert-<?= htmlspecialchars($_SESSION["msg_type"]) ?> alert-dismissible fade show no-print" role="alert">
                            <?= htmlspecialchars($_SESSION["msg"]) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php /* Não usar unset aqui para não limpar antes da primeira via */ ?>
                    <?php endif; ?>

                    <div class="text-center mb-4">
                        <h5>Empréstimo Nº Grupo: <?= htmlspecialchars($n_emprestimo_grupo) ?></h5> 
                        <p>Data do Empréstimo: <?= htmlspecialchars(date("d/m/Y H:i", strtotime($emprestimo_geral_dados["data_emprestimo"]))) ?></p>
                    </div>

                    <h6>Dados do Aluno</h6>
                    <table class="table table-bordered table-sm mb-4">
                        <tbody>
                            <tr><th style="width: 100px;">Nome</th><td><?= htmlspecialchars($emprestimo_geral_dados["nome_aluno"]) ?></td></tr>
                            <tr><th>RA</th><td><?= htmlspecialchars($emprestimo_geral_dados["ra_aluno"]) ?></td></tr>
                            <tr><th>Curso</th><td><?= htmlspecialchars($emprestimo_geral_dados["nome_curso"] ?? "N/A") ?></td></tr>
                            <tr><th>Período</th><td><?= htmlspecialchars($emprestimo_geral_dados["periodo"] ?? "N/A") ?></td></tr>
                        </tbody>
                    </table>

                    <h6>Livros Emprestados (Total: <?= htmlspecialchars($quantidade_total_livros) ?>)</h6>
                    <table class="table table-bordered table-striped table-sm mb-4">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>ISBN Falso</th>
                                <th>Tombo</th>
                                <th>Devolução Prevista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($livros_emprestados)): ?>
                                <tr><td colspan="5" class="text-center">Nenhum livro encontrado para este empréstimo.</td></tr>
                            <?php else: ?>
                                <?php foreach ($livros_emprestados as $livro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($livro["nome_livro"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["autor"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["isbn_falso"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($livro["tombo"] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($livro["data_devolucao_item"]))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <h6>Informações Adicionais</h6>
                    <table class="table table-bordered table-sm mb-4">
                        <tbody>
                            <tr><th style="width: 250px;">Data Prevista para Devolução Geral</th><td><?= htmlspecialchars(date("d/m/Y", strtotime($emprestimo_geral_dados["data_devolucao_prevista"]))) ?></td></tr>
                            <tr><th>Bibliotecário Responsável</th><td><?= htmlspecialchars($emprestimo_geral_dados["emprestado_por"]) ?></td></tr>
                        </tbody>
                    </table>

                    <div class="alert alert-warning" role="alert">
                        <strong>Atenção:</strong> A não devolução dos livros na data prevista implicará em penalidades conforme o regulamento da biblioteca.
                    </div>

                    <div class="row assinatura">
                        <div class="col-md-6">
                            <span class="linha-assinatura"></span>
                            <p class="mt-1">Assinatura do Aluno</p>
                        </div>
                        <div class="col-md-6">
                            <span class="linha-assinatura"></span>
                            <p class="mt-1">Assinatura do Bibliotecário</p>
                        </div>
                    </div>
                </div> 
            </div> 
            <!-- =========================== -->
            <!-- === FIM VIA BIBLIOTECA === -->
            <!-- =========================== -->

            <div class="card-footer comprovante-footer text-center p-3 no-print">
                <button class="btn btn-secondary" onclick="window.print();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                    </svg>
                    Imprimir Comprovante
                </button>
                <a href="../aluno/pesquisa_aluno.php" class="btn btn-primary">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                    Novo Empréstimo
                </a>
                 <a href="../../../../../pageUser/bibliotecario/pagebibliotecario.php" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
                    </svg>
                    Início
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

