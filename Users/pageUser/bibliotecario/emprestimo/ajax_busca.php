<?php
// ajax_busca.php
session_start();
require_once '../../../../conexao/conexao.php'; // Ajuste o caminho se necessário

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = $_POST['tipo'];
    $conn = new mysqli("localhost", "root", "", "bdescola"); // Atualize se necessário

    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // ===============================
    // BUSCA DE ALUNOS
    // ===============================
    if ($tipo === 'aluno' && isset($_POST['aluno'])) {
        $input = $_POST['aluno'];
        $query = "SELECT * FROM tbalunos WHERE nome LIKE ? OR ra_aluno LIKE ?";
        $stmt = $conn->prepare($query);
        $busca = "%" . $input . "%";
        $stmt->bind_param("ss", $busca, $busca);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            echo "<select id='selecaoAluno' onchange='selecionarAluno()'>";
            echo "<option disabled selected>Escolha um aluno</option>";
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['codigo']}' data-nome='" . htmlspecialchars($row['nome']) . "'>{$row['ra_aluno']} - {$row['nome']}</option>";
            }
            echo "</select>";
        } else {
            echo "Nenhum aluno encontrado.";
        }
        exit();
    }

    // ===============================
    // BUSCA DE LIVROS
    // ===============================
    if ($tipo === 'livro' && isset($_POST['livro'])) {
        $input = $_POST['livro'];
        $query = "SELECT * FROM tblivros WHERE titulo LIKE ? OR tombo LIKE ?";
        $stmt = $conn->prepare($query);
        $busca = "%" . $input . "%";
        $stmt->bind_param("ss", $busca, $busca);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            echo "<select id='selecaoLivro' onchange='selecionarLivro()'>";
            echo "<option disabled selected>Escolha um livro</option>";
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['codigo']}' data-titulo='" . htmlspecialchars($row['titulo']) . "'>{$row['tombo']} - {$row['titulo']}</option>";
            }
            echo "</select>";
        } else {
            echo "Nenhum livro encontrado.";
        }
        exit();
    }
}
?>
