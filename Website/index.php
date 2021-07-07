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
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<body>
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

                        $getTodasConsultas = "SELECT * FROM requisicoes_de_doacoes WHERE email = '$email'";

                        $status = array();

                        $select = mysqli_query($conexao, $getTodasConsultas);
                        if (mysqli_num_rows($select) != 0) {
                            while ($info = mysqli_fetch_array($select)) {
                                array_push($status, $info['status']);
                            }
                        }


                        // pegar status das minhas doacoes, se alguem colocou para REJEITADO.

                        $getTodasConsultas = "SELECT * FROM desejo_doar WHERE email_doador = '$email'";

                        $status_desejodoar = array();

                        $select = mysqli_query($conexao, $getTodasConsultas);
                        if (mysqli_num_rows($select) != 0) {
                            while ($info = mysqli_fetch_array($select)) {
                                array_push($status_desejodoar, $info['status']);
                            }
                        }

                         // verificar se existe alguma mensagem para algumas das minhas requisicoes

                         $getTodasConsultas = "SELECT * FROM chat WHERE email_solicitante = '$email'";

                         $chat_requisicao = false;
 
                         $select = mysqli_query($conexao, $getTodasConsultas);
                         if (mysqli_num_rows($select) != 0) {
                             while ($info = mysqli_fetch_array($select)) {
                                $chat_requisicao = true;
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
                    <div class="col-md-12 notification-tab <?php if (!in_array('ACIONADO', $status)) {
                                                                echo 'hide';
                                                            } ?> ">
                        <!-- Warning -->
                        <div class="g-brd-around g-brd-gray-light-v7 g-rounded-4 g-pa-15 g-pa-20--md g-mb-30">
                            <div class="noty_bar noty_type__warning noty_theme__unify--v1 noty_close_with_click noty_close_with_button g-mb-25">
                                <div class="noty_body">
                                    <div class="g-mr-20">
                                        <div class="noty_body__icon">
                                            <i class="hs-admin-bolt"></i>
                                        </div>
                                    </div>
                                    <div>
                                        Olá, temos uma boa notícia para você. Uma pessoa demonstrou interesse em doar o(s) alimento(s) que
                                        você solicitou. Acesse o item na página "Minhas Requisições" para aprovar ou rejeitar a proposta de doação.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Warning -->
                    </div>
                    <div class="col-md-12 notification-tab <?php if (!in_array('REJEITADO', $status_desejodoar)) {
                                                                echo 'hide';
                                                            } ?> ">
                        <!-- error -->
                        <div class="g-brd-around g-brd-gray-light-v7 g-rounded-4 g-pa-15 g-pa-20--md g-mb-30">
                            <div class="noty_bar noty_type__error noty_theme__unify--v1 g-mb-25">
                                <div class="noty_body">
                                    <div class="g-mr-20">
                                        <div class="noty_body__icon">
                                            <i class="hs-admin-alert"></i>
                                        </div>
                                    </div>
                                    <div class="text-notification-error">
                                        Olá, infelizmente uma das pessoas para a qual você desejaria doar o(s) alimento(s) rejeitou a sua solicitação.
                                        Acesse o item que foi rejeitado e confirme para retirar a notificação do painel inicial.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 notification-tab <?php if (!in_array('APROVADO', $status_desejodoar)) {
                                                                echo 'hide';
                                                            } ?> ">
                        <!-- sucess -->
                        <div class="g-brd-around g-brd-gray-light-v7 g-rounded-4 g-pa-15 g-pa-20--md g-mb-30">
                            <div class="noty_bar noty_type__success noty_theme__unify--v1 g-mb-25">
                                <div class="noty_body">
                                    <div class="g-mr-20">
                                        <div class="noty_body__icon">
                                            <i class="hs-admin-alert"></i>
                                        </div>
                                    </div>
                                    <div class="text-notification-error">
                                        Olá, temos uma excelente notícia. Uma das pessoas para a qual você demonstrou interesse em doar
                                        alimento(s) aprovou a sua solicitação. Acesse o respectivo item na página "Minhas doações" e indique quando
                                        você tiver realizado a entrega, para que esta notificação possa ser retirada do painel inicial.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 notification-tab <?php if (!$chat_requisicao) {
                                                                echo 'hide';
                                                            } ?> ">
                        <!-- sucess -->
                        <div class="g-brd-around g-brd-gray-light-v7 g-rounded-4 g-pa-15 g-pa-20--md g-mb-30">
                            <div class="noty_bar noty_type__info noty_theme__unify--v1 g-mb-25">
                                <div class="noty_body">
                                    <div class="g-mr-20">
                                        <div class="noty_body__icon">
                                            <i class="hs-admin-alert"></i>
                                        </div>
                                    </div>
                                    <div class="text-notification-error">
                                        Olá, você possui mensagens de alguém que pretende doar para você. Acesse a página "Minhas Requisões" e verifique quais mensagens foram enviadas
                                        para o item que está com o status AGUARDANDO.
                                        Atenção, esse alerta sumirá assim que alguma pessoa proceder com a doação ou quando a requisição estiver indisponível para doação.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 main-index-presentation">
                        <h2 class="text-center"> SISTEMA PARA DOAÇÃO DE ALIMENTOS </h2>
                        <img class="center-block" src="assets/img/index_logo.jpg" />
                        <p class="index-p text-center">
                            Este sistema permite a conexão entre o doador de alimentos e a pessoa que necessita dessas doações.
                            O ato de doar alimentos permite contribuir positiva e efetivamente para a transformação da sociedade.
                            Ao considerar as necessidades e limitações do próximo e ajudá-lo, podemos nos tornar mais igualitários e justos.
                            <br />
                            Neste sistema você terá a liberdade de realizar doações e também de requisitar doações, desse modo será possível praticar a
                            solidariedade mútua.
                        </p>
                        <br />
                        <p class="index-p text-center">
                            Sistema desenvolvido pela <a href="https://www.github.com/annelivia" target="u_black">Anne Livia. </a>
                            <br />
                            © Todos os direitos reservados.
                        </p>
                        <br />
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