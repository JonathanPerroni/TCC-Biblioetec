<?php
session_start();
ob_start();
date_default_timezone_set('America/Sao_Paulo');
include_once("../../conexao/conexao.php");

// Definir as tabelas e seus respectivos tipos de acesso
$tabelasAcesso = [
    'tbadmin' => 'administrador',
    'tbfuncionarios' => 'funcionario',
    'tbbibliotecario' => 'bibliotecario',
    'tbprofessores' => 'professor',
    'tbalunos' => 'aluno'
];

// Verificar se a conexão é válida
if ($conn instanceof mysqli) {
    // Recebe os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Verifica se o código de autenticação foi informado
    if (!empty($dados['ValCodigo'])) {
        // Para cada tabela, verificamos se o usuário existe e o tipo de acesso
        foreach ($tabelasAcesso as $tabela => $acesso) {
            $query_usuario = "SELECT codigo, nome, cpf, email, password, celular, acesso, data_codigo_autenticacao
                              FROM $tabela
                              WHERE codigo = ? AND email = ? AND codigo_autenticacao = ?
                              LIMIT 1";

            // Preparar e verificar a query
            $stmt = $conn->prepare($query_usuario);
            if ($stmt === false) {
                die("Erro na preparação da query: " . $conn->error);
            }

            // Substituir os placeholders
            $stmt->bind_param('sss', $_SESSION['codigo'], $_SESSION['email'], $dados['codigo_autenticacao']);
            $stmt->execute();
            $result_usuario = $stmt->get_result();

            if ($result_usuario->num_rows > 0) {
                // Usuário encontrado; capturar dados
                $row_usuario = $result_usuario->fetch_assoc();
                
                // Atualizar o código de autenticação
                $query_up_usuario = "UPDATE $tabela SET codigo_autenticacao = NULL, data_codigo_autenticacao = NULL
                                     WHERE codigo = ? LIMIT 1";
                $stmt_up = $conn->prepare($query_up_usuario);
                $stmt_up->bind_param('s', $_SESSION['codigo']);
                $stmt_up->execute();

                // Salvar informações na sessão
                $_SESSION['nome'] = $row_usuario['nome'];
                $_SESSION['acesso'] = $acesso;

                // Redirecionar conforme o tipo de acesso
                switch ($_SESSION['acesso']) {
                    case 'administrador':
                        header('Location: ../pageUser/admin/pageAdmin.php');
                        break;
                    case 'funcionario':
                        header('Location: ../pageUser/funcionario/pagefuncionario.php');
                        break;
                    case 'bibliotecario':
                        header('Location: ../pageUser/bibliotecario/pagebibliotecario.php');
                        break;
                    case 'professor':
                        header('Location: ../pageUser/professor/pageprofessor.php');
                        break;
                    case 'aluno':
                        header('Location: ../pageUser/aluno/pagealuno.php');
                        break;
                    default:
                        $_SESSION['msg'] = "Tipo de acesso inválido!";
                        header("Location: ../login/login.php");
                        break;
                }
                exit();
            }
        }
        // Caso não encontre o código em nenhuma tabela
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Código Inválido</p>";
    }
} else {
    die("Conexão com o banco de dados não estabelecida.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Codigo de Acesso</title>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../UserCss/defaults.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-screen h-screen flex flex-col items-center justify-center bg-[var(--off-white)]">
    <main class="min-w-[320px] w-[320px] sm:w-[392px] flex flex-col gap-4 pb-2 sm:pb-2 p-4 sm:p-8 bg-white rounded-md shadow-md">
        <header class="flex gap-2 justify-center items-center">
            <h1 class="text-3xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
            <span class="w-[2px] h-6 sm:h-8  bg-secondary"></span>
            <h1 class="text-3xl font-regular text-secondary">Código</h1>
        </header>
        <form action="" method="post" class="flex flex-col gap-4">
        <div class="flex flex-col gap-0">
            <label for="codigo_autenticacao" class="text-secondary font-medium">Código:</label>
            <input type="text"  name="codigo_autenticacao" id="codigo_autenticacao" placeholder="Insira o código" class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
        </div>
        
            
        <div class="flex flex-col gap-2">
            <input type="submit" name="ValCodigo" value="Validar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer">
            <a href="../login/login.php" class="text-secondary text-sm text-center underline">Sair</a>
        </div>
        
        <?php
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
        ?>
        </form>
    </main>   
</body>
</html>
