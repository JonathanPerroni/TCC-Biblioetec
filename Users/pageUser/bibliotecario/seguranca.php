<?php
// ===========================
// segurança.php (Modificado e Corrigido)
// ===========================

// Inicia a sessão, se ainda não estiver iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Caminho absoluto para login.php (evita problemas de redirecionamento)
// Ajuste o número de '..' se seguranca.php estiver em um nível diferente
// Ex: Se seguranca.php está em /app/includes/ e login.php em /app/login/
$loginPath = dirname(__DIR__, 2) . '/login/login.php'; // Assumindo que login está 2 níveis acima
$loginUrl = '';
if (file_exists($loginPath)) {
    // Tenta obter o caminho relativo à raiz do documento
    $realLoginPath = realpath($loginPath);
    if ($realLoginPath && isset($_SERVER['DOCUMENT_ROOT'])) {
        $loginUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $realLoginPath);
    }
}
if (!$loginUrl) {
    // Fallback ou log de erro se realpath falhar ou arquivo não existir
    error_log("Falha ao determinar o caminho real para login.php a partir de seguranca.php. Verifique o caminho: " . $loginPath);
    // Tenta um caminho relativo padrão como fallback
    // Ajuste este caminho se necessário
    $loginUrl = '../../login/login.php'; 
}


// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    // Garante que o redirecionamento não ocorra se headers já foram enviados
    if (!headers_sent()) {
        header("Location: " . $loginUrl);
        exit();
    } else {
        // Log ou mensagem alternativa se headers já foram enviados
        error_log("Cabeçalhos já enviados, não foi possível redirecionar para o login em seguranca.php");
        echo "Erro: Você precisa estar logado. Redirecionando para o login...";
        echo "<meta http-equiv='refresh' content='3;url={$loginUrl}'>";
        exit();
    }
}

// --- INÍCIO DA MODIFICAÇÃO ---
// Garante que os dados do bibliotecário estejam na sessão, se aplicável

// Inclui a conexão com o banco de dados APENAS se necessário
// O caminho para conexao.php deve ser relativo à localização de seguranca.php
// Baseado nos includes vistos (ex: ../../../../../conexao/conexao.php a partir de ../livro/pesquisa_livro.php que inclui ../../seguranca.php)
// O caminho relativo de seguranca.php para conexao.php é provavelmente ../../../conexao/conexao.php
$conexaoPath = __DIR__ . '/../../../conexao/conexao.php'; // Use __DIR__ para caminho absoluto seguro

// Verifica se a sessão do bibliotecário já está carregada e completa
$bibliotecarioCarregado = isset($_SESSION['bibliotecario']) && is_array($_SESSION['bibliotecario']) && isset($_SESSION['bibliotecario']['codigo']);

// Tenta carregar os dados do bibliotecário apenas se não estiverem carregados e houver um email na sessão
if (!$bibliotecarioCarregado && !empty($_SESSION['email'])) {

    if (file_exists($conexaoPath)) {
        // Usa require_once para evitar redefinições e problemas
        // Coloca dentro de um bloco try/catch para lidar com erros de conexão
        try {
            // Verifica se a variável $conn já existe (pode ter sido incluída antes)
            if (!isset($conn) || !($conn instanceof mysqli || $conn instanceof PDO)) {
                 require_once $conexaoPath;
            }
        } catch (Exception $e) {
            error_log("Erro ao incluir conexao.php em seguranca.php: " . $e->getMessage());
            // Decide o que fazer se a conexão falhar. Talvez redirecionar para erro?
            // Por enquanto, apenas loga e continua, mas a sessão 'bibliotecario' não será definida.
            $conn = null; // Garante que $conn não seja usado se a inclusão falhar
        }

        // Verifica se a conexão foi estabelecida ($conn geralmente é definido em conexao.php)
        if (isset($conn) && ($conn instanceof mysqli || $conn instanceof PDO)) { // Adapte para PDO se necessário
            $email_sessao = $_SESSION['email'];
            $bibliotecario_data = null; // Inicializa

            try {
                // Verifica se é mysqli ou PDO para usar a sintaxe correta
                if ($conn instanceof mysqli) {
                    $sql = "SELECT codigo, nome FROM tbbibliotecario WHERE email = ? LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("s", $email_sessao);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $bibliotecario_data = $result->fetch_assoc();
                        $stmt->close();
                    } else {
                        error_log("Erro ao preparar statement (mysqli) em seguranca.php: " . $conn->error);
                    }
                } elseif ($conn instanceof PDO) {
                    // Adapte para PDO se sua conexão usar PDO
                    // $sql = "SELECT codigo, nome FROM tbbibliotecario WHERE email = :email LIMIT 1";
                    // $stmt = $conn->prepare($sql);
                    // $stmt->bindParam(':email', $email_sessao);
                    // $stmt->execute();
                    // $bibliotecario_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    error_log("Lógica para PDO não implementada em seguranca.php");
                }

                // Se encontrou um bibliotecário com esse email, armazena na sessão
                if ($bibliotecario_data) {
                    $_SESSION['bibliotecario'] = [
                        'codigo' => $bibliotecario_data['codigo'],
                        'nome' => $bibliotecario_data['nome']
                    ];
                    // Atualiza a flag para evitar re-checagem desnecessária na mesma requisição
                    $bibliotecarioCarregado = true;
                }
                // Se não encontrou, não define $_SESSION['bibliotecario'].
                // Isso é importante para não sobrescrever dados de outros tipos de usuários (admin, aluno).

            } catch (Exception $e) {
                error_log("Erro durante a consulta de bibliotecário em seguranca.php: " . $e->getMessage());
            }
            // Não fecha a conexão aqui ($conn->close()), pois outros scripts podem precisar dela.
        } else {
             error_log("Variável de conexão \$conn não definida ou inválida em seguranca.php após incluir conexao.php.");
        }
    } else {
        error_log("Arquivo de conexão não encontrado em: " . $conexaoPath . " a partir de " . __DIR__);
        // Considerar uma ação mais drástica se a conexão for essencial aqui?
        // Por ora, apenas loga o erro.
    }
}
// --- FIM DA MODIFICAÇÃO ---


// Função para gerar token CSRF
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para validar token CSRF
function validarTokenCSRF($token) {
    // Verifica se o token da sessão existe e se corresponde ao token enviado
    // Usa hash_equals para prevenir ataques de timing
    return isset($_SESSION['csrf_token']) && !empty($_SESSION['csrf_token']) && isset($token) && hash_equals($_SESSION['csrf_token'], $token);
}

// Exemplo de uso da validação CSRF em páginas com formulários POST:
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        // Token inválido ou ausente - Tratar como tentativa de CSRF
        $_SESSION['msg'] = "Erro de validação. Tente novamente.";
        // Redirecionar ou exibir erro
        header('Location: ' . $_SERVER['PHP_SELF']); // Exemplo de redirecionamento
        exit();
    }
    // Se chegou aqui, o token é válido, pode processar o POST
}
*/

// Gera o token para ser usado nos formulários das páginas que incluem este arquivo
// É importante que as páginas que usam POST incluam um campo oculto com este token:
// <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token_csrf); ?>