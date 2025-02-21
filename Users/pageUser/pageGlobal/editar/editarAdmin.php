<?php
session_start();

ob_start(); // Inicia o buffer de saída
include("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $codigo = $conn->real_escape_string($codigo);

    $sql = "SELECT * FROM tbadmin WHERE codigo = $codigo";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Registro não encontrado!";
        exit;
    }
} else {
    echo "ID não fornecido!";
    exit;
}

// Consulta para os dados da ETEC
$sqlEtec = "SELECT codigo_escola, unidadeEscola FROM dados_etec";
$resultEtec = $conn->query($sqlEtec);

$dadosEtec = [];
if ($resultEtec && $resultEtec->num_rows > 0) {
    while ($rowEtec = $resultEtec->fetch_assoc()) {
        $dadosEtec[] = $rowEtec;
    }
}

// Validação de login, só entra se estiver logado
if (empty($_SESSION['email'])) {
    // echo  $_SESSION['nome'];
    // echo  $_SESSION['acesso'];
    $_SESSION['msg'] = "Faça o Login!!";
    header("Location:  ../../../../loginDev.php");
    exit();
}

// Verifica se há mensagem na sessão
if (isset($_SESSION['sucesso'])) {
    $sucesso = htmlspecialchars($_SESSION['sucesso'], ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$sucesso');</script>";
    // Limpa a mensagem da sessão
    unset($_SESSION['sucesso']);
}


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../UserCss/defaults.css">
</head>
<body class="w-100 h-auto d-flex flex-column align-items-center">
    <header class="container-fluid d-flex justify-content-center align-items-center bg-white py-2 px-4 shadow">
        <a href="../list/listaadminNew.php" class="d-flex align-items-center position-absolute start-0 ms-4 nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <span class="fw-medium">Voltar</span>
        </a>
        <a href="#" class="nav-link fs-3 fw-medium text-primary">Editar Cadastro</a>
    </header>

    <div class="container-sm my-4 bg-white shadow p-4 rounded-3">
        <p class="text-primary">
            <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
            ?>
        </p>

        <form action="attAdmin.php" method="POST" class="d-flex flex-column gap-4">
            <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($row['codigo']); ?>">

            <div>
                <label for="nome" class="form-label">Nome completo:</label>
                <input type="text" name="nome" required class="form-control" value="<?php echo htmlspecialchars($row['nome']); ?>">
            </div>

            <div>
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" required class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>">
            </div>

            <div>
                <label for="password" class="form-label">Senha:</label>
                <input type="password" name="password" required class="form-control" value="<?php echo htmlspecialchars($row['password']); ?>">
            </div>

            <div>
                <label for="password2" class="form-label">Confirme a Senha:</label>
                <input type="password" name="password2" required class="form-control" value="<?php echo htmlspecialchars($row['password']); ?>">
            </div>

            <div>
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="tel" name="telefone" class="form-control" value="<?php echo htmlspecialchars($row['telefone']); ?>">
            </div>

            <div>
                <label for="celular" class="form-label">Celular:</label>
                <input type="tel" name="celular" required class="form-control" value="<?php echo htmlspecialchars($row['celular']); ?>">
            </div>

            <div>
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" name="cpf" required class="form-control" value="<?php echo htmlspecialchars($row['cpf']); ?>">
            </div>

            <div class="d-flex gap-4">
                <div class="w-50">
                    <label for="codigo_escola" class="form-label">Código Etec:</label>
                    <input type="text" id="codigo_escola" name="codigo_escola" list="codigos" required class="form-control" value="<?php echo htmlspecialchars($row['codigo_escola']); ?>">
                    
                    <datalist id="codigos">
                        <?php foreach ($dadosEtec as $escola): ?>
                            <option value="<?php echo $escola['codigo_escola']; ?>" data-nome="<?php echo $escola['unidadeEscola']; ?>">
                                <?php echo $escola['codigo_escola'] . " - " . $escola['unidadeEscola']; ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="w-50">
                    <label for="nome_escola" class="form-label">Nome Etec:</label>
                    <input type="text" id="nome_escola" name="nome_escola" class="form-control" value="<?php echo isset($row['nome_escola']) ? htmlspecialchars($row['nome_escola']) : ''; ?>" readonly>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codigoEscolaInput = document.getElementById('codigo_escola');
            const nomeEscolaInput = document.getElementById('nome_escola');
            const datalistOptions = document.querySelectorAll('#codigos option');

            codigoEscolaInput.addEventListener('input', function () {
                const codigoSelecionado = this.value.trim();
                let nomeEncontrado = '';

                datalistOptions.forEach(option => {
                    if (option.value === codigoSelecionado) {
                        nomeEncontrado = option.getAttribute('data-nome') || option.textContent.split(' - ')[1];
                    }
                });

                nomeEscolaInput.value = nomeEncontrado;
            });
        });
    </script>
</body>
</html>