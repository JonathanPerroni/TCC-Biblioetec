<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../../../../conexao/conexao.php");
include_once('../seguranca.php');// já verifica login e carrega CSRF
$token_csrf = gerarTokenCSRF(); // usa token no formulário

// Verifica se o usuário está logado
if (empty($_SESSION['email'])) {
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: ../../login/login.php");
    exit();
}

// Verifica se está criada a sessão para controlar as etapas
if (!isset($_SESSION['etapa'])) {
    $_SESSION['etapa'] = 1; // Inicia na etapa 1
}

$dados  = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Conecta com as páginas de validação dos dados do aluno e do livro
include_once './validacao_dados_aluno.php';
include_once './validacao_dados_livro.php';

// Exibe a mensagem somente se for da etapa correta
if (isset($_SESSION['msg']) && $_SESSION['etapa'] == 1) {
    echo "<div class='mensagem'>{$_SESSION['msg']}</div>";
    unset($_SESSION['msg']); // Limpa a mensagem após exibir
}

if (isset($_SESSION['msg']) && $_SESSION['etapa'] == 2) {
    echo "<div class='mensagem'>{$_SESSION['msg']}</div>";
    unset($_SESSION['msg']);
}

if (isset($_SESSION['msg']) && $_SESSION['etapa'] == 3) {
    echo "<div class='mensagem'>{$_SESSION['msg']}</div>";
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Admin</title>
    <link rel="stylesheet" href="../../../UserCss/defaults.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

   <main class="mx-1 sm:mx-16 my-8">
       <?php
       // Verifica qual etapa deve ser carregada e inclui o respectivo formulário
       if ($_SESSION['etapa'] == 1) {
           include_once './formulario_pesquisa_aluno.php';
       } elseif ($_SESSION['etapa'] == 2) {
           include_once './formulario_pesquisa_livro.php';
       } elseif ($_SESSION['etapa'] == 3) {
           include_once './formulario_de_confirmacao.php';
       }
       ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  
</body>
</html>
