<?php
include "../conexao/conexao.php";

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
    
    <link rel="stylesheet" href="../Dev/DevCss/defaults.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-screen h-screen flex flex-col items-center bg-[var(--off-white)]">
    <header class="min-w-full bg-white py-1 flex flex-col justify-center items-center shadow-md overflow-hidden">
        <a href="#" class="text-2xl  xl:text-4xl xl:mx-4 text-primary font-semibold" tabindex="-1">Biblio<span class="text-secondary">etec</span></a>
        <h1 class="text-2xl font-light text-primary">ACERVOS CADASTRADOS</h1>
    </header>  
    <main class="mt-4 mx-4 w-full flex flex-col items-center gap-4 ">
        <form name="pesquisa" action="tela_aluno.php" method="get" class="w-[75%] flex items-center justify-between gap-2 px-2 py-2 bg-white rounded-md shadow">
            <div class="w-full flex gap-2 items-center">
                <label for="pesquise" class="font-medium text-secondary text-nowrap">Pesquisa de livros:</label>
                <input type="text" name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>" class="w-full border border-secondary rounded text-secondary placeholder:text-secondary px-2 py-1">
            </div>
            <input type="submit" value="Pesquisar" class="bg-secondary text-white rounded shadow h-full px-4">
        </form>

        <div class="relative overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full text-sm text-left rtl:text-right text-[var(--secondary)]">
                <thead class="text-sm text-white uppercase bg-[var(--primary)] border border-[var(--primary-emphasis)]">
                    <tr class="">
                        <th scope="col" class="px-6 py-3">Código</th>
                        <th scope="col" class="px-6 py-3">Nome da escola</th>
                        <th scope="col" class="px-6 py-3">Título</th>
                        <th scope="col" class="px-6 py-3">Autor</th>
                        <th scope="col" class="px-6 py-3">Estante</th>
                        <th scope="col" class="px-6 py-3 text-center">Prateleira</th>
                        <th scope="col" class="px-6 py-3 text-center">Quantidade</th>
                    </tr>
                </thead>    
                <tbody class="border border-[var(--grey)]">
                    <?php
                    // Consulta com filtro de pesquisa
                    $sql = "SELECT codigo, nome_escola, titulo, autor, estante, prateleira, quantidade
                            FROM tblivros
                            WHERE titulo LIKE '%$pesquisa_escapada%'
                            ORDER BY codigo";
                    $result = mysqli_query($conn, $sql);
                    // Verificação de erros na consulta
                    if (!$result) {
                        echo "Erro na consulta: " . mysqli_error($conn);
                    }
                    // Exibição dos dados
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $codigo = htmlspecialchars($row['codigo']);
                            $nome_escola = htmlspecialchars($row['nome_escola']);
                            $titulo = htmlspecialchars($row['titulo']);
                            $autor = htmlspecialchars($row['autor']);
                            $estante = htmlspecialchars($row['estante']);
                            $prateleira = htmlspecialchars($row['prateleira']);
                            $quantidade = htmlspecialchars($row['quantidade']);
                            echo '<tr class="odd:bg-white even:bg-[var(--off-white)] border-b border-[var(--grey)]">';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$codigo</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$nome_escola</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$titulo</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$autor</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$estante</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$prateleira</td>';
                            echo    '<td scope="row" class="px-6 py-4 font-medium text-[var(--secondary)] whitespace-nowrap border-r border-[var(--grey)]">$quantidade</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Nenhum dado encontrado.</td></tr>";
                    }
                    // Fechar a conexão
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>