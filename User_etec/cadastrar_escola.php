<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de ETEC</title>
</head>
<body>

    <div class="header">
        <h1>Cadastrar ETEC</h1>        
        <a href="index.php">Início</a>
        <br><br>
    </div>  

    <div class="form-cadastro">
        <form action="" method="POST">
            <input type="text" name="nome_escola" placeholder="Nome da Etec" required>
            <input type="text" name="tipo_escola" placeholder="Tipo de Etec" required>
            <input type="text" name="codigo_escola" placeholder="Código da Etec" required>
            <input type="text" name="endereco" placeholder="Endereço" required>
            <input type="text" name="bairro" placeholder="Bairro" required>
            <input type="text" name="cidade" placeholder="Cidade" required>
            <input type="text" name="estado" placeholder="Estado" required>
            <input type="text" name="cnpj" placeholder="CNPJ" required>
            <input type="text" name="telefone" placeholder="Telefone">
            <input type="text" name="celular" placeholder="Celular" required>            

            <button type="reset">Limpar</button>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
        $tipo_escola = filter_input(INPUT_POST, 'tipo_escola', FILTER_SANITIZE_EMAIL);
        $codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
        $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
        $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
        $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
        $cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);        

        if (!preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', $cnpj)) {
            echo "<p>CNPJ inválido. Use o formato XX.XXX.XXX/XXXX-XX.</p>";
        }

        $conn = new mysqli("localhost", "root", "", "bdescola");

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO tbescola (nome_escola, tipo_escola, codigo_escola, 
        endereco, bairro, cidade, estado, cnpj, telefone, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nome_escola, $tipo_escola, $codigo_escola, $endereco, 
        $bairro, $cidade, $estado, $cnpj, $telefone, $celular);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Ocorreu um erro ao cadastrar o usuário.</p>";
        }

        $conn->close();
    }

?>


    
</body>
</html>