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

            // deletar todas as mensagens do item requisitado se houver, ja qe o status mudou para expirado
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
    <link href="assets/css/all.min.css" rel="stylesheet" />
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
                    <a class="navbar-brand" href="index.php"><img src="assets/img/food-donation.png" id="iconHopNav" /> &nbsp;Donate Food System</a>
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

                        ?>

                        <p id="userNome"><?php echo $nome; ?></p>
                    </li>
                    <li>
                        <a href="index.php"><img src="assets/img/home_menu.png" class="iconMenu" /> Inicio</a>
                    </li>
                    <li>
                        <a href="desejo_doar.php"><img src="assets/img/icon_food_menu.png" class="iconMenu" /> Desejo doar</a>
                    </li>
                    <li>
                        <a href="desejo_receber.php"><img src="assets/img/food_receive_menu.png" class="iconMenu" /> Desejo receber</a>
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
                        <?php
                        if ($genero == 'feminino') {
                            echo '<img class="img-design-ativos center-block" src="assets/img/food_request_info_girl.png" />';
                        } else {
                            echo '<img class="img-design-ativos center-block" src="assets/img/food_request_info_boy.png" />';
                        }
                        ?>
                        <h3 class="title_ativos">Todas as minhas solicitações de alimentos no sistema </h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table_ativos table-ativos-requisicoes-doacao table table-striped table-borderless table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>O que receber </th>
                                        <th>Motivação</th>
                                        <th>Data limite</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $conexao = mysqli_connect('localhost', 'root', '') or die("Erro de conexao " . mysqli_connect_error());

                                    $bd = mysqli_select_db($conexao, "donate_system");
                                    if (empty($bd)) {
                                        $criaBD = mysqli_query($conexao, "CREATE DATABASE donate_system DEFAULT CHARSET=utf8");
                                        if (!$criaBD) {
                                            die("Erro ao criar banco de dados");
                                        }
                                    }


                                    if (mysqli_query($conexao, "SELECT * FROM requisicoes_de_doacoes")) {

                                        $consulta = "SELECT * FROM requisicoes_de_doacoes WHERE email = '$email'";

                                        $select = mysqli_query($conexao, $consulta);

                                        if (mysqli_num_rows($select) != 0) {
                                            $i = 0;
                                            $ii = 0;
                                            while ($info = mysqli_fetch_array($select)) {
                                                $id = $info['id'];
                                                $oqueReceber = $info['oqueReceber'];
                                                $motivacao = $info['porqueReceber'];
                                                $dataLimite = $info['dataLimiteEntrega'];
                                                $status = $info['status'];
                                                $ii++;
                                                if ($status == 'ACIONADO') {
                                                    echo "<tr class='$status' id='$id'>
                                                        <td class='tabelaAcionada'> $ii </td>
                                                        <td class='tabelaAcionada'>$oqueReceber</td>
                                                        <td class='tabelaAcionada'>$motivacao</td>
                                                        <td class='tabelaAcionada'>$dataLimite</td>
                                                        <td class='tabelaAcionada'>$status</td>
                                                        <td class='tabelaAcionada'>
                                                            <button class='button-style-onTable atualizarRequestDoacao btn btn-success rounded1'><i class='fas fa-pencil-alt'></i></button>
                                                            <button class='button-style-onTable deleteRequestDoacao btn btn-danger rounded1'><i class='far fa-trash-alt'></i></button>
                                                        </td>
                                                    </tr>";
                                                } else if ($status == 'ENTREGUE') {
                                                    echo "<tr class='$status' id='$id'>
                                                        <td> $ii </td>
                                                        <td>$oqueReceber</td>
                                                        <td>$motivacao</td>
                                                        <td>$dataLimite</td>
                                                        <td>$status</td>
                                                        <td>
                                                            <button class='button-style-onTable atualizarRequestDoacao btn btn-success rounded1'><i class='fas fa-pencil-alt'></i></button>
                                                            <button class='button-style-onTable deleteDoacaoEntregue btn btn-danger rounded1'><i class='far fa-trash-alt'></i></button>
                                                        </td>
                                                    </tr>";
                                                }  else {
                                                    echo "<tr class='$status' id='$id'>
                                                        <td> $ii </td>
                                                        <td>$oqueReceber</td>
                                                        <td>$motivacao</td>
                                                        <td>$dataLimite</td>
                                                        <td>$status</td>
                                                        <td>
                                                            <button class='button-style-onTable atualizarRequestDoacao btn btn-success rounded1'><i class='fas fa-pencil-alt'></i></button>
                                                            <button class='button-style-onTable deleteRequestDoacao btn btn-danger rounded1'><i class='far fa-trash-alt'></i></button>
                                                        </td>
                                                    </tr>";
                                                }
                                            }
                                            $i++;
                                        }
                                    }

                                    mysqli_close($conexao);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <p class="text-center">
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