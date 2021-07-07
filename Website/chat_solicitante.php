<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("location: login.php");
}

$email = $_SESSION['email'];

// verificar todas as datas limites de entrega das requisicoes de doacao, se houver algum em que a data
// limite é antes do hoje, setar status para inspirado.
$conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao com o servidor" . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

if (empty($bd)) {
    die("Banco de dados não encontrado");
}

$query = "SELECT * FROM requisicoes_de_doacoes";
$select = mysqli_query($conexao, $query);

$i = 1;
$datetoday = date('Y-m-d');
if (mysqli_num_rows($select) != 0) {
    while ($info = mysqli_fetch_array($select)) {
        $dataLimiteEntrega = $info['dataLimiteEntrega'];
        $id = $info['id'];
        $status = $info['status'];

        // transformar para um formato de data comparavel
        // y-m-d
        $i++;
        $auxDate = "";
        for ($i = 6; $i < 10; $i++)
            $auxDate .= $dataLimiteEntrega[$i];
        $auxDate .= '-';
        for ($i = 3; $i < 5; $i++)
            $auxDate .= $dataLimiteEntrega[$i];
        $auxDate .= '-';
        for ($i = 0; $i < 2; $i++)
            $auxDate .= $dataLimiteEntrega[$i];
        $dataLimiteEntrega = $auxDate;
        if ($dataLimiteEntrega < $datetoday && $status == 'AGUARDANDO') {
            $query = "UPDATE requisicoes_de_doacoes SET status = 'EXPIRADO' WHERE id = '$id'";
            $update = mysqli_query($conexao, $query);

            // deletar todas as mensagens do item requisitado se houver, ja qe o status mudou para inspirado
            $query = "DELETE FROM chat WHERE id_requisicao = '$id'";
            $delete = mysqli_query($conexao, $query);
        }
    }
}

// criar bd de chat so para evitar erros caso nao exista nenhuma mensagem
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

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate Food System</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href="assets/css/chat.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <!-- para atualizar a pagina a cada atualizacao-->
    <script type="text/javascript">
        function ajax() {
            var req = new XMLHttpRequest();
            req.onreadystatechange = function() {
                if (req.readyState == 4 && req.status == 200) {
                    document.getElementById('chat-area').innerHTML = req.responseText;
                }
            }
            req.open('GET', "update_chat.php?id_requisicao_doacao=<?php echo $_GET['id']; ?>&email_doador=<?php echo $_GET['email_doador']?>", true);
            req.send();

            
        }

        setInterval(function() {
            ajax();
            var elem = document.getElementById('scroller');
            elem.scrollTop = elem.scrollHeight;
        }, 1000);

        
    </script>
</head>

<body onload="ajax();">
    <div id="wrapper">
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img src="assets/img/food-donation.png" id="iconHopNav" /> &nbsp;DONATE FOOD SYSTEM</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li id="nav_top_style"><a href="settings.php"><img src="assets/img/setting_icon.png"/ id="setting_icon"></a></li>
                        <li id="nav_top_style"><a href="info.php"><img src="assets/img/info.png"/ id="info_icon"></a></li>
                        <li id="nav_top_style"><a id="Logout" href="#"><img src="assets/img/logout_icon.png"/ id="logout_icon"></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <?php
                        $address = "";

                        $conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao com o servidor" . mysqli_connect_error());

                        $bd = mysqli_select_db($conexao, "donate_system");

                        if (empty($bd)) {
                            die("Banco de dados não encontrado");
                        }

                        $query = "SELECT genero FROM users WHERE email = '$email'";
                        $select = mysqli_query($conexao, $query);

                        if ($select) {
                            $genero = mysqli_fetch_array($select)['genero'];
                            if ($genero == "feminino") {
                                echo "<img src='assets/img/feminino.png' class='userImg img-responsive'/>";
                            } else {
                                echo "<img src='assets/img/masculino.png' class='userImg img-responsive'/>";
                            }
                        }

                        $query = "SELECT nome FROM users WHERE email = '$email'";
                        $select = mysqli_query($conexao, $query);
                        if ($select) {
                            $nome = mysqli_fetch_array($select)['nome'];
                        }

                        $id_requisicao_doacao = $_GET['id'];
                        $email_doador = $_GET['email_doador'];
                       

                        $getTodasConsultas = "SELECT * FROM users WHERE email = '$email_doador'";

                        $nome_doador = "";
                        $genero_doador = "";

                        $select = mysqli_query($conexao, $getTodasConsultas);
                        if (mysqli_num_rows($select) != 0) {
                            while ($info = mysqli_fetch_array($select)) {
                                $nome_doador = $info['nome'];
                                $genero_doador = $info['genero'];
                            }
                        }

                        ?>


                        <p id="userNome"><?php echo $nome; ?></p>
                    </li>
                    <li>
                        <a href="index.php"><img src="assets/img/home_menu.png" class="iconMenu" /> Inicio</a>
                    </li>
                    <li>
                        <a href="desejo_doar.php"><img src="assets/img/icon_food_menu.png" class="iconMenu" /> Desejo doar </a>
                    </li>
                    <li>
                        <a href="desejo_receber.php"><img src="assets/img/food_receive_menu.png" class="iconMenu" /> Desejo receber </a>
                    </li>
                    <li>
                        <a href="endereco_usuario.php"><img src="assets/img/location_menu.png" class="iconMenu" /> Endereço de recebimento </a>
                    </li>
                    <li>
                        <a href="minhas_requisicoes.php"><img src="assets/img/myrequisitions_menu.png" class="iconMenu" /> Minhas requisições </a>
                    </li>
                    <li>
                        <a href="minhas_doacoes.php"><img src="assets/img/my_donations_menu.png" class="iconMenu" /> Minhas doações </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <img class="img-design-ativos center-block" src="assets/img/chat.png" />
                        <h3 class="title_ativos">Converse com <?php if ($genero_doador == 'masculino') {
                                                                    echo 'o';
                                                                } else {
                                                                    echo 'a';
                                                                } ?> solicitante da doação </h3>
                    </div>
                </div>
                <hr /><br /><br />
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <!--Widget body-->
                            <div id="demo-chat-body" class="collapse in">
                                <div class="nano has-scrollbar" style="height:380px">
                                    <div class="nano-content pad-all"  id='scroller' tabindex="0" style="right: -17px;">
                                        <ul class="list-unstyled media-block" id="chat-area">
                                            <!-- Os dados da mensagem irão vir aqui aqui -->
                                        </ul>
                                    </div>
                                    <div class="nano-pane">
                                        <div class="nano-slider" style="height: 141px; transform: translate(0px, 0px);"></div>
                                    </div>
                                </div>

                                <!--Widget footer-->
                                <div class="row final-chat">
                                    <form method="POST" action="create_bdChat.php" id="form-style-chat-3" class="">
                                        <div class="form-row">
                                            <div class="hide">
                                                <input class="form-control" name="email_user_doador" autocomplete="off" value=<?php echo $email_doador; ?> />
                                            </div>
                                            <div class="hide">
                                                <input class="form-control" name="email_user_solicitante" autocomplete="off" value=<?php echo $email; ?> />
                                            </div>
                                            <div class="hide">
                                                <input class="form-control" name="hora_mensagem" autocomplete="off" value=<?php date_default_timezone_set('America/Sao_Paulo');
                                                                                                                            echo Date('H:i:s'); ?> />
                                            </div>
                                            <div class="hide">
                                                <input class="form-control" name="data_mensagem" autocomplete="off" value=<?php echo Date('Y-m-d'); ?> />
                                            </div>
                                            <div class="hide">
                                                <input class="form-control" name="id_requisicao" autocomplete="off" value=<?php echo $id_requisicao_doacao; ?> />
                                            </div>
                                            <div class="col-10">
                                                <input class="form-control" name="chat_input" id="chat-input" autocomplete="off" maxlength="500" placeholder="Digite aqui" />
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-style-chat btn-primary btn-block rounded1" type="submit">Enviar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <p class="text-center footer">
                    Developed by <a href="https://github.com/AnneLivia" target="u_black">Anne Livia</a><br />
                    © All Rights Reserved.
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                </p>
            </footer>
        </div>
    </div>
    <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>

</html>