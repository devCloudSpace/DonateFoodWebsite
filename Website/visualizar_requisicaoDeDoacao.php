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
<html lang="pt-br">

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

                        // pegar o id 
                        $id = $_GET['id'];

                        // obter os dados do usuario atual
                        $query = "SELECT nome FROM users WHERE email = '$email'";
                        $select = mysqli_query($conexao, $query);
                        if ($select) {
                            $nome = mysqli_fetch_array($select)['nome'];
                        }

                        // obter os dados do usuario atual
                        $query = "SELECT telefone FROM users WHERE email = '$email'";
                        $select = mysqli_query($conexao, $query);
                        if ($select) {
                            $telefone = mysqli_fetch_array($select)['telefone'];
                        }

                        $getTodasConsultas = "SELECT * FROM endereco_user WHERE email = '$email'";

                        $endereco = "";
                        $complemento = "";
                        $cep = "";
                        $bairro = "";
                        $cidade = "";

                        $select = mysqli_query($conexao, $getTodasConsultas);
                        if (mysqli_num_rows($select) != 0) {
                            while ($info = mysqli_fetch_array($select)) {
                                $id_endereco = $info['id'];
                                $endereco = $info['endereco'];
                                $complemento = $info['complemento'];
                                $cep = $info['cep'];
                                $bairro = $info['bairro'];
                                $cidade = $info['cidade'];
                            }
                        }

                        $query = "SELECT * FROM requisicoes_de_doacoes WHERE id = $id";
                        $select = mysqli_query($conexao, $query);

                        $dataLimiteEntrega = "";
                        $tipoAlimento = "";
                        $oque = "";
                        $porque = "";

                        $i = 1;
                        $datetoday = date('Y-m-d');
                        if (mysqli_num_rows($select) != 0) {
                            while ($info = mysqli_fetch_array($select)) {
                                $dataLimiteEntrega = $info['dataLimiteEntrega'];
                                $tipoAlimento = $info['tipoAlimento'];
                                $oque = $info['oqueReceber'];
                                $porque = $info['porqueReceber'];
                                $status = $info['status'];
                                $id = $info['id'];

                                $i++;
                            }
                        }
                        // transformar para um formato
                        // y-m-d
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


                        // pegar dado do doador do alimento se acionado.
                        // obter os dados do usuario atual
                        $doador = null;
                        $email_doador = null;
                        $id_doacao = null;
                        if ($status == 'ACIONADO') {
                            $query = "SELECT users.nome, users.email, desejo_doar.id FROM users 
                            INNER JOIN desejo_doar 
                            ON desejo_doar.email_doador = users.email AND desejo_doar.status = 'AGUARDANDO' 
                            AND desejo_doar.id_requisicao = '$id'
                            INNER JOIN requisicoes_de_doacoes
                            ON requisicoes_de_doacoes.id = '$id';
                            ";
                            $select = mysqli_query($conexao, $query);
                            if (mysqli_num_rows($select) != 0) {
                                while ($info = mysqli_fetch_array($select)) {
                                    $doador  = $info['nome'];
                                    $email_doador = $info['email'];
                                    $id_doacao = $info['id'];
                                }
                            }
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
                        <!-- Form Elements -->
                        <div class="panel panel-default painel-css-1">
                            <div class="panel-heading">
                                Dados da requisição de doação
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form method="POST" action="mudar_status_bdDoacao.php" class="visualizar-dados-requisicao-de-doacao" id="form-style2">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input class="form-control" value='<?php echo $nome; ?>' readonly />
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" value='<?php echo $email; ?>' readonly />
                                                </div>
                                                <div class="col">
                                                    <label>Telefone</label>
                                                    <input type="text" class="form-control" value='<?php echo $telefone; ?>' readonly />
                                                </div>
                                            </div>
                                            <div class="form-row upALittle">
                                                <div class="col">
                                                    <label>CEP </label>
                                                    <input type="text" class="form-control cep_user" value='<?php echo $cep ?>' readonly />
                                                </div>
                                                <div class="col">
                                                    <label>Endereço </label>
                                                    <input type="text" class="form-control complemento_user" value='<?php echo $endereco ?>' readonly />
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label>Bairro </label>
                                                    <input type="text" class="form-control " value='<?php echo $bairro ?>' readonly />
                                                </div>
                                                <div class="col">
                                                    <label>Cidade </label>
                                                    <input type="text" class="form-control" value='<?php echo $cidade ?>' readonly />
                                                </div>
                                                <div class="col">
                                                    <label>Complemento</label>
                                                    <input type="text" class="form-control complemento_user" value='<?php echo $complemento ?>' readonly />
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <label>Data limite para entrega</label>
                                                    <input type="date" class="form-control" id="user_dataLimite_atualizar" name="user_dataLimite_atualizar" readonly value='<?php echo $dataLimiteEntrega; ?>' />
                                                </div>
                                                <div class="col">
                                                    <label>Tipo de alimento</label>
                                                    <input type="text" class="form-control" id="tipo_alimento" name="tipo_alimento" readonly value='<?php echo $tipoAlimento; ?>' />
                                                </div>
                                            </div>
                                            <div class="form-group form-style-select2">
                                                <label>Quais alimentos eu preciso?</label>
                                                <textarea type="text" class="form-control" readonly required maxlength="1000" /><?php echo $oque; ?></textarea>
                                            </div>
                                            <div class="form-group form-style-select2">
                                                <label>Por que eu preciso desta doação ?</label>
                                                <textarea type="text" class="form-control" readonly required maxlength="1000" /><?php echo $porque; ?></textarea>
                                            </div>

                                            <br class="<?php if ($status != 'ACIONADO') {echo 'hide'; } ?>"/>
                                            <br class="<?php if ($status != 'ACIONADO') {echo 'hide'; } ?>"/>
                                            <hr class="<?php if ($status != 'ACIONADO') {echo 'hide'; } ?>"/>
                                            <br class="<?php if ($status != 'ACIONADO') {echo 'hide'; } ?>"/>

                                            <div class="col-md-12 <?php if ($status != 'ACIONADO') {echo 'hide'; } ?>">
                                                <img class="img-design-ativos center-block" src="assets/img/happy_donation.png" />
                                                <h3 class="title_ativos">Alguém deseja doar o que você requisitou. </h3>
                                            </div>
                    
                                            <div class="form-row <?php if ($status != 'ACIONADO') {echo 'hide'; } ?>">
                                                <div class="col-5 hide">
                                                    <label>ID doacao</label>
                                                    <input type="text" class="form-control" id="id_doacao" name="id_doacao" readonly value='<?php echo $id_doacao; ?>' />
                                                </div>
                                                <div class="col-5 hide">
                                                    <label>ID requisicao</label>
                                                    <input type="text" class="form-control" id="id_requisicao" name="id_requisicao" readonly value='<?php echo $id; ?>' />
                                                </div>
                                                <div class="col-5 hide">
                                                    <label>Eamil doador</label>
                                                    <input type="text" class="form-control" id="email_doador" name="email_doador" readonly value='<?php echo $email_doador; ?>' />
                                                </div>
                                                <div class="col-5">
                                                    <label>Doador do(s) alimento(s)</label>
                                                    <input type="text" class="form-control" id="nome_doador" name="nome_doador" readonly value='<?php echo $doador; ?>' />
                                                </div>
                                                <div class="col-5">
                                                    <label>Você aprova ou rejeita está doação ?</label><br />
                                                    <select class="form-control select-veridido-sobre-doacao select-style" name="veredito_doacao" required>
                                                        <option value='APROVADO' selected>Aprovado</option>
                                                        <option value='REJEITADO'>Rejeitado</option>
                                                    </select>
                                                </div>
                                                <div class="col-2 center-block">
                                                    <label class="hide">Você aprova ou rejeita está doação ?</label><br />
                                                    <button type="submit" class="btn btn-success btn-style btn-proceder-aprovar-rejeitar rounded1">Proceder</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-12">
                                    
                                        <?php
                                            // pegar o dado do chat
                                            $email_chat_doador = null;
                                            $nome_chat_doador = null;

                                            // primeiro verificar se tem mensagem, so aparece aqueles que estiverem com status aguardando
                                            $query = "SELECT * FROM chat 
                                            INNER JOIN requisicoes_de_doacoes ON 
                                            chat.id_requisicao = requisicoes_de_doacoes.id AND 
                                            requisicoes_de_doacoes.status = 'AGUARDANDO'
                                            WHERE chat.id_requisicao = $id";
                                            $select = mysqli_query($conexao, $query);
                                            if (mysqli_num_rows($select) != 0) {
                                                echo '<div class="col-md-12">
                                                    <img class="img-design-ativos center-block" src="assets/img/message.png" />
                                                    <h3 class="title_ativos title-chat-solicitante">&nbsp;&nbsp;Olá, você possui mensagens!</h3>
                                                </div>';
                                                $query = "SELECT DISTINCT users.email, users.nome FROM users 
                                                    INNER JOIN chat ON id_requisicao = $id AND chat.email_doador = users.email";
                                                $select = mysqli_query($conexao, $query);
                                                if (mysqli_num_rows($select) != 0) {
                                                    while ($info = mysqli_fetch_array($select)) {
                                                        $email_chat_doador  = $info['email'];
                                                        $nome_chat_doador = $info['nome'];

                                                        echo "
                                                        <div class='form-row div-chat-buttons'>
                                                            <div class='col-3'></div>
                                                            <div class='col-6'>
                                                                <button class='btn btn-warning btn-style btn-acessar-mensagem rounded1' value='$id $email_chat_doador'>$nome_chat_doador</button>
                                                            </div>
                                                            <div class='col-3'></div>
                                                        </div>";
                                                    }
                                                }
                                            }
                                            
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Form Elements -->
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