<?php
// ===========================
// segurança.php
// ===========================

// Inicia a sessão, se ainda não estiver iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Caminho absoluto para login.php (evita problemas de redirecionamento)
$loginPath = dirname(__DIR__, 2) . '/login/login.php';
$loginUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($loginPath));

// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: $loginUrl");
    exit();
}

// Função para gerar token CSRF
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para validar token CSRF
function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
