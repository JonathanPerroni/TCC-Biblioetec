<?php
session_start();
$visualizacaoHTML = $_SESSION['visualizacao'] ?? null;
unset($_SESSION['visualizacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload e Cadastro de Livros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Upload e Cadastro de Livros</h1>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="arquivo" accept=".xml">
            <button type="submit" name="enviar">Enviar Arquivo</button>
        </form>

        <?php if (isset($_SESSION['arquivo_enviado'])): ?>
            <form action="visualizar.php" method="POST">
                <button name="visualizar">Visualizar</button>
            </form>

            <form action="salvar.php" method="POST">
                <input type="text" name="cadastrado_por" placeholder="Seu nome" required>
                <button name="salvar">Salvar no Banco de Dados</button>
            </form>

            <form action="excluir.php" method="POST">
                <button name="excluir">Excluir Arquivo</button>
            </form>
        <?php endif; ?>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>

        <?php if (!empty($visualizacaoHTML)): ?>
            <div class="visualizacao"><?php echo $visualizacaoHTML; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
