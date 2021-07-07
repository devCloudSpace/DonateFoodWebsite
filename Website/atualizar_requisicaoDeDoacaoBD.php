<?php

$conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao " . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

if (empty($bd)) {
    die("Banco de dados não encontrado");
}

$id = $_POST['id_requisicao'];


$dataLimite = date("d/m/Y", strtotime($_POST['user_dataLimite_atualizar']));
$tipoAlimento = $_POST['tipo_alimento_atualizar'];
$oque = $_POST['user_quaisAlimentos_atualizar'];
$porque = $_POST['user_motivacaoParaReceberDoacao_atualizar'];


$query = "UPDATE requisicoes_de_doacoes SET dataLimiteEntrega = '$dataLimite',
            tipoAlimento = '$tipoAlimento', oqueReceber = '$oque', porqueReceber = '$porque' WHERE id = '$id'";
$update = mysqli_query($conexao, $query);


if (!$update) {
    echo "<script>alert('Erro na atualização da tabela de requisição de doações.</script>";
} else {
    echo "<script>alert('Dados da requisiçao de alimento foi atualizado com sucesso!')</script>";
}

header("refresh:0.5;url=minhas_requisicoes.php");
mysqli_close($conexao);

?>