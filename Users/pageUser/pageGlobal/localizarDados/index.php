<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de XML</title>
    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        /* Estilos das abas */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            position: sticky;
            top: 0;
            background: #f5f5f5;
            z-index: 100;
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            background-color: #e0e0e0;
            cursor: pointer;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
        }

        .tab-button.active {
            background-color: #007bff;
            color: white;
        }

        /* Estilos da tabela */
        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: none;
            overflow-x: auto;
        }

        .table-container.active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            min-width: 1000px; /* Garante largura mínima para scroll */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            white-space: nowrap; /* Evita quebra de linha */
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Estilos dos botões */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-export {
            background-color: #28a745;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
        }

        .btn-search {
            background-color: #17a2b8;
            color: white;
        }

        /* Estilos do upload */
        .upload-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .file-list {
            margin-top: 20px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .file-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            position: sticky;
            bottom: 20px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }

        /* Estilo para mensagens */
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='message success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='message error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <div class="upload-container">
        <h2>Upload de XML</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="xmlfile" accept=".xml" required>
            <button type="submit" class="btn btn-export">Enviar</button>
        </form>
    </div>

    <div class="tabs">
        <button class="tab-button active" onclick="showTab('todos')">Todos os Dados</button>
        <button class="tab-button" onclick="showTab('completos')">Dados Completos</button>
        <button class="tab-button" onclick="showTab('incompletos')">Dados Incompletos</button>
    </div>

    <?php
    $xmlFiles = glob("arquivoXML/*.xml");
    if (!empty($xmlFiles)) {
        echo "<div class='file-list'>";
        echo "<h3>Arquivos XML Disponíveis</h3>";
        echo "<form action='excluir_arquivo.php' method='post'>";
        foreach ($xmlFiles as $file) {
            $filename = basename($file);
            echo "<div class='file-item'>";
            echo "<div class='file-actions'>";
            echo "<label><input type='checkbox' name='files[]' value='" . htmlspecialchars($filename) . "'> " . htmlspecialchars($filename) . "</label>";
            echo "<a href='parser.php?file=" . urlencode($filename) . "' class='btn btn-view'>Visualizar</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "<button type='submit' class='btn btn-delete'>Excluir Selecionados</button>";
        echo "</form>";
        echo "</div>";
    }
    ?>

    <div class="button-container">
        <form action="buscar_isbn.php" method="POST">
            <button type="submit" class="btn btn-search">Buscar ISBN para Todos</button>
        </form>
        <a href="export.php" class="btn btn-export">Exportar para XML</a>
    </div>

    <div id="todos" class="table-container active">
        <?php
        if (isset($_SESSION['livros']) && !empty($_SESSION['livros'])) {
            echo "<table>";
            echo "<tr>";
            foreach (array_keys($_SESSION['livros'][0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            foreach ($_SESSION['livros'] as $livro) {
                echo "<tr>";
                foreach ($livro as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>

    <div id="completos" class="table-container">
        <?php
        if (isset($_SESSION['livros']) && !empty($_SESSION['livros'])) {
            $livrosCompletos = array_filter($_SESSION['livros'], function($livro) {
                return !in_array('', $livro, true) && !in_array('N/A', $livro, true);
            });
            
            if (!empty($livrosCompletos)) {
                echo "<table>";
                echo "<tr>";
                foreach (array_keys($livrosCompletos[array_key_first($livrosCompletos)]) as $header) {
                    echo "<th>" . htmlspecialchars($header) . "</th>";
                }
                echo "</tr>";
                foreach ($livrosCompletos as $livro) {
                    echo "<tr>";
                    foreach ($livro as $valor) {
                        echo "<td>" . htmlspecialchars($valor) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>

    <div id="incompletos" class="table-container">
        <?php
        if (isset($_SESSION['livros']) && !empty($_SESSION['livros'])) {
            $livrosIncompletos = array_filter($_SESSION['livros'], function($livro) {
                return in_array('', $livro, true) || in_array('N/A', $livro, true);
            });
            
            if (!empty($livrosIncompletos)) {
                echo "<table>";
                echo "<tr>";
                foreach (array_keys($livrosIncompletos[array_key_first($livrosIncompletos)]) as $header) {
                    echo "<th>" . htmlspecialchars($header) . "</th>";
                }
                echo "</tr>";
                foreach ($livrosIncompletos as $livro) {
                    echo "<tr>";
                    foreach ($livro as $valor) {
                        echo "<td>" . htmlspecialchars($valor) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.table-container').forEach(container => {
                container.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>