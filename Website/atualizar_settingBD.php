<?php

session_start();

// se sessão está setada, então existe tabela users
if (isset($_SESSION['email'])) {

    $email = $_SESSION['email'];
    $email_novo = $_POST['email_users'];
    $telefone_users = $_POST['telefone_users'];

    if (!filter_var($email_novo, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Endereço de email inválido')</script>";
        header("refresh: 0.5; url = settings.php");
    } else {


        $senha = $_POST['setting_senha_nova1'];
        $nome = $_POST['nome_users'];

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
        $query = "SELECT * FROM users";
        $select = mysqli_query($conexao, $query);
        // se não existir, criar a tabela
        if (!$select) {
            $query = "CREATE TABLE users (
                id int(100) NOT NULL AUTO_INCREMENT,
                email varchar(200) NOT NULL,
                senha varchar(30) NOT NULL,
                genero varchar(20) NOT NULL,
                cpf varchar(30) NOT NULL,
                nome varchar(200) NOT NULL,
                telefone varchar(50) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARSET=utf8";

            $createTable = mysqli_query($conexao, $query);

            if (!$createTable) {
                die("Tabela não foi criada com sucesso!");
            }
        }

        $search_element = mysqli_query($conexao, "SELECT * FROM users WHERE email = '$email'");

        // se senha não estiver setada, pega a senha atual no bd para mante-lá
        if (!$senha) {
            $query = "SELECT senha FROM users WHERE email = '$email'";
            $select = mysqli_query($conexao, $query);

            if ($select) {
                $senhaAtual = mysqli_fetch_array($select)['senha'];
                $senha = $senhaAtual;
            }
        }

        // se nome não estiver setado, pega o nome atual no bd para mante-lo
        if (!$nome) {
            $query = "SELECT nome FROM users WHERE email = '$email'";
            $select = mysqli_query($conexao, $query);

            if ($select) {
                $nomeAtual = mysqli_fetch_array($select)['nome'];
                $nome = $nomeAtual;
            }
        }

        // se nome não estiver setado, pega o telefone atual no bd para mante-lo
        if (!$telefone_users) {
            $query = "SELECT telefone FROM users WHERE email = '$email'";
            $select = mysqli_query($conexao, $query);

            if ($select) {
                $telAtual = mysqli_fetch_array($select)['telefone'];
                $telefone_users = $telAtual;
            }
        }

        $query = "UPDATE users SET email = '$email_novo',
            senha = '$senha', nome = '$nome', telefone = '$telefone_users' WHERE email = '$email'";
        $insert = mysqli_query($conexao, $query);
        if (!$insert) {
            echo "<script>alert('Erro ao atualizar os dados do cadastro')</script>";
        } else {
            // se deu certo, atualizar o email em requisicoes_de_doacoes
            $query = "UPDATE requisicoes_de_doacoes SET email = '$email_novo' WHERE email = '$email'";
            $insert = mysqli_query($conexao, $query);
            if (!$insert) {
                echo "<script>alert('Erro ao atualizar os dados na tabela de requisições de doação.')</script>";
            }

            // se deu certo, atualizar o email em endereco_user
            $query = "UPDATE endereco_user SET email = '$email_novo' WHERE email = '$email'";
            $insert = mysqli_query($conexao, $query);
            if (!$insert) {
                echo "<script>alert('Erro ao atualizar os dados na tabela de endereço de recebimento de doação do usuário.')</script>";
            }
            echo "<script>alert('Dados atualizados com sucesso!')</script>";
            $_SESSION['email'] = $email_novo;
            $_SESSION['senha'] = $senha;
        }

        header("refresh: 0.5; url = settings.php");
        mysqli_close($conexao);
    }
}

?>
