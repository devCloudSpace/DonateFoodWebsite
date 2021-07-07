<?php
session_start();

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}


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

$id_requisicao_doacao = $_GET['id_requisicao_doacao'];
$email_doador = $_GET['email_doador'];

$getTodasConsultas = "SELECT email FROM requisicoes_de_doacoes WHERE id = '$id_requisicao_doacao'";

$email_solicitante = "";

$select = mysqli_query($conexao, $getTodasConsultas);
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $email_solicitante = $info['email'];
    }
}

$getTodasConsultas = "SELECT * FROM users WHERE email = '$email_solicitante'";

$nome_solicitante = "";
$genero_solicitante = "";

$select = mysqli_query($conexao, $getTodasConsultas);
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $nome_solicitante = $info['nome'];
        $genero_solicitante = $info['genero'];
    }
}

// checar se já não existe chat com o solicitante indicado e a requisicao selecionada.
$getTodasConsultas = "SELECT chat.chat_input, chat.hora_mensagem, chat.doador_enviou,
chat.email_doador, users.nome, users.genero
FROM chat
INNER JOIN users ON chat.email_doador = users.email 
WHERE id_requisicao = '$id_requisicao_doacao' AND email_solicitante = '$email_solicitante' AND email_doador = '$email_doador'";

$select = mysqli_query($conexao, $getTodasConsultas);
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $chat_input = $info['chat_input'];
        $hora_mensagem = $info['hora_mensagem'];
        $doador_enviou = $info['doador_enviou'];
        $email_doador = $info['email_doador'];
        $nome_doador = $info['nome'];
        $genero_doador = $info['genero'];
        // checar o email logado atualmente, ele deve vir do lado esquerdo
        /*
        Se quem enviou a mensagem atual for o doador e é ele quem está conectado, mensagem vai na direita,
        caso contrario mensagem vai na esquerda. Para verificar se ele é quem está conectado agora ou nao
        basta verificar se o email atual é igual ao email do solicitante por exemplo, se for, é porque o solicitante é
        quem está online, caso contrario, quem está online é o doador. Com base nisso, será decidido qual mensagem vai na direita
        e qual nao. sempre o que estver conectado atualmente é quem recebe a mensagem na direita, a  mensagem do outro vem na esquerda.

        agora se a mensagem nao for enviada pelo doador, so fazer as mesmas configurações, considerando o nome do solicitante.
        */
        if ($doador_enviou) {
            if ($email == $email_solicitante) {
                echo "
                    <li class='mar-btm'>
                        <div class='media-left'>
                            <img src='assets/img/$genero_doador.png' class='img-circle img-sm' alt='Profile Picture'>
                        </div>
                        <div class='media-body pad-hor speech-left'>
                            <div class='speech'>
                                <p href='#' class='media-heading'>$nome_doador</p>
                                <p>$chat_input</p>
                                <p class='speech-time'>
                                    <i class='fa fa-clock-o fa-fw'></i> $hora_mensagem
                                </p>
                            </div>
                        </div>
                    </li>";
            } else {
                echo "
                    <li class='mar-btm'>
                        <div class='media-right'>
                            <img src='assets/img/$genero_doador.png' class='img-circle img-sm' alt='Profile Picture'>
                        </div>
                        <div class='media-body pad-hor speech-right'>
                            <div class='speech'>
                                <p href='#' class='media-heading'>$nome_doador</p>
                                <p>$chat_input</p>
                                <p class='speech-time'>
                                    <i class='fa fa-clock-o fa-fw'></i> $hora_mensagem
                                </p>
                            </div>
                        </div>
                    </li>";
            }
        } else {
            if ($email == $email_solicitante) {
                echo "
                    <li class='mar-btm'>
                        <div class='media-right'>
                            <img src='assets/img/$genero_solicitante.png' class='img-circle img-sm' alt='Profile Picture'>
                        </div>
                        <div class='media-body pad-hor speech-right'>
                            <div class='speech'>
                                <p href='#' class='media-heading'>$nome_solicitante</p>
                                <p>$chat_input</p>
                                <p class='speech-time'>
                                    <i class='fa fa-clock-o fa-fw'></i> $hora_mensagem
                                </p>
                            </div>
                        </div>
                    </li>";
            } else {
                echo "
                <li class='mar-btm'>
                    <div class='media-left'>
                        <img src='assets/img/$genero_solicitante.png' class='img-circle img-sm' alt='Profile Picture'>
                    </div>
                    <div class='media-body pad-hor speech-left'>
                        <div class='speech'>
                            <p href='#' class='media-heading'>$nome_solicitante</p>
                            <p>$chat_input</p>
                            <p class='speech-time'>
                                <i class='fa fa-clock-o fa-fw'></i> $hora_mensagem
                            </p>
                        </div>
                    </div>
                </li>";
            }
        }
    }
}
