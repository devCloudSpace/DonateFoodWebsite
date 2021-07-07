<?php

$conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao " . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

if (empty($bd)) {
    die("Banco de dados não encontrado");
}

$id = $_GET['id'];

$getTodasConsultas = "SELECT * FROM desejo_doar WHERE id = '$id'";

$id_requisicao = "";

$select = mysqli_query($conexao, $getTodasConsultas);
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $id_requisicao = $info['id_requisicao'];
    }
}

$deucerto = true;

$query = "UPDATE desejo_doar SET status = 'ENTREGUE' WHERE id = '$id'";
$update = mysqli_query($conexao, $query);


if (!$update) {
    $deucerto = false;
}

$query = "UPDATE requisicoes_de_doacoes SET status = 'ENTREGUE' WHERE id = '$id_requisicao'";
$update = mysqli_query($conexao, $query);

if (!$update) {
    $deucerto = false;
}

// se rejeitar ou aprovar, remover o chat com a pessoa atual
$query = "DELETE FROM chat WHERE id_requisicao = '$id_requisicao'";
$delete = mysqli_query($conexao, $query);

if (!$delete) {
    echo "<script>alert('Erro ao remover mensagens enviadas para o item selecionado').</script>";
}

if (!$deucerto) {
    echo "<script>alert('Erro a confirma entraga.')</script>";
} else {
    echo "<script>alert('Entrega confirmada, parabéns por essa doação!')</script>";
}

header("refresh:0.5;url=minhas_doacoes.php");
mysqli_close($conexao);

?>