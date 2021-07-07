<?php

session_start();
$email = $_SESSION['email'];

$id = $_GET['id'];

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
$query = "SELECT * FROM desejo_doar";
$select = mysqli_query($conexao, $query);
// se não existir, criar a tabela
if (!$select) {
    $query = "CREATE TABLE desejo_doar (
                id int(100) NOT NULL AUTO_INCREMENT,
                id_requisicao int(100) NOT NULL,
                email_doador varchar(200) NOT NULL,
                status varchar(200) NOT NULL,
                data_solicitacao varchar(20) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARSET=utf8";

    $createTable = mysqli_query($conexao, $query);

    if (!$createTable) {
        die("Tabela não foi criada com sucesso!");
    }
}

// aprovado pode ser, aguardando, ENTREGUE, rejeitado ou aprovado.
$today = date("d/m/Y");

// verificar se a requisicao ainda existe, porque pode acontecer da pessoa remover a requisicao no mesmo tempo
// em que a pessoa disser que quer doar
$search_element = mysqli_query($conexao, "SELECT * FROM requisicoes_de_doacoes WHERE id = '$id'");
if (mysqli_num_rows($search_element) == 0) {
    // requisicao de alimento não existe mais
    echo "<script>alert('Requisição inexistente. Provavelmente a pessoa que estava requisitando a doação, cancelou o pedido.')</script>";
} else {
    // verificar se alguem já não demonstrou interesse em doar primeiro
    $search_element = mysqli_query($conexao, "SELECT * FROM desejo_doar WHERE id_requisicao = '$id' AND status = 'AGUARDANDO'");
    if (mysqli_num_rows($search_element) != 0) {
        // requisicao de alimento não existe mais
        echo "<script>alert('Olá, alguém já demonstrou interesse em doar para o solicitante selecionado. Agradecemos imensamente pelo interesse em doar.')</script>";
    } else {
        // VERIFICAR SE O DOADOR RECENTE JA NAO TENTOU DOAR E FOI REJEITADO
        $search_element = mysqli_query($conexao, "SELECT * FROM desejo_doar WHERE email_doador = '$email' AND id_requisicao = '$id' AND (status = 'REJEITADO' OR status = 'FINALIZADO')");
        if (mysqli_num_rows($search_element) == 0) {
            $query = "INSERT INTO desejo_doar 
        (id_requisicao, email_doador, status, data_solicitacao) VALUES 
        ('$id', '$email', 'AGUARDANDO', '$today')";

            $insert = mysqli_query($conexao, $query);
            if (!$insert) {
                echo "<script>alert('Erro ao acionar doação para a requisição selecionada. Tente novamente!')</script>";
            } else {
                echo "<script>alert('Você demonstrou interesse em doar o(s) alimento(s) da requisição selecionada. Aguarde a aprovação do requerente.')</script>";
                // alterar status do requisicoes_de_doacoes para acionado.
                $query = "UPDATE requisicoes_de_doacoes SET status = 'ACIONADO' WHERE id = '$id'";
                $insert = mysqli_query($conexao, $query);
                if (!$insert) {
                    echo "<script>alert('Erro ao atualizar o status na tabela de requisições de doação.')</script>";
                }
            }
        } else {
            echo "<script>alert('Infelizmente você não poderá doar os alimentos requisitados neste item. Sua doação foi anteriormente rejeitada pelo solicitante.')</script>";
        }
    }
}

header("refresh: 0.5; url = minhas_doacoes.php");
mysqli_close($conexao);
