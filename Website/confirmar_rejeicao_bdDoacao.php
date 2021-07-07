<?php

$conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao " . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

if (empty($bd)) {
    die("Banco de dados não encontrado");
}

$id = $_GET['id'];

$query = "UPDATE desejo_doar SET status = 'FINALIZADO' WHERE id = '$id'";

$update = mysqli_query($conexao, $query);


if (!$update) {
    echo "<script>alert('Erro ao confirmar rejeição.')</script>";
} else {
    echo "<script>alert('Rejeição confirmada!')</script>";
}

header("refresh:0.5;url=minhas_doacoes.php");
mysqli_close($conexao);

?>