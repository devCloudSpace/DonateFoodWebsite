<?php

$email = $_POST['email_users'];
$cep = $_POST['cep_user'];
$cidade = $_POST['cidade_user'];
$bairro = $_POST['bairro_user'];
$endereco = $_POST['endereco_user'];
$complemento = $_POST['complemento_user'];

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
$query = "SELECT * FROM endereco_user";
$select = mysqli_query($conexao, $query);
// se não existir, criar a tabela
if (!$select) {
    $query = "CREATE TABLE endereco_user (
                id int(100) NOT NULL AUTO_INCREMENT,
                email varchar(300) NOT NULL,
                cidade varchar(300) NOT NULL,
                cep varchar(20) NOT NULL,
                complemento varchar(300) NOT NULL,
                endereco varchar(300) NOT NULL,
                bairro varchar(300) NOT NULL,
                PRIMARY KEY(id)            
            ) DEFAULT CHARSET=utf8";

    $createTable = mysqli_query($conexao, $query);

    if (!$createTable) {
        die("Tabela não foi criada com sucesso!");
    }
}

$search_element = mysqli_query($conexao, "SELECT * FROM endereco_user WHERE email = '$email'");
// se não tiver encontrado, dado não existe na tabela, então simplesmente cadastrar
if (mysqli_num_rows($search_element) == 0) {
    $query = "INSERT INTO endereco_user (email, cidade, cep, complemento, endereco, bairro) VALUES ('$email', '$cidade', '$cep', '$complemento', '$endereco', '$bairro')";
    $insert = mysqli_query($conexao, $query);
    if ($insert) {
        echo "<script>alert('Endereço cadastrado com sucesso para o usuário com email: $email!')</script>";
    } else {
        echo "<script>alert('Erro ao inserir endereço para o usuário com email: $email!')</script>";
    }
} else {
    // se chegou aqui é porque o usuario ja cadastrou email, então atualizar endereço
    $query = "UPDATE endereco_user SET
                    cidade = '$cidade', 
                    cep = '$cep', 
                    complemento = '$complemento',
                    endereco = '$endereco', 
                    bairro = '$bairro'
                WHERE email = '$email'";
    $insert = mysqli_query($conexao, $query);
    if ($insert) {
        echo "<script>alert('Endereço atualizado com sucesso para o usuário com email: $email!')</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o endereço para o usuário com email: $email!')</script>";
    }
}
header("refresh: 0.5; url = endereco_usuario.php");
mysqli_close($conexao);
