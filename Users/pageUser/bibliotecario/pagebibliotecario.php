<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../../../conexao/conexao.php");
include_once('./seguranca.php');// já verifica login e carrega CSRF
$token_csrf = gerarTokenCSRF(); // usa token no formulário
// Inicializa mensagens e variáveis de controle
$msg = "";
$etapa = $_POST['etapa'] ?? 1;



// Etapa 1: Buscar Aluno
if ($etapa == 1 && isset($_POST['buscar_cpf'])) {
    $cpf = trim($_POST['cpf']);

    $query_aluno = "SELECT nome, nome_escola, nome_curso, acesso FROM tbalunos WHERE cpf = ?";
    $stmt = $conn->prepare($query_aluno);

    if ($stmt) {
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['aluno'] = $result->fetch_assoc();
            $_SESSION['aluno']['cpf'] = $cpf;
            $etapa = 2;
        } else {
            $msg = "Aluno não encontrado!";
        }
        $stmt->close();
    } else {
        $msg = "Erro ao preparar consulta de aluno: " . $conn->error;
    }
}

// Etapa 2: Buscar Livro
if ($etapa == 2 && isset($_POST['buscar_livro'])) {
    $isbn = trim($_POST['isbn']);
    $titulo = trim($_POST['titulo']);

    $query_livro = "SELECT isbn, titulo, quantidade FROM tblivros WHERE isbn = ? OR titulo = ?";
    $stmt = $conn->prepare($query_livro);

    if ($stmt) {
        $stmt->bind_param("ss", $isbn, $titulo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['livro'] = $result->fetch_assoc();
            $etapa = 3;
        } else {
            $msg = "Livro não encontrado!";
        }
        $stmt->close();
    } else {
        $msg = "Erro ao preparar consulta de livro: " . $conn->error;
    }
}

// Etapa 3: Confirmar Empréstimo
if ($etapa == 3 && isset($_POST['confirmar_emprestimo'])) {
    // Verifique se os dados estão na sessão
    if (!isset($_SESSION['aluno']) || !isset($_SESSION['livro'])) {
        $msg = "Erro: Dados do aluno ou livro ausentes!";
    } else {
        $aluno = $_SESSION['aluno'];
        $livro = $_SESSION['livro'];

        // Verifique a quantidade total e emprestada
        $query_quantidade = "SELECT quantidade, 
                                (SELECT COUNT(*) FROM tbemprestimos WHERE isbn_livro = ? AND status = 'emprestado') AS emprestados 
                             FROM tblivros WHERE isbn = ?";
        $stmt = $conn->prepare($query_quantidade);

        if ($stmt) {
            $stmt->bind_param("ss", $livro['isbn'], $livro['isbn']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $dados_livro = $result->fetch_assoc();
                $quantidade_total = $dados_livro['quantidade'];
                $quantidade_emprestada = $dados_livro['emprestados'];
                $disponiveis = $quantidade_total - $quantidade_emprestada;

                // Verifica se há apenas 1 exemplar restante
                if ($disponiveis <= 1) {
                    echo "<script>
                            alert('Apenas 1 exemplar disponível. Deve haver pelo menos uma cópia na biblioteca.');
                            window.location.href = 'http://localhost/biblioetec/Desenvolvedor/Users/pageUser/bibliotecario/pagebibliotecario.php';
                          </script>";
                } else {
                    // Registra o empréstimo
                    $query_emprestimo = "INSERT INTO tbemprestimos (cpf_aluno, isbn_livro, data_emprestimo, status) VALUES (?, ?, NOW(), 'emprestado')";
                    $stmt = $conn->prepare($query_emprestimo);

                    if ($stmt) {
                        $stmt->bind_param("ss", $aluno['cpf'], $livro['isbn']);

                        if ($stmt->execute()) {
                            echo "<script>
                                    alert('Livro emprestado com sucesso!');
                                    window.location.href = '../bibliotecario/pagebibliotecario.php';
                                  </script>";
                            // Limpa os dados da sessão
                            unset($_SESSION['aluno']);
                            unset($_SESSION['livro']);
                        } else {
                            $msg = "Erro ao registrar o empréstimo: " . $stmt->error;
                        }
                    } else {
                        $msg = "Erro ao preparar consulta de empréstimo: " . $conn->error;
                    }
                }
            } else {
                $msg = "Erro ao verificar a disponibilidade do livro!";
            }
        } else {
            $msg = "Erro ao preparar consulta de quantidade: " . $conn->error;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Admin</title>

    <link rel="stylesheet" href="../../UserCss/defaults.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<nav class="flex justify-between items-center py-2 px-4 bg-white shadow-md">
        <div id="nav-left-side">
            <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="inline-flex items-center p-2 ms-3 bg-[var(--primary)] text-sm text-white rounded-lg hover:bg-[var(--primary-emphasis)] focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-white dark:hover:bg-[var(--primary-emphasis)]">
                <span class="sr-only">Open sidebar</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
            <a href="#" class="text-4xl mx-4 text-primary font-semibold hidden md:inline" tabindex="-1">Biblio<span class="text-secondary">etec</span></a>
        </div>
        <aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full" aria-label="Sidebar">
                                    <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-[var(--primary-emphasis)]">
                                    <ul class="space-y-2 font-medium">
                                    <li>
                                                    <!-- ira verificar qual  usuario esta logado para voltar para pagina especifica do tipo do acesso  -->
                                                    <?php
                                                                

                                                                // Verifica o tipo de acesso do usuário
                                                                $acesso = $_SESSION['acesso'] ?? ''; // Define o valor de acesso na sessão, caso não exista

                                                                // Define o link de redirecionamento com base no tipo de acesso
                                                                switch ($acesso) {
                                                                    case 'administrador':
                                                                        $pagina_inicial = "../admin/pageAdmin.php";
                                                                        break;
                                                                    case 'bibliotecario':
                                                                        $pagina_inicial = "../bibliotecario/pagebibliotecario.php";
                                                                        break;
                                                                    default:
                                                                        // Redireciona para uma página padrão, caso o acesso não seja identificado
                                                                        $pagina_inicial = "../../../login/login.php";
                                                                        break;
                                                                }
                                                    ?>

                                                    <a href="<?php echo $pagina_inicial; ?>" class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house">
                                                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                                                            <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                                        </svg>
                                                        <span class="ms-3">Início</span>
                                                    </a>
                                            </li>
                                            <li>
                                                <button type="button" class="flex w-full items-center p-2 text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group" aria-controls="dropdown-cadastro" data-collapse-toggle="dropdown-cadastro">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-plus"><path d="M11 12H3"/><path d="M16 6H3"/><path d="M16 18H3"/><path d="M18 9v6"/><path d="M21 12h-6"/></svg>
                                                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Cadastrar</span>
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                                    </svg>
                                                </button>
                                                <ul id="dropdown-cadastro" class="hidden py-2 space-y-2">
                                                    <li>
                                                        <a href="../pageGlobal/cadastrar/cadastrar_aluno.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Aluno</a>
                                                    </li>
                                                    <li>
                                                        <a href="../pageGlobal/cadastrar/cadastrar_bibliotecario.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Bibliotecario</a>
                                                    </li>
                                                    <li>
                                                        <a href="../pageGlobal/cadastrar/cadastrar_funcionario.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Funcionario</a>
                                                    </li>
                                                    <li>
                                                        <a href="../pageGlobal/cadastrar/cadastrar_professor.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Professor</a>
                                                    </li>
                                                                                                    
                                                                
                                                </ul>
                                            </li>
                                    <li>
                                        <button type="button" class="flex w-full items-center p-2 text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group" aria-controls="dropdown-lista" data-collapse-toggle="dropdown-lista">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-list"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/><path d="M14 4h7"/><path d="M14 9h7"/><path d="M14 15h7"/><path d="M14 20h7"/></svg>
                                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Listas</span>
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        <ul id="dropdown-lista" class="hidden py-2 space-y-2">
                                        
                                            <li>
                                                <a href="../pageGlobal/list/listaalunoNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Aluno</a>
                                            </li>
                                            <li>
                                                <a href="../pageGlobal/list/listabibliotecarioNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista bibliotecario</a>
                                            </li>
                                            <li>
                                                <a href="../pageGlobal/list/listafuncionarioNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Funcionario</a>
                                            </li>
                                            <li>
                                                <a href="../pageGlobal/list/listaprofessorNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Professor</a>
                                            </li>
                                        
                                        </ul>
                                    </li>
                                                                                <!-- acervo cadastrar -->
                                                                                <li>
                                                                                    <button type="button" class="flex w-full items-center p-2  text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group" aria-controls="dropdown-acervo" data-collapse-toggle="dropdown-acervo">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book">
                                                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                                                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15z"/>
                                                                                    </svg>
                                                                                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Registrar Acervo</span>
                                                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                                                                    </svg>
                                                                                    </button>
                                                                                    <ul id="dropdown-acervo" class="hidden py-2 space-y-2">
                                                                                        <li>
                                                                                            <a href="../pageGlobal/cadastrar/acervo/cadastrar_livro.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Registrar Livro</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/cadastrar/acervo/cadastrar_jornal_revista.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Registrar Jornal/Revista</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/cadastrar/acervo/cadastrar_midia.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Registrar Midia</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/cadastrar/acervo/cadastrar_jogos.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Registrar Jogo</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/cadastrar/acervo/cadastrar_tcc.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Registrar TCC</a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </li>  
                                                                                <!-- acervo lista -->
                                                                                <li>
                                                                                    <button type="button" class="flex w-full items-center p-2  text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group" aria-controls="dropdown-listaacervo" data-collapse-toggle="dropdown-listaacervo">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-library-big">
                                                                                            <rect width="8" height="18" x="3" y="3" rx="1"/>
                                                                                            <path d="M7 3v18"/>
                                                                                            <path d="M20.4 18.9c.2.5-.1 1.1-.6 1.3l-1.9.7c-.5.2-1.1-.1-1.3-.6L11.1 5.1c-.2-.5.1-1.1.6-1.3l1.9-.7c.5-.2 1.1.1 1.3.6Z"/>
                                                                                        </svg>
                                                                                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Catalogo Acervo</span>
                                                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                                                                        </svg>

                                                                                    </button>
                                                                                    <ul id="dropdown-listaacervo" class="hidden py-2 space-y-2">
                                                                                        <li>
                                                                                            <a href="../pageGlobal/list/acervo/listalivroNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Catalogo de Livro</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/list/acervo/listajornalrevistaNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Catalogo de Jornal/Revista</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/list/acervo/listamidiaNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Catalogo de Midia</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/list/acervo/listajogosNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Catalogo de Jogo</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../pageGlobal/list/acervo/listatccNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Catalogo de TCC</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="../bibliotecario/lista_emprestimos.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista de Emprestimo</a>
                                                                                        </li>
                                                                                                </ul>
                                                                                            </li>        
                    
                        <!-- Link para Relatório -->
                        <li>
                            <a href="../pageGlobal/Relatorio/historico.php" class="flex items-center p-2  text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text mr-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <line x1="10" y1="9" x2="8" y2="9"/>
                                </svg>
                                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Relatório</span>
                            </a>
                        </li>

                    </ul>
                    </div>
        </aside>

        <button id="dropdown-perfil" data-dropdown-toggle="dropdown" class="flex justify-between items-center max-h-12 pl-4 mr-4 bg-white border-2 border-solid border-[var(--secondary)] border-r-0 rounded-lg text-[var(--secondary)] text-left flex-nowrap text-nowrap" type="button">
            <div>
                <span class="text-[var(--secondary)] font-medium"><?php echo $_SESSION['nome']; ?></span>
                <hr>
                <span class="text-xs text-[var(--secondary)]"><?php echo $_SESSION['acesso']; ?></span>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round translate-x-[1rem]"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-[184px] dark:bg-white">
            <ul class="text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-perfil">
            <li>
                 
                <a href="../DevSidebar/editar/editarDev.php?codigo=<?php ' . urlencode($row["codigo"]) . '; ?>" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)] hover:rounded-t-md hover:bg-gray-100 dark:hover:bg-[var(--secondary)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round">
                        <circle cx="12" cy="8" r="5"/>
                        <path d="M20 21a8 8 0 0 0-16 0"/>
                    </svg>    
                    Perfil
                </a>
            </li>
            <li>
                <a href="" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)] hover:bg-gray-100 dark:hover:bg-[var(--secondary)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                    Ajuda
                </a>
            </li>
            <li>
                <a href="../../login/logout.php" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)] hover:rounded-b-md hover:bg-gray-100 dark:hover:bg-[var(--primary-emphasis)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Sair
                </a>
            </li>
            </ul>
        </div>
</nav>
   <main class="mx-1 sm:mx-16 my-8">
     <div class ="main-container">
            <!-- araa de informação do bd, botões redirecionados -->                                                          
        <div class="info">
              <a href="./emprestimo/emprestimo.php">
                  <div><img src="../icon/books.svg" alt=""></div>
                  EMPRESTIMO                                               
              </a>
              <a href="./gerenciamento_pedidos/gerenciamento.php">
                <div><img src="../icon/pedidos.svg" alt=""></div>
              <?php 
                            $tabela = 'tbpedidos';

                            $sql = "SELECT COUNT(*) As total FROM $tabela";
                            $resultado = $conn->query($sql);

                            if($resultado){
                                $linha = $resultado->fetch_assoc();
                                echo "PEDIDOS: " . $linha['total'];
                            }else{
                                echo "Erro na consulta: " . $conn->error;
                            }
                            
                          
                        ?>

              </a>
              <a href="livros">
                    <div><img src="../icon/livros.svg" alt=""></div>
                    <?php 
                            $tabela = 'tblivros';

                            $sql = "SELECT COUNT(*) As total FROM $tabela";
                            $resultado = $conn->query($sql);

                            if($resultado){
                                $linha = $resultado->fetch_assoc();
                                echo "LIVROS: " . $linha['total'];
                            }else{
                                echo "Erro na consulta: " . $conn->error;
                            }
                            
                          
                        ?>
              </a>
              <a href="usuarios">
                <img src="../icon/estudante.svg" alt="">
              <?php 
                       if ($conn->connect_error) {
                        die("Conexão falhou: " . $conn->connect_error);
                    }
                
                    $tabela = 'tbalunos';
                    $sql = "SELECT COUNT(*) As total FROM $tabela";
                    $resultado = $conn->query($sql);
                
                    if($resultado){
                        $linha = $resultado->fetch_assoc();
                        echo "ALUNOS: " . $linha['total'];
                    }else{
                        echo "Erro na consulta: " . $conn->error;
                    }
                
                    
                ?>
              </a>                                                  
        </div>

        
          <!-- grafiso e ranks -->                                                        
        <div class="info">
            <div class="container-grafico">


            <div class="grafico-1" style="width: 100%; max-width: 800px; margin: auto;">
            <?php
                // INSERÇÃO DE DADOS
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = $_POST['data_registro'];
                    $quantidade = $_POST['quantidade'];

                    $verifica = $conn->prepare("SELECT id_fluxo FROM tb_fluxo_biblioteca WHERE data_registro = ?");
                    $verifica->bind_param("s", $data);
                    $verifica->execute();
                    $verifica->store_result();

                    if ($verifica->num_rows > 0) {
                        echo "<script>alert('Já existe um registro para essa data.'); window.history.back();</script>";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO tb_fluxo_biblioteca (data_registro, quantidade) VALUES (?, ?)");
                        $stmt->bind_param("si", $data, $quantidade);

                        if ($stmt->execute()) {
                            echo "<script>alert('Registro salvo com sucesso!'); window.location.href='pagebibliotecario.php';</script>";
                        } else {
                            echo "Erro ao salvar: " . $stmt->error;
                        }
                    }
                }

                // --- Definir o ano dinamicamente ---
                $ano = date('Y');

                // --- Mapear meses em português ---
                $nomes_meses = [
                    1  => 'Janeiro',   2  => 'Fevereiro', 3  => 'Março',    4  => 'Abril',
                    5  => 'Maio',      6  => 'Junho',     7  => 'Julho',    8  => 'Agosto',
                    9  => 'Setembro', 10  => 'Outubro',  11  => 'Novembro', 12 => 'Dezembro',
                ];

                // --- Inicializa todos os meses com zero ---
                $valores_fluxo = [];
                foreach ($nomes_meses as $num => $nome) {
                    $valores_fluxo["{$nome}/{$ano}"] = 0;
                }

                // --- Query usando o número do mês e ano dinâmico ---
                $query = "
                    SELECT
                        MONTH(data_registro) AS mes_num,
                        SUM(quantidade)       AS total
                    FROM tb_fluxo_biblioteca
                    WHERE YEAR(data_registro) = {$ano}
                    GROUP BY mes_num
                    ORDER BY mes_num
                ";

                $result = $conn->query($query);
                if ($result === false) {
                    die("Erro na consulta de fluxo: " . $conn->error);
                }

                // --- Preenche os dados reais nos meses correspondentes ---
                while ($row = $result->fetch_assoc()) {
                    $mes_num = (int)$row['mes_num'];
                    if (isset($nomes_meses[$mes_num])) {
                        $label = $nomes_meses[$mes_num] . "/{$ano}";
                        $valores_fluxo[$label] = (int)$row['total'];
                    }
                }

                // --- Prepara os arrays finais para o gráfico ---
                $meses   = array_keys($valores_fluxo);
                $valores = array_values($valores_fluxo);
                ?>

                <!-- HTML -->
                <div class="grafico-1" style="width: 100%; max-width: 800px; margin: auto;">

                    <!-- Gráfico -->
                    <canvas id="graficoFluxo" style="margin-bottom: 20px;"></canvas>

                    <!-- Botão que abre o modal -->
                    <button onclick="abrirModal()" style="padding:10px 20px; background:#4CAF50; color:white; border:none; border-radius:5px; cursor:pointer;">
                        Inserir fluxo
                    </button>

                    <!-- Modal escondido por padrão -->
                    <div id="modalFluxo" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px #999; z-index:1000;">
                        <h3>Registrar fluxo de entrada</h3>
                        <form method="POST" action="">
                            <label>Data:</label><br>
                            <input type="date" name="data_registro" required><br><br>

                            <label>Quantidade:</label><br>
                            <input type="number" name="quantidade" min="1" required><br><br>

                            <button type="submit" style="padding:8px 15px;">Confirmar</button>
                            <button type="button" onclick="fecharModal()" style="padding:8px 15px;">Cancelar</button>
                        </form>
                    </div>

                </div>

                <!-- Scripts para Modal -->
                <script>
                function abrirModal() {
                    document.getElementById('modalFluxo').style.display = 'block';
                }
                function fecharModal() {
                    document.getElementById('modalFluxo').style.display = 'none';
                }
                </script>

                <!-- Chart.js -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                // Dados vindos do PHP
                const ctx = document.getElementById('graficoFluxo').getContext('2d');

                const grafico = new Chart(ctx, {
                    type: 'bar', // gráfico de barras
                    data: {
                        labels: <?php echo json_encode($meses); ?>,
                        datasets: [{
                            label: 'Visita Na Biblioteca',
                            data: <?php echo json_encode($valores); ?>,
                            backgroundColor: '#10b981', // verde esmeralda
                            borderRadius: 5,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false // oculta legenda se só tiver uma barra
                            },
                            title: {
                                display: true,
                                text: 'Visita Na Biblioteca',
                                font: {
                                    size: 18
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' entradas';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 200 // ajuste conforme escala dos seus dados
                                }
                            }
                        }
                    }
                });
                </script>
<button>Relatorio de Fluxo de alunos </button>
            </div>

                <div class="grafico 2" style="width: 100%; max-width: 800px; margin: auto;">
                <div class="grafico-emprestimo" style="width:100%;max-width:800px;margin:auto;">
                <?php
// --- 1) Definir o ano dinamicamente ---
$ano = date('Y');

// --- 2) Mapear os meses em português ---
$nomes_meses = [
    1  => 'Janeiro',   2  => 'Fevereiro', 3  => 'Março',    4  => 'Abril',
    5  => 'Maio',      6  => 'Junho',     7  => 'Julho',    8  => 'Agosto',
    9  => 'Setembro', 10  => 'Outubro',  11 => 'Novembro', 12 => 'Dezembro',
];

// --- 3) Inicializar todos os meses com zero ---
$valores_emprestimos = [];
foreach ($nomes_meses as $num => $nome) {
    $valores_emprestimos["{$nome}/{$ano}"] = 0;
}

// --- 4) Montar e executar a query ---
$sql = "
    SELECT
        MONTH(data_emprestimo) AS mes_num,
        SUM(qntd_livros)       AS total
    FROM tbemprestimos
    WHERE YEAR(data_emprestimo) = {$ano}
    GROUP BY mes_num
    ORDER BY mes_num
";

// se der erro, interrompe a execução e exibe a mensagem
$result = $conn->query($sql);
if ($result === false) {
    die("Erro na consulta de empréstimos: " . $conn->error);
}

// --- 5) Preencher o array apenas nos meses com registros ---
while ($row = $result->fetch_assoc()) {
    $mes_num  = (int)$row['mes_num'];
    $total    = (int)$row['total'];
    if (isset($nomes_meses[$mes_num])) {
        $label = $nomes_meses[$mes_num] . "/{$ano}";
        $valores_emprestimos[$label] = $total;
    }
}

// --- 6) Preparar arrays para o Chart.js ---
$meses  = array_keys($valores_emprestimos);
$valores = array_values($valores_emprestimos);
?>
<!-- ------------------- HTML do Gráfico ------------------- -->
<div class="grafico-emprestimo" style="width:100%;max-width:800px;margin:auto;">
    <canvas id="graficoEmprestimo" style="height:400px;"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('graficoEmprestimo').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($meses, JSON_UNESCAPED_UNICODE); ?>,
            datasets: [{
                label: 'Empréstimos em <?php echo $ano; ?>',
                data: <?php echo json_encode($valores); ?>,
                backgroundColor: '#10b981',
                borderRadius: 5,
                barPercentage: 0.6,
                categoryPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Empréstimos de Livros por Mês',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' empréstimos'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#e5e7eb' }
                }
            }
        }
    });
});
</script>
<button>Relatorio de  Emprestimo de livros</button>
</div>




                    <div class="grafico-cad-livro"  style="width: 100%; max-width: 800px; margin: auto;">
                    <?php
// Lista de nomes dos meses em português
$nomes_meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Inicializa os valores com zero
$valores_por_mes = [];
foreach ($nomes_meses as $num => $nome) {
    $valores_por_mes["$nome/2024"] = 0;
}

// Query para obter os dados reais
$query = "SELECT 
    MONTH(data_aquisicao) AS mes_num,
    COUNT(*) AS total
    FROM tblivros
    WHERE YEAR(data_aquisicao) = 2024
    GROUP BY mes_num
    ORDER BY mes_num";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $mes_nome = $nomes_meses[(int)$row['mes_num']] . "/2024";
    $valores_por_mes[$mes_nome] = (int)$row['total'];
}

// Arrays finais para o gráfico
$meses = array_keys($valores_por_mes);
$valores = array_values($valores_por_mes);
?>

                        <div class="grafico-1" style="width: 100%; max-width: 800px; margin: auto;">
                                    <canvas id="cadastrolivro" style="margin-bottom: 20px; height: 400px;"></canvas>
                                </div>

                                <!-- Chart.js -->
                           <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const ctx2 = document.getElementById('cadastrolivro').getContext('2d');

                                const cadastrolivro = new Chart(ctx2, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo json_encode($meses); ?>,
                                        datasets: [{
                                            label: 'Cadastro De Livro',
                                            data: <?php echo json_encode($valores); ?>,
                                            backgroundColor: '#10b981', // Verde como no exemplo
                                            borderRadius: 5,
                                            barPercentage: 0.6,
                                            categoryPercentage: 0.6
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            title: {
                                                display: true,
                                                text: 'Cadastro De Livro',
                                                font: {
                                                    size: 16
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return context.parsed.y + ' entradas';
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                ticks: {
                                                    maxRotation: 45,
                                                    minRotation: 45
                                                },
                                                grid: {
                                                    display: false
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    color: '#e5e7eb' // cinza claro
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                            </script>
                            </div>
                            <button>Relatorio de  Cadastro de livros</button>

                </div>
            </div> 
            <div class="container-rank">
            <div class="ranks-1">
    <h2>🏅 Alunos que mais emprestaram</h2>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #10b981; color: white;">

     
            <tr>
                <th>Posição</th>
                <th>Nome do Aluno</th>
                <th>RA do Aluno</th>
                <th>Total de Empréstimos</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sqlAlunos = "SELECT 
                            a.nome,
                            a.ra_aluno,
                            COUNT(e.id_emprestimo) AS total_emprestimos
                        FROM tbemprestimos e
                        JOIN tbalunos a ON e.ra_aluno = a.ra_aluno
                        GROUP BY a.ra_aluno
                        ORDER BY total_emprestimos DESC
                        LIMIT 10";

            $resultAlunos = $conn->query($sqlAlunos);
            $pos = 1;
            while ($row = $resultAlunos->fetch_assoc()) {
                echo "<tr>
                        <td>$pos</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['ra_aluno']}</td>
                        <td>{$row['total_emprestimos']}</td>
                    </tr>";
                $pos++;
            }
            ?>
        </tbody>
    </table>
    <button>Rank de Alunos</button>
</div>
                <div class="ranks 2">
                <h2>📚 Livros mais emprestados</h2>
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead style="background-color: #10b981; color: white;">
        <tr>
            <th>Posição</th>
            <th>Título do Livro</th>
            <th>ISBN do Livro</th>
            <th>Total de Empréstimos</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sqlLivros = "SELECT 
                        l.titulo,
                        e.isbn_falso,
                        COUNT(e.id_emprestimo) AS total_emprestimos
                    FROM tbemprestimos e
                    JOIN tblivros l ON e.isbn_falso = l.isbn_falso
                    GROUP BY l.isbn_falso
                    ORDER BY total_emprestimos DESC
                    LIMIT 10";

        $resultLivros = $conn->query($sqlLivros);
        $pos = 1;
        while ($row = $resultLivros->fetch_assoc()) {
            echo "<tr>
                    <td>$pos</td>
                    <td>{$row['titulo']}</td>
                    <td>{$row['isbn_falso']}</td>
                    <td>{$row['total_emprestimos']}</td>
                </tr>";
            $pos++;
        }
        ?>
    </tbody>
</table>
<button>Rank de Livros</button>
                </div>
            </div>
        </div>

    </div>

</main>


                                                  

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
                
</body>
</html>