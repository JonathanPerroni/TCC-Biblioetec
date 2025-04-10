<?php
session_start();
include_once 'api.php';  // Supondo que as funções de API já estão aqui

// Verificar se o parâmetro 'file' foi passado na URL
if (!isset($_GET['file'])) {
    header('Location: index.php');
    exit;
}

// Caminho do arquivo XML
$xmlFile = "arquivoXML/" . basename($_GET['file']);
if (!file_exists($xmlFile)) {
    $_SESSION['error'] = "Arquivo XML não encontrado.";
    header('Location: index.php');
    exit;
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    $_SESSION['error'] = "Erro ao carregar o arquivo XML.";
    header('Location: index.php');
    exit;
}

// Processamento do XML
$_SESSION['livros'] = processarXML($xml);
header('Location: index.php');
exit;

// Função para processar o XML e extrair os livros
function processarXML($xml) {
    $livros = [];
    $livrosCompletos = [];
    $livrosComFaltando = [];
    $totalLivros = 0;
    $totalCompletos = 0;
    $colunas = [];

    // Definir os campos obrigatórios
    $camposObrigatorios = [
        'TOMBO', 'ISBN', 'AQUISIÇÃO', 'TITULO', 'GENERO', 
        'AUTOR', 'EDITORA', 'ANO DE PUBLICAÇÃO', 'NUMERO DE PAGINAS', 
        'CDD/CUTTER', 'IDIOMA'
    ];

    // Registrar o namespace do XML
    $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
    $rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');

    if (empty($rows)) {
        die("Nenhuma linha encontrada no XML.");
    }

    // Percorrer as linhas do XML
    foreach ($rows as $index => $row) {
        $cells = $row->xpath('ss:Cell/ss:Data');

        if ($index === 0) {
            // Primeira linha contém os nomes das colunas
            foreach ($cells as $cell) {
                $colunas[] = trim((string)$cell);
            }
        } else {
            // Demais linhas são os dados dos livros
            $livro = [];
            foreach ($cells as $key => $cell) {
                $coluna = $colunas[$key] ?? "Coluna_$key";
                $livro[$coluna] = trim((string)$cell); // Usa o nome da coluna ou um nome genérico
            }
            $livros[] = $livro;

            // Verifica se o livro tem todas as informações preenchidas
            $livroCompleto = true;

            foreach ($camposObrigatorios as $campo) {
                if (!isset($livro[$campo]) || empty($livro[$campo]) || in_array(strtolower($livro[$campo]), ['n/a', 'desconhecido', 'undefined', 'null'])) {
                    $livroCompleto = false;
                    break;
                }
            }

            // Classifica o livro como completo ou com informações faltando
            if ($livroCompleto) {
                $livrosCompletos[] = $livro;
                $totalCompletos++;
            } else {
                $livrosComFaltando[] = $livro;
            }

            $totalLivros++;
        }
    }

    // Armazenar na sessão os livros completos e com falta de informações
    $_SESSION['livrosCompletos'] = $livrosCompletos;
    $_SESSION['livrosComFaltando'] = $livrosComFaltando;
    $_SESSION['totalLivros'] = $totalLivros;
    $_SESSION['totalCompletos'] = $totalCompletos;
    
    return $livros;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros Importados</title>
    <style>
        .tabs {
            display: flex;
            margin-bottom: 10px;
        }
        .tab {
            padding: 10px;
            cursor: pointer;
            background: #ddd;
            margin-right: 5px;
            border-radius: 5px;
        }
        .tab.active {
            background: #bbb;
        }
        .table-container {
            display: none;
        }
        .table-container.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="tabs">
        <div class="tab active" onclick="showTab('todos')">Todos os Dados</div>
        <div class="tab" onclick="showTab('completos')">Dados Completos</div>
        <div class="tab" onclick="showTab('incompletos')">Dados Incompletos</div>
    </div>

    <!-- Todos os Dados -->
    <div id="todos" class="table-container active">
        <h2>Todos os Livros</h2>
        <table>
            <tr>
                <?php foreach ($_SESSION['livros'][0] as $coluna => $valor) echo "<th>" . htmlspecialchars($coluna) . "</th>"; ?>
            </tr>
            <?php foreach ($_SESSION['livros'] as $livro) { 
                echo "<tr>";
                foreach ($livro as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            } ?>
        </table>
        <form action="buscar_isbn.php" method="POST">
            <button type="submit">Buscar ISBN para Todos</button>
        </form>
    </div>

    <!-- Dados Completos -->
    <div id="completos" class="table-container">
        <h2>Livros Completos</h2>
        <table>
            <tr>
                <?php foreach ($_SESSION['livrosCompletos'][0] as $coluna => $valor) echo "<th>" . htmlspecialchars($coluna) . "</th>"; ?>
            </tr>
            <?php foreach ($_SESSION['livrosCompletos'] as $livro) { 
                echo "<tr>";
                foreach ($livro as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            } ?>
        </table>
    </div>

    <!-- Dados Incompletos -->
    <div id="incompletos" class="table-container">
        <h2>Livros Incompletos</h2>
        <table>
            <tr>
                <?php foreach ($_SESSION['livrosComFaltando'][0] as $coluna => $valor) echo "<th>" . htmlspecialchars($coluna) . "</th>"; ?>
            </tr>
            <?php foreach ($_SESSION['livrosComFaltando'] as $livro) { 
                echo "<tr>";
                foreach ($livro as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            } ?>
        </table>
    </div>
    
    <script>
        function showTab(tabName) {
            // Ocultar todas as abas
            document.querySelectorAll('.table-container').forEach(function(tab) {
                tab.classList.remove('active');
            });
            // Exibir a aba selecionada
            document.getElementById(tabName).classList.add('active');
        }
    </script>
</body>
</html>
