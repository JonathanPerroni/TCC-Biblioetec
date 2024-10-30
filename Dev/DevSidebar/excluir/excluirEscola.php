<?php
session_start();
include("../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');



//recebe e filtra os dados do formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"){
$cod = $_POST['codigo'];


$sql = "DELETE from tbescola WHERE codigo = $cod";
                
  if ($conn->query($sql) === TRUE) {
    echo "Registro excluído com sucesso!";
} else {
    echo "Erro ao excluir registro: " . $conn->error;
}

$conn->close();

// USAR UM OU OUTRO

// VOLTAR PARA PÁGINA DE EDITAR
//header("Location: ./editarAdmin.php?cod=" . urlencode($cod));

// VOLTAR PARA PÁGINA LISTAR
header("Location: ../list/listaescolaNew.php");

exit;
} else {
echo "Método de requisição inválido!";
exit;
}


?>