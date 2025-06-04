<?php
ob_start(); // Inicia o buffer de saída no início

ini_set("display_errors", 0);
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "php_errors.log"); // Certifique-se que este arquivo tem permissão de escrita pelo servidor web

session_start();

// *** Define que esta é uma requisição AJAX ANTES de incluir segurança ***
define("IS_AJAX_REQUEST", true);

// Inclui segurança (que inicia sessão, verifica login e carrega dados do bibliotecário)
require_once __DIR__ . "/../../seguranca.php"; // Ajuste o caminho se necessário

// Inclui conexão com o banco
require_once __DIR__ . "/../../../../../conexao/conexao.php"; // Ajuste o caminho se necessário

// Garante que o tipo de conteúdo seja JSON
header("Content-Type: application/json");

// Verifica se a conexão foi estabelecida
if (!isset($conn) || !($conn instanceof mysqli)) {
    error_log(
        "Erro: Conexão com banco de dados não estabelecida em finalizar_emprestimo.php."
    );
    ob_end_clean(); // Limpa buffer
    echo json_encode([
        "success" => false,
        "error" => "Erro interno no servidor (conexão)",
    ]);
    exit();
}

// Verifica se as sessões necessárias existem
if (
    !isset($_SESSION["bibliotecario"]) ||
    !isset($_SESSION["aluno_emprestimo"]) ||
    !isset($_SESSION["livros_emprestimo"])
) {
    ob_end_clean(); // Limpa buffer
    echo json_encode([
        "success" => false,
        "error" => "Acesso não autorizado ou sessão inválida",
    ]);
    exit();
}

$numero_emprestimo_grupo = null; // Guarda o n_emprestimo comum para esta transação
$primeiro_emprestimo_id = null; // Guarda o id_emprestimo da primeira linha inserida
$tombos_inseridos_nesta_transacao = []; // <<< ADICIONADO: Array para rastrear tombos já inseridos nesta transação

try {
    // --- DEBUG: Determinar o n_emprestimo ANTES de iniciar a transação ---
    $sql_get_next_n = "SELECT MAX(n_emprestimo) as max_n FROM tbemprestimos";
    error_log("DEBUG N_EMPRESTIMO: Executando consulta: " . $sql_get_next_n); // Log 1
    $result_n = $conn->query($sql_get_next_n);
    if (!$result_n) {
        error_log("DEBUG N_EMPRESTIMO: Erro na consulta MAX: " . $conn->error); // Log erro consulta
        throw new Exception(
            "Erro ao buscar próximo n_emprestimo: " . $conn->error
        );
    }
    $row_n = $result_n->fetch_assoc();
    error_log("DEBUG N_EMPRESTIMO: Resultado da consulta MAX: " . print_r($row_n, true)); // Log 2
    // Garante que mesmo se max_n for NULL (tabela vazia), o resultado seja 1
    $max_n_valor = intval($row_n["max_n"] ?? 0); // Pega o valor ou 0
    error_log("DEBUG N_EMPRESTIMO: Valor MAX obtido (ou 0): " . $max_n_valor); // Log 3
    $numero_emprestimo_grupo = $max_n_valor + 1; // Calcula o próximo
    error_log("DEBUG N_EMPRESTIMO: Próximo n_emprestimo calculado: " . $numero_emprestimo_grupo); // Log 4
    $result_n->free();
    // --- FIM DEBUG ---

    // --- Iniciar a transação APÓS determinar o número do grupo ---
    $conn->begin_transaction();

    $aluno = $_SESSION["aluno_emprestimo"];
    $bibliotecario = $_SESSION["bibliotecario"];
    $livros = $_SESSION["livros_emprestimo"];

    if (empty($livros)) {
        throw new Exception("Nenhum livro selecionado para empréstimo");
    }

    // Validações essenciais
    if (empty($aluno["ra_aluno"])) {
        throw new Exception("RA do aluno não encontrado na sessão");
    }
    if (empty($bibliotecario["codigo"]) || empty($bibliotecario["nome"])) {
        throw new Exception("Dados do bibliotecário incompletos na sessão");
    }

    // --- Preparar statements fora do loop --- 
    $sql_emprestimo_individual = "INSERT INTO tbemprestimos (
        n_emprestimo, ra_aluno, nome_aluno, isbn_falso, isbn, tombo, nome_livro,
        qntd_livros, data_emprestimo, data_devolucao_prevista,
        emprestado_por, id_bibliotecario
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?, ?, ?)";
    $stmt_emprestimo = $conn->prepare($sql_emprestimo_individual);
    if (!$stmt_emprestimo) {
        throw new Exception(
            "Erro ao preparar empréstimo individual: " . $conn->error
        );
    }

    $sql_item = "INSERT INTO tbitens_emprestimo (
                    id_emprestimo, isbn_falso, tombo, data_devolucao_prevista
                ) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    if (!$stmt_item) {
        throw new Exception("Erro ao preparar item empréstimo: " . $conn->error);
    }

    $sql_atualiza = "UPDATE tblivro_estoque SET total_exemplares = total_exemplares - 1 WHERE isbn_falso = ?";
    $stmt_estoque = $conn->prepare($sql_atualiza);
    if (!$stmt_estoque) {
        error_log("Erro ao preparar atualização estoque: " . $conn->error);
    }

    // --- Loop para processar cada livro --- 
    foreach ($livros as $index => $livro) {
        // Validação crucial: Verifica se o tombo existe no array do livro
        if (empty($livro["tombo"])) {
            error_log("AVISO: Livro sem tombo definido na sessão durante finalização: " . print_r($livro, true));
            // Decide se quer pular ou lançar erro. Pular é mais seguro para não travar tudo.
            continue; 
        }
        $tombo_livro = $livro["tombo"];

        // <<< INÍCIO DA VERIFICAÇÃO DE DUPLICIDADE NO SERVIDOR >>>
        if (in_array($tombo_livro, $tombos_inseridos_nesta_transacao)) {
            // Se o tombo já foi inserido NESTA transação, loga um aviso e pula para o próximo livro
            error_log("AVISO: Tentativa de inserir tombo duplicado {$tombo_livro} para n_emprestimo {$numero_emprestimo_grupo} detectada na sessão. Pulando inserção.");
            continue; // Pula para o próximo livro
        }
        // <<< FIM DA VERIFICAÇÃO DE DUPLICIDADE NO SERVIDOR >>>

        // <<< ADICIONADO: Adiciona o tombo ao array de rastreamento APÓS a verificação >>>
        $tombos_inseridos_nesta_transacao[] = $tombo_livro;

        // Se não for duplicado, prossiga com as inserções e atualizações
        if (empty($livro["isbn_falso"])) {
            throw new Exception(
                "Livro sem ISBN Falso na lista de empréstimo."
            );
        }
        // Note: A validação de tombo vazio já foi feita acima

        $isbn_falso_livro = $livro["isbn_falso"];
        $isbn_livro = ""; // Assumindo que ISBN real não é usado aqui
        // $tombo_livro já definido acima
        $nome_livro = $livro["titulo"] ?? "";
        $data_devolucao_livro = date(
            "Y-m-d",
            strtotime($livro["data_devolucao"] ?? " + 7 days") // Usar data da sessão ou padrão
        );

        // 1. Inserir linha em tbemprestimos
        $stmt_emprestimo->bind_param(
            "issssssssi", // Ajustado para 10 parâmetros (i para n_emprestimo, s para os strings, i para id_bibliotecario)
            $numero_emprestimo_grupo,
            $aluno["ra_aluno"],
            $aluno["nome"],
            $isbn_falso_livro,
            $isbn_livro, // Vazio
            $tombo_livro,
            $nome_livro,
            $data_devolucao_livro,
            $bibliotecario["nome"],
            $bibliotecario["codigo"]
        );
        if (!$stmt_emprestimo->execute()) {
            throw new Exception(
                "Erro ao registrar empréstimo para Tombo {$tombo_livro}: " .
                    $stmt_emprestimo->error
            );
        }
        $current_emprestimo_id = $conn->insert_id;

        // Guarda o ID do primeiro empréstimo inserido para usar na tbitens_emprestimo
        if ($index === 0) {
            $primeiro_emprestimo_id = $current_emprestimo_id;
        }

        // 2. Inserir linha em tbitens_emprestimo
        if ($primeiro_emprestimo_id === null) {
            // Isso não deveria acontecer se o loop rodou pelo menos uma vez
            throw new Exception(
                "Erro crítico: ID do primeiro empréstimo não definido para vincular itens."
            );
        }
        $stmt_item->bind_param(
            "isss", // i para id_emprestimo, s para isbn_falso, s para tombo, s para data
            $primeiro_emprestimo_id, // Usa o ID do primeiro registro de tbemprestimos como chave estrangeira
            $isbn_falso_livro,
            $tombo_livro,
            $data_devolucao_livro
        );
        if (!$stmt_item->execute()) {
            throw new Exception(
                "Erro ao registrar item empréstimo (Tombo: {$tombo_livro}): " .
                    $stmt_item->error
            );
        }

        // 3. Atualizar estoque
        if ($stmt_estoque) {
            $stmt_estoque->bind_param("s", $isbn_falso_livro);
            if (!$stmt_estoque->execute()) {
                // Loga o erro mas não interrompe a transação necessariamente
                error_log(
                    "Erro na atualização do estoque para ISBN {$isbn_falso_livro}: " .
                        $stmt_estoque->error
                );
            }
        }
    } // Fim do loop foreach

    // Fechar statements preparados
    $stmt_emprestimo->close();
    $stmt_item->close();
    if ($stmt_estoque) {
        $stmt_estoque->close();
    }

    // Se tudo correu bem, comita a transação
    $conn->commit();

    // Limpa a sessão de livros após sucesso
    unset($_SESSION["livros_emprestimo"]);

    ob_end_clean(); // Limpa buffer antes do JSON final
    // Retorna o número do grupo de empréstimo
    echo json_encode([
        "success" => true,
        "n_emprestimo" => $numero_emprestimo_grupo,
        "message" =>
            "Empréstimo registrado com sucesso! (Nº Grupo: " .
            $numero_emprestimo_grupo .
            ")",
    ]);
    exit(); // Garante que nada mais seja executado

} catch (Exception $e) {
    // Garante rollback em caso de erro
    if (isset($conn) && $conn->ping() && $conn->in_transaction) {
        $conn->rollback();
    }
    error_log(
        "ERRO FINALIZAR EMPRESTIMO: " . $e->getMessage()
    );
    ob_end_clean(); // Limpa buffer em caso de erro
    echo json_encode([
        "success" => false,
        "error" =>
            "Erro ao processar empréstimo. Verifique os logs ou contate o suporte.",
        // uncomment the line below for debugging only
        // "error_debug" => $e->getMessage()
    ]);
    exit();
}

?>
