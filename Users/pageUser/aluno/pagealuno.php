<?php
session_start();
ob_start();

date_default_timezone_set('America/Sao_Paulo');

include_once("../../../conexao/conexao.php");

//Validação de login, só entra se estiver logado
if(empty($_SESSION['email'])){
    echo $_SESSION['nome'];
    echo $_SESSION['acesso'];

    $_SESSION['msg'] = "Faça o Login!!";
    header("Location: ../../login/login.php ");
    exit();
}
if (isset($_GET["pesquisa"])) {
    $pesquisa = $_GET["pesquisa"];
} else {
    $pesquisa = ''; // Definir um valor padrão se a pesquisa não estiver definida
}

// Escapar o valor da pesquisa para evitar injeção de SQL
$pesquisa_escapada = mysqli_real_escape_string($conn, $pesquisa);

// Definir o charset para UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioEtec</title>
    
    <link rel="stylesheet" href="../../UserCss/defaults.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-screen h-screen flex flex-col items-center bg-[var(--off-white)]">
    <header class="min-w-full bg-white py-1 flex flex-row justify-between items-center shadow-md overflow-hidden ">
        <a href="#" class="text-2xl  xl:text-4xl xl:mx-4 text-primary font-semibold" tabindex="-1">Biblio<span class="text-secondary">etec</span></a>
        <h1 class="text-2xl font-light text-primary">ACERVOS CADASTRADOS</h1>
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
    </header>  
    <main class="mt-1 mx-1 md:mt-4 md:mx-4 w-full flex flex-col items-center gap-4 ">
        <form name="pesquisa" action="pagealuno.php" method="get" class="w-full md:w-[75%] flex flex-column md:flex-row items-center justify-between gap-2 px-2 py-2 bg-white rounded-md shadow">
            <div class="w-full flex flex-column justify-center gap-2 items-center">
                <label for="pesquise" class="font-medium text-secondary text-nowrap">Pesquisa de livros:</label>
                <input type="text" name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>" class="w-full border border-secondary rounded text-secondary placeholder:text-secondary px-2 py-1">
            </div>
            <input type="submit" value="Pesquisar" class="bg-secondary text-white rounded shadow h-full px-4">
        </form>

        <div class="w-full md:w-[75%] relative overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full text-sm text-left text-[var(--secondary)]">
                <thead class="text-sm text-white uppercase bg-[var(--primary)]">
                    <tr>
                        <!-- A coluna de código está oculta -->
                        <th class="px-6 py-3" style="display: none;">Código</th>
                        <th class="px-6 py-3">Escola</th>
                        <th class="px-6 py-3">ISBN</th> <!-- Nova coluna para ISBN -->
                        <th class="px-6 py-3">Título</th>
                        <th class="px-6 py-3">Autor</th>
                        <th class="px-6 py-3">Estante</th>
                        <th class="px-6 py-3">Prateleira</th>
                        <th class="px-6 py-3">Quantidade</th>
                      
          
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta no banco com filtro
                    $sql = "SELECT codigo, codigo_escola, titulo, autor, estante, prateleira, quantidade, isbn
                            FROM tblivros
                            WHERE titulo LIKE '%$pesquisa_escapada%'
                            ORDER BY codigo";
                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                           
                        
                            
                            echo '<tr class="border-b">';
                            // A célula de código está oculta
                            echo '<td class="px-6 py-4" style="display: none;">' . htmlspecialchars($row['codigo']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['codigo_escola']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['isbn']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['titulo']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['autor']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['estante']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['prateleira']) . '</td>';
                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['quantidade']) . '</td>';
                            
                           
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9" class="px-6 py-4 text-center">Nenhum resultado encontrado.</td></tr>';
                    }

                    // Fecha a conexão
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>

</html>