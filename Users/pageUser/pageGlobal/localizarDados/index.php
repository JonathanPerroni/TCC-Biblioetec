<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leitor de XML</title>
</head>
<body>
    <h2>Upload de XML</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="xmlfile" required>
        <button type="submit">Enviar</button>
    </form>

    <?php if (isset($_SESSION['livros']) && count($_SESSION['livros']) > 0): ?>
        <h2>Livros Encontrados</h2>
        <ul>
            <?php foreach ($_SESSION['livros'] as $livro): ?>
                <li>
                    <strong><?= htmlspecialchars($livro['titulo'] ?? "Título não disponível") ?></strong><br>
                    <em>Autor:</em> <?= htmlspecialchars($livro['autor'] ?? "Autor desconhecido") ?><br>
                    <em>Editora:</em> <?= htmlspecialchars($livro['editora'] ?? "Editora desconhecida") ?><br>
                    <em>Ano de Publicação:</em> <?= htmlspecialchars($livro['ano_publicacao'] ?? "Desconhecido") ?><br>
                    <em>ISBN:</em> <?= htmlspecialchars($livro['isbn'] ?? "Não disponível") ?><br>
                    <em>Número de Páginas:</em> <?= htmlspecialchars($livro['paginas'] ?? "Não disponível") ?><br>
                    <em>Idioma:</em> <?= htmlspecialchars($livro['idioma'] ?? "Não disponível") ?><br>
                    <em>Gênero:</em> <?= htmlspecialchars($livro['genero'] ?? "Não disponível") ?><br>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
        <p><strong>Quantidade de livros:</strong> <?= count($_SESSION['livros']) ?></p>

        <!-- Formulário para exportar para CSV -->
        <form action="exportar.php" method="post">
            <button type="submit">Exportar para CSV</button>
        </form>
    <?php endif; ?>
</body>
</html>
