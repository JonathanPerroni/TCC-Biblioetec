<?php
session_start();
include("../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');



//recebe e filtra os dados do formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"){
$cod = $_POST['cod'];
$nome = $_POST['nome'];
$email = $_POST[ 'email'];
$password = $_POST[ 'password'];
$password2 = $_POST[ 'password2'];
$telefone = $_POST[ 'telefone'];
$celular = $_POST[ 'celular'];
$cpf = $_POST[ 'cpf'];
$codigo_escola = $_POST[ 'codigo_escola'];
$acesso = $_POST[ 'acesso'];


$nome = $conn->real_escape_string($nome);
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);
$password2 = $conn->real_escape_string($password2);
$telefone = $conn->real_escape_string($telefone);
$celular = $conn->real_escape_string($celular);
$cpf = $conn->real_escape_string($cpf);
$codigo_escola = $conn->real_escape_string($codigo_escola);
$acesso = $conn->real_escape_string($acesso);


$sql = "UPDATE tbadmin SET
                nome='$nome', 
                email='$email', 
                password='$password', 
                telefone='$telefone', 
                celular='$celular', 
                cpf='$cpf', 
                codigo_escola='$codigo_escola', 
                acesso='$acesso'
                WHERE codigo=$cod";
                
  if ($conn->query($sql) === TRUE) {
    echo "Registro atualizado com sucesso!";
} else {
    echo "Erro ao atualizar registro: " . $conn->error;
}

$conn->close();


header("Location: editarAdmin.php");
exit;
} else {
echo "Método de requisição inválido!";
exit;
}


?>