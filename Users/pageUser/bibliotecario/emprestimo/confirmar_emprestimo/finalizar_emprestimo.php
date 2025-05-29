<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

session_start();

// Inclui segurança (que inicia sessão, verifica login e carrega dados do bibliotecário)
require_once __DIR__ . '/../../seguranca.php'; // Ajuste o caminho se necessário

// Inclui conexão com o banco
require_once __DIR__ . '/../../../../../conexao/conexao.php'; // Ajuste o caminho se necessário

header('Content-Type: application/json');

// Verifica se a conexão foi estabelecida
if (!isset($conn) || !($conn instanceof mysqli)) {
    error_log("Erro: Conexão com banco de dados não estabelecida em finalizar_emprestimo.php.");
    echo json_encode(['success' => false, 'error' => 'Erro interno no servidor (conexão)']);
    exit;
}

// Verifica se as sessões necessárias existem
if (!isset($_SESSION['bibliotecario']) || !isset($_SESSION['aluno_emprestimo']) || !isset($_SESSION['livros_emprestimo'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado ou sessão inválida']);
    exit;
}

$conn->begin_transaction();
$numero_emprestimo_grupo = null; // Guarda o n_emprestimo comum para esta transação
$primeiro_emprestimo_id = null; // Guarda o id_emprestimo da primeira linha inserida

try {
    $aluno = $_SESSION['aluno_emprestimo'];
    $bibliotecario = $_SESSION['bibliotecario'];
    $livros = $_SESSION['livros_emprestimo'];

    if (empty($livros)) {
        throw new Exception('Nenhum livro selecionado para empréstimo');
    }

    // Validações essenciais
    if (empty($aluno['ra_aluno'])) {
        throw new Exception('RA do aluno não encontrado na sessão');
    }
    if (empty($bibliotecario['codigo']) || empty($bibliotecario['nome'])) {
         throw new Exception('Dados do bibliotecário incompletos na sessão');
    }

    // --- NOVA LÓGICA: Determinar o n_emprestimo do grupo --- 
    // Pega o maior n_emprestimo existente e soma 1. 
    // CUIDADO: Isso pode ter problemas de concorrência em sistemas com muitos usuários simultâneos.
    // Uma sequência ou UUID seria mais robusto para gerar o ID do grupo.
    // Usando MAX + 1 por simplicidade conforme lógica anterior implícita.
    $sql_get_next_n = "SELECT MAX(n_emprestimo) as max_n FROM tbemprestimos";
    $result_n = $conn->query($sql_get_next_n);
    if (!$result_n) {
        throw new Exception("Erro ao buscar próximo n_emprestimo: " . $conn->error);
    }
    $row_n = $result_n->fetch_assoc();
    $numero_emprestimo_grupo = ($row_n['max_n'] ?? 0) + 1;
    $result_n->free();

    // --- Preparar statements fora do loop --- 

    // SQL para inserir linha individual em tbemprestimos
    $sql_emprestimo_individual = "INSERT INTO tbemprestimos (
        n_emprestimo, ra_aluno, nome_aluno, isbn_falso, isbn, tombo, nome_livro,
        qntd_livros, data_emprestimo, data_devolucao_prevista,
        emprestado_por, id_bibliotecario
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?, ?, ?)"; // qntd_livros é sempre 1
    $stmt_emprestimo = $conn->prepare($sql_emprestimo_individual);
    if (!$stmt_emprestimo) {
        throw new Exception("Erro ao preparar empréstimo individual: " . $conn->error);
    }

    // SQL para inserir em tbitens_emprestimo (mantido conforme solicitado)
    // Vinculado ao ID do PRIMEIRO registro inserido em tbemprestimos para este grupo
    $sql_item = "INSERT INTO tbitens_emprestimo (
                    id_emprestimo, isbn_falso, tombo, data_devolucao_prevista
                ) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
     if (!$stmt_item) {
        throw new Exception("Erro ao preparar item empréstimo: " . $conn->error);
    }

    // SQL para atualizar estoque
    $sql_atualiza = "UPDATE tblivro_estoque SET total_exemplares = total_exemplares - 1 WHERE isbn_falso = ?";
    $stmt_estoque = $conn->prepare($sql_atualiza);
    if (!$stmt_estoque) {
         error_log("Erro ao preparar atualização estoque: " . $conn->error);
         // Considerar lançar exceção se a atualização de estoque for crítica
         // throw new Exception("Erro ao preparar atualização estoque: " . $conn->error);
    }

    // --- Loop para processar cada livro --- 
    foreach ($livros as $index => $livro) {
        // Validações para cada livro
        if (empty($livro['isbn_falso'])) {
             throw new Exception("Livro sem ISBN Falso na lista de empréstimo.");
        }
        if (empty($livro['tombo'])) {
             throw new Exception("Livro '{$livro['titulo']}' sem TOMBO definido na seleção.");
        }

        // (Opcional) Verificar disponibilidade novamente aqui para evitar race conditions
        // ... (código de verificação de estoque omitido para brevidade, mas recomendado) ...

        $isbn_falso_livro = $livro['isbn_falso'];
        $isbn_livro = ''; // Assumindo que ainda não está disponível
        $tombo_livro = $livro['tombo'];
        $nome_livro = $livro['titulo'] ?? '';
        $data_devolucao_livro = date('Y-m-d', strtotime($livro['data_devolucao'] ?? ' + 7 days'));

        // 1. Inserir linha em tbemprestimos para este livro
        // Bind: int(n_emp), string(ra), string(nome_a), string(isbn_f), string(isbn), string(tombo), string(nome_l), string(data_dev), string(nome_b), int(id_b)
        $stmt_emprestimo->bind_param("issssssssi",
            $numero_emprestimo_grupo, // n_emprestimo comum
            $aluno['ra_aluno'],
            $aluno['nome'],
            $isbn_falso_livro,
            $isbn_livro,
            $tombo_livro,
            $nome_livro,
            $data_devolucao_livro, // Data específica do item ou geral?
            $bibliotecario['nome'],
            $bibliotecario['codigo']
        );

        if (!$stmt_emprestimo->execute()) {
            throw new Exception("Erro ao registrar empréstimo para Tombo {$tombo_livro}: " . $stmt_emprestimo->error);
        }
        $current_emprestimo_id = $conn->insert_id; // Pega o ID desta linha específica

        // Guarda o ID da primeira linha inserida para vincular os itens
        if ($index === 0) {
            $primeiro_emprestimo_id = $current_emprestimo_id;
        }

        // 2. Inserir linha em tbitens_emprestimo (vinculada ao primeiro ID)
        // Certifique-se que $primeiro_emprestimo_id foi definido
        if ($primeiro_emprestimo_id === null) {
             throw new Exception("Erro crítico: ID do primeiro empréstimo não definido para vincular itens.");
        }
        // Bind: int(id_emp_primeiro), string(isbn_f), string(tombo), string(data_dev)
        $stmt_item->bind_param("isss",
            $primeiro_emprestimo_id, // Vincula ao ID da primeira linha de tbemprestimos deste grupo
            $isbn_falso_livro,
            $tombo_livro,
            $data_devolucao_livro
        );
         if (!$stmt_item->execute()) {
            error_log("Erro ao registrar item empréstimo (Tombo: {$tombo_livro}): " . $stmt_item->error);
            throw new Exception("Erro ao registrar item empréstimo (Tombo: {$tombo_livro}): " . $stmt_item->error);
        }

        // 3. Atualizar estoque (se o statement foi preparado com sucesso)
        if ($stmt_estoque) {
            $stmt_estoque->bind_param("s", $isbn_falso_livro);
            if (!$stmt_estoque->execute()) {
                error_log("Erro na atualização do estoque para ISBN {$isbn_falso_livro}: " . $stmt_estoque->error);
                // Decidir se lança exceção ou apenas loga
            }
        }
    } // Fim do loop foreach

    // Fechar statements preparados
    $stmt_emprestimo->close();
    $stmt_item->close();
    if ($stmt_estoque) {
        $stmt_estoque->close();
    }

    $conn->commit();

    unset($_SESSION['livros_emprestimo']);

    // Retorna o número do grupo de empréstimo
    echo json_encode([
        'success' => true,
        'n_emprestimo' => $numero_emprestimo_grupo, // Retorna o número do grupo
        'message' => 'Empréstimo registrado com sucesso! (Nº Grupo: ' . $numero_emprestimo_grupo . ')'
    ]);

} catch (Exception $e) {
    // Garante rollback em caso de erro
    if (isset($conn) && $conn->ping() && $conn->in_transaction) {
        $conn->rollback();
    }
    error_log("ERRO FINALIZAR EMPRESTIMO (Reestruturado): " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar empréstimo. Verifique os logs ou contate o suporte.'
        // 'error_debug' => $e->getMessage() // Descomentar para debug apenas
    ]);
    exit;
}

?>

