<?php

$id = $_GET['id'];

$conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao " . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");
if (empty($bd)) {
    $criaBD = mysqli_query($conexao, "CREATE DATABASE donate_system DEFAULT CHARSET=utf8");
    if (!$criaBD) {
        die("Erro ao criar banco de dados");
    }
}

// alterar status do requisicoes_de_doacoes para Aguardando. pegar o id da requisicao
$query = "SELECT id_requisicao FROM desejo_doar WHERE id = '$id'";
$select = mysqli_query($conexao, $query);
if ($select) {
    $id_requisicao = mysqli_fetch_array($select)['id_requisicao'];
}

// if tabela existe
if (mysqli_query($conexao, "SELECT * FROM desejo_doar")) {
    $query = "DELETE FROM desejo_doar WHERE id = '$id'";
    $delete = mysqli_query($conexao, $query);
    if ($delete) {
        echo "<script>alert('Você retirou o interesse em doar o(s) alimento(s) para o solicitante.')</script>";
        
        $query = "UPDATE requisicoes_de_doacoes SET status = 'AGUARDANDO' WHERE id = '$id_requisicao'";
        $insert = mysqli_query($conexao, $query);
        if (!$insert) {
            echo "<script>alert('Erro ao atualizar o status na tabela de requisições de doação.')</script>";
        }
    }
}
header("refresh:0.5;url=minhas_doacoes.php");
mysqli_close($conexao);
