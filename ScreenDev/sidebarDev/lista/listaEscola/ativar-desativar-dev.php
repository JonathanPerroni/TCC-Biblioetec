<?php
include_once("../../../../conexao.php");

if(isset($_GET['codigo']) && isset($_GET['acao'])){
    $codigo = (int) $_GET['codigo'];
    $novaAcaoDev = (int) $_GET['acao'];

    // Atualizar o status no banco de dados
    $sqlUpdate = "UPDATE tbdev SET  statusDev = ? WHERE codigo = ?";
    $stmt = mysqli_prepare($conn, $sqlUpdate);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "ii", $novaAcaoDev, $codigo);
        mysqli_stmt_execute($stmt);

        //redireciona de volta a lista de dev
        header("Location: lista_dev.php");

    }else{
        echo "Erro na atualização: " . mysqli_error($conn);
    }

}else{
    echo "Parametros inválidos";
}

mysqli_close($conn);

?>