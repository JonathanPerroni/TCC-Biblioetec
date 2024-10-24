<?php

session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

 include("../../../conexao/conexao.php");


//Validação de login, só entra se estiver logado
//verifica se o email nao sessão esta vazio, ou seja, se o usuario esta loga 
if(empty($_SESSION['email'])){
    //exibe o nome eo nivel de acesso do usuario
    echo $_SESSION['nome'];
    echo $_SESSION['acesso'];

       
            //mensagem e redirecionamento para pagina de login de desenvolvedor
            $_SESSION['msg'] = "Faça o login!!";
            header("Location: ../../loginDev.php");
            exit();
       
        
      
             

}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Administradores</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../DevCss/defaults.css">
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
        <aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full " aria-label="Sidebar">
            <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-[var(--primary-emphasis)]">
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="../../DevScreen/pagedevNew.php" class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        <span class="ms-3">Início</span>
                        </a>
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
                                <a href="../list/listadevNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Devs</a>
                            </li>
                            <li>
                                <a href="../list/listaadminNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Admins</a>
                            </li>
                            <li>
                                <a href="../list/listaescolaNew.php" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Lista Escolas</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <button type="button" class="flex w-full items-center p-2 text-white rounded-lg dark:text-white hover:bg-[var(--primary)] dark:hover:bg-[var(--primary)] group" aria-controls="dropdown-cadastro" data-collapse-toggle="dropdown-cadastro">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-plus"><path d="M11 12H3"/><path d="M16 6H3"/><path d="M16 18H3"/><path d="M18 9v6"/><path d="M21 12h-6"/></svg>                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Cadastrar</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-cadastro" class="hidden py-2 space-y-2">
                            <li>
                                <a href="#" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Devs</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Admins</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center w-full p-2 text-white transition duration-75 rounded-lg pl-11 group hover:bg-[var(--primary)] dark:text-white dark:hover:bg-[var(--primary)]">Cadastrar Escolas</a>
                            </li>
                        </ul>
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
                <a href="#" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)]  hover:rounded-t-md hover:bg-gray-100 dark:hover:bg-[var(--secondary)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>    
                    Perfil
                </a>
            </li>
            <li>
                <a href="#" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)] hover:bg-gray-100 dark:hover:bg-[var(--secondary)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                    Ajuda
                </a>
            </li>
            <li>
                <a href="../../DevScreen/logout.php" class="flex gap-4 justify-start items-center px-4 py-2 text-[var(--secondary)] hover:rounded-b-md hover:bg-gray-100 dark:hover:bg-[var(--secondary)] dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Sair
                </a>
            </li>
            </ul>
        </div>
    </nav>

    <main class="mx-1 sm:mx-16 my-8">
        <div class="relative overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full text-sm text-left rtl:text-right text-[var(--secondary)]">
                <thead class="text-sm text-white uppercase bg-[var(--primary)] border border-[var(--primary-emphasis)]">
                    <tr class="">
                        <th scope="col" class="px-6 py-3">Nome</th>
                        <th scope="col" class="px-6 py-3">Código Etec</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Telefone</th>
                        <th scope="col" class="px-6 py-3">CPF</th>
                        <th scope="col" class="px-6 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="border border-[var(--grey)]">
                <?php
                   
                    $sql = "SELECT codigo, nome, email, telefone, codigo_escola, cpf FROM tbadmin";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {

                            echo '
                            <tr class="odd:bg-white even:bg-[var(--off-white)] border-b border-[var(--grey)]">
                                <th scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">
                                    ' . htmlspecialchars($row["nome"]) . '
                                </th>
                                <td class="px-6 py-4 border-r border-[var(--grey)]">' . htmlspecialchars($row["codigo_escola"]) . '</td>
                                <td class="px-6 py-4 border-r border-[var(--grey)]">' . htmlspecialchars($row["email"]) . '</td>
                                <td class="px-6 py-4 border-r border-[var(--grey)]">' . htmlspecialchars($row["telefone"]) . '</td>
                                    <td class="px-6 py-4 border-r border-[var(--grey)]">' . htmlspecialchars($row["cpf"]) . ' </td>   
                                <td class="flex justify-between md:justify-evenly gap-1 px-6 py-4">
                                    <a href="../editar/editarAdmin.php?cod=' . urlencode($row["codigo"]) . '" class="font-medium text-blue-600 hover:underline">Editar</a>
                                    <a href="#" class="font-medium text-red-600 hover:underline">Excluir</a>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center px-6 py-4">Nenhum dado encontrado</td></tr>';
                    }

                    mysqli_close($conn);
                ?>
                </tbody>
            </table>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>