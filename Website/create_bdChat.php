<?php
session_start();

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}


$email_user_doador = $_POST['email_user_doador'];
$email_user_solicitante = $_POST['email_user_solicitante'];
$hora_mensagem = $_POST['hora_mensagem'];
$id_requisicao = $_POST['id_requisicao'];
$chat_input = $_POST['chat_input'];
$data_mensagem = date("d/m/Y", strtotime($_POST['data_mensagem']));


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

// VERIFICAR SE o item nao está como aguardando, se nao estiver, sair da pagina, nao permitir conversa
$getTodasConsultas = "SELECT status FROM requisicoes_de_doacoes WHERE id = '$id_requisicao'";

$select = mysqli_query($conexao, $getTodasConsultas);
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $statuss = $info['status'];
    }

    if ($statuss != 'AGUARDANDO') {
        // retorna para página de desejo doar
        echo "<script>alert('Conversa finalizada. Item indisponível no momento.')</script>";
        header("refresh: 0.2; url = desejo_doar.php");
    } else {

        // verificar se os usuarios ainda existem.
        $getTodasConsultas = "SELECT email FROM users WHERE email = '$email_user_doador'";

        $select = mysqli_query($conexao, $getTodasConsultas);
        if (mysqli_num_rows($select) == 0) {
            echo "<script>alert('Usuário que pretendia realizar a doação removeu sua conta do sistema.')</script>";
            mysqli_close($conexao);
            header("refresh: 0.2; url = index.php");
        } else {
            // verificar se os usuarios ainda existem.
            $getTodasConsultas = "SELECT email FROM users WHERE email = '$email_user_solicitante'";

            $select = mysqli_query($conexao, $getTodasConsultas);
            if (mysqli_num_rows($select) == 0) {
                echo "<script>alert('Usuário que solicitou a doação do(s) alimento(s) removeu sua conta do sistema')</script>";
                mysqli_close($conexao);
                header("refresh: 0.2; url = index.php");
            } else {
                // se chegou aqui, BD existe ou foi criado
                // agora verificar se tabela users existe, se não existir, criar
                $query = "SELECT * FROM chat";
                $select = mysqli_query($conexao, $query);
                // se não existir, criar a tabela
                if (!$select) {
                    $query = "CREATE TABLE chat (
                id int(100) NOT NULL AUTO_INCREMENT,
                email_doador varchar(200) NOT NULL,
                email_solicitante varchar(200) NOT NULL,
                id_requisicao int(100) NOT NULL,
                hora_mensagem varchar(20) NOT NULL,
                data_mensagem varchar(20) NOT NULL,
                chat_input varchar(500) NOT NULL,
                doador_enviou BOOLEAN NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARSET=utf8";

                    $createTable = mysqli_query($conexao, $query);

                    if (!$createTable) {
                        die("Tabela não foi criada com sucesso!");
                    }
                }

                // por padrão o doador é o primeiro a enviar mensagem já que ele é quem envia a primeira mensagem
                $doador_enviou = true;

                // se o email do solicitante for igual ao email conectado agora é porque quem enviou foi o solicitante e nao o doador
                if ($email_user_solicitante == $email) {
                    $doador_enviou = false;
                }

                $query = "INSERT INTO chat (email_doador, email_solicitante, id_requisicao, hora_mensagem, data_mensagem, chat_input, doador_enviou) 
VALUES ('$email_user_doador', '$email_user_solicitante', '$id_requisicao', '$hora_mensagem', '$data_mensagem', '$chat_input', '$doador_enviou')";

                $insert = mysqli_query($conexao, $query);
                if (!$insert) {
                    echo "<script>alert('Erro ao enviar mensagem.')</script>";
                }
                // se email atual for igual ao do solicitante voltar para chat_solicitante, caso contrario para o chat mesmo
                if ($email != $email_user_solicitante) {
                    header("refresh: 0.5; url = chat.php?id=$id_requisicao");
                    mysqli_close($conexao);
                } else {
                    header("refresh: 0.5; url = chat_solicitante.php?id=$id_requisicao&email_doador=$email_user_doador");
                    mysqli_close($conexao);
                }
            }
        }
    }
} else {
    echo "<script>alert('Requisição removida do sistema')</script>";
    header("refresh: 0.2; url = desejo_doar.php");
    mysqli_close($conexao);
}
