<?php

$email = $_POST['user_email'];
$id_endereco = $_POST['id_endereco'];
$dataLimite = date("d/m/Y", strtotime($_POST['user_dataLimite']));
$tipoAlimento = $_POST['tipo_alimento'];
$oque = $_POST['user_quaisAlimentos'];
$porque = $_POST['user_motivacaoParaReceberDoacao'];

$conexao = mysqli_connect('localhost', 'root', '') or die('Erro de conexão' . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

// se banco de dados não existe, criar outro
if (empty($bd)) {
    $query = "CREATE DATABASE donate_system DEFAULT CHARSET=utf8";
    $createBD = mysqli_query($conexao, $query);
    // se o banco de dados não tiver sido criado
    if (!$createBD) {
        die("Erro ao criar banco de dados");
    }
}

// se chegou aqui, BD existe ou foi criado
// agora verificar se tabela users existe, se não existir, criar
$query = "SELECT * FROM requisicoes_de_doacoes";
$select = mysqli_query($conexao, $query);
// se não existir, criar a tabela
if (!$select) {
    $query = "CREATE TABLE requisicoes_de_doacoes (
                id int(100) NOT NULL AUTO_INCREMENT,
                email varchar(200) NOT NULL,
                id_endereco int(100) NOT NULL,
                dataLimiteEntrega varchar(20) NOT NULL,
                tipoAlimento varchar(30) NOT NULL,
                oqueReceber varchar(500) NOT NULL,
                porqueReceber varchar(500) NOT NULL,
                status varchar(100) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARSET=utf8";

    $createTable = mysqli_query($conexao, $query);

    if (!$createTable) {
        die("Tabela não foi criada com sucesso!");
    }
}

// se status aguardando, usuario atual nao pode fazer requisicao. agora se for inspirado ou atendido, ok.
$search_element = mysqli_query($conexao, "SELECT * FROM requisicoes_de_doacoes WHERE email = '$email' AND (status = 'AGUARDANDO' OR status = 'ACIONADO' OR status = 'APROVADO')");
// se não tiver encontrado, dado não existe na tabela
if (mysqli_num_rows($search_element) == 0) {
    $query = "INSERT INTO requisicoes_de_doacoes (email, id_endereco, dataLimiteEntrega, tipoAlimento, oqueReceber, porqueReceber, status) VALUES ('$email', '$id_endereco', '$dataLimite', '$tipoAlimento', '$oque', '$porque', 'AGUARDANDO')";
    $insert = mysqli_query($conexao, $query);
    if (!$insert) {
        echo "<script>alert('Erro ao requisitar doação. Tente novamente!')</script>";
    } else {
        echo "<script>alert('Alimento requisitado com sucesso. Aguarde respostas.')</script>";
    }
} else {
    echo "<script>alert('Você possui uma requisição de doação ativa no sistema. Só é possivel fazer uma requisição por vez.')</script>";
}
header("refresh: 0.5; url = minhas_requisicoes.php");
mysqli_close($conexao);
