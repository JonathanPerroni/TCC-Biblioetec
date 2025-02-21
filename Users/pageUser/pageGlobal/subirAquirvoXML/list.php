<?php
// Lê os arquivos no diretório xml
$arquivos = scandir('xml/');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivos XML</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Arquivos XML Disponíveis</h1>
        <ul>
            <?php
            foreach ($arquivos as $arquivo) {
                if ($arquivo !== '.' && $arquivo !== '..') {
                    echo "<li><a href='visualizar.php?arquivo=$arquivo'>$arquivo</a></li>";
                }
            }
            ?>
        </ul>
        <a href="unificado.php">Voltar ao Upload</a>
    </div>
</body>
</html>
