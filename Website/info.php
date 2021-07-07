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
                    <div class="col-md-12 main-index-presentation">
                        <h3 class="text-center"> INFORMAÇÕES GERAIS PARA DOAÇÃO ADEQUADA DOS ALIMENTOS </h3>
                        <img class="center-block" id="info_donation_img" src="assets/img/info_donation.jpg" />
                        <br />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="title-index center"><img src="assets/img/food-donation_general.png" class="title_session" /> &nbsp; BENEFÍCIOS DE DOAR ALIMENTOS </h4>
                    </div>
                    <div class="col-md-12">
                        <br />
                        <p class="info-p text-justify">
                            <strong>Doar para os necessitados não beneficia apenas o receptor, mas também o doador, melhorando o bem-estar físico, psicológico e emocional.</strong>
                            <br /><br />
                            <strong>Perspectivas em que a doação é benéfica para os doadores</strong>
                            <br /><br />
                            <strong>Educação:</strong>
                            <br /><br />
                            Para muitos doadores, doar é uma oportunidade para conhecer as questões que envolvem essa necessidade específica. A maioria das pessoas escolhe aprender sobre os problemas, antes de doar, para obter uma perspectiva mais ampla.
                            Doar para os necessitados fornece novas informações e revela um ponto de vista diferente sobre diferentes questões sociais, como a falta de moradia, a fome ou a pobreza.
                            <br /><br />
                            <strong>Comunidade:</strong>
                            <br /><br />
                            Doar para os necessitados é uma ótima maneira de melhorar as condições em seu bairro ou comunidade. Doar alimentos para pessoas ou organizações dignas ajuda a combater a pobreza, a fome e, ao mesmo tempo, pode melhorar a harmonia, a amizade e a confiança entre os residentes.
                            Verificou-se que as doações de caridade promovem maiores níveis de prosperidade e aumentam a felicidade, a cooperação, a boa vontade e comunidades fortes.
                            <br /><br />
                            <strong>Saúde:</strong>
                            <br /><br />
                            Foi observado que a generosidade libera endorfinas que geram sentimentos de calma, paz, gratidão e satisfação que ajudam a aliviar a tensão e o estresse.
                            Assim, as pessoas que ajudam os necessitados obtêm os benefícios em termos de um sistema imunológico mais forte, menor freqüência cardíaca, maior energia, menor dor e menor pressão arterial.
                            <br /><br />
                            Você está interessado em doar alimentos? Você gostaria de obter os benefícios da doação?
                            Se você é uma pessoa física, pessoa Jurídica ou uma organização, faça parte do nosso sistema e contribuia com o fornecimento de alimentos de qualidade aos necessitados.
                            Junte-se a nós e venha contribuir para acabar com a fome da sociedade.
                            <br /><br />
                        </p>
                    </div>
                    <div class="col-md-12">
                        <h4 class="title-index center"><img src="assets/img/food-donation_general.png" class="title_session" /> &nbsp; O QUE DOAR ? </h4>
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-info">Alimentos não-perecíveis</h4>
                        <p class="text-justify info-p">
                            São todos aqueles que vêm embalados de fábrica e têm longa duração. São alimentos ideais para arrecadação, pois podem ficar à temperatura ambiente sem estragar e exigem menos cuidados para armazenamento (diferente de frutas e congelados, por exemplo).
                            <br /><br />
                            Além de arroz, feijão, açúcar e farinha de trigo, outras opções de não perecíveis que você pode doar são:
                            leite em pó;
                            legumes enlatados como milho e ervilha;
                            gelatina em pó;
                            mistura para bolo;
                            biscoitos;
                            milho de pipoca;
                            fubá;
                            achocolatado em pó;
                            óleo de cozinha;
                            leite em caixa longa vida; e etc.
                            <br /><br />
                            Antes de entregar qualquer um dos alimentos acima para doação, verifique a data de validade do produto e, também, se as embalagens estão íntegras, sem furos ou amassados, para que eles não deteriorem até chegar a seu destino final.
                            <br /><br />
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-info">Alimentos perecíveis</h4>
                        <p class="text-justify info-p">
                            Há instituições de arrecadação de alimentos que aceitam alimentos perecíveis como frutas, legumes e congelados. Nesses casos, quem vai doar precisa tomar os seguintes cuidados:
                            <br /><br />
                            Frutas e legumes: podem ser doados verdes e maduros, mas nunca estragados. Precisam ser doados refrigerados (converse com a ONG que deseja ajudar, para ter orientações sobre como transportar os alimentos ou se eles retiram em sua casa).
                            <br /><br />
                            Congelados: precisam estar na validade, com a embalagem íntegra e, claro... congelados! Neste caso, também é preciso combinar com a ONG que vai receber, para que o alimento não estrague antes de chegar ao seu destinatário.
                            <br /><br />
                        </p>
                    </div>
                    <div class="col-md-12">
                        <h4 class="title-index center"><img src="assets/img/food-donation_general.png" class="title_session" /> &nbsp; O QUE NÃO PODE DOAR ? </h4>
                    </div>
                    <div class="col-md-12">
                        <h4 class="title-info"></h4>
                        <p class="info-p text-center">
                            Por maior que seja a boa vontade, há certos alimentos que podem representar um risco para quem vai consumir. Sobras de refeições prontas, por exemplo, podem estragar rapidamente e não são aconselháveis para doação de alimentos. O mesmo pode acontecer com alimentos fora do prazo de validade, ou que deveriam estar no freezer e já foram descongelados.
                        </p>
                    </div>
                    <div class="col-md-12">
                        <br /><br />
                        <p class="info-p text-center">
                            Dados extraídos <a href="https://www.sodexobeneficios.com.br/qualidade-de-vida/noticias/como-doar-alimentos.htm#ixzz6vWBhqWT6" target="_blank">
                            desta fonte</a> e <a href="https://www.misrii.com/blog/benefits-of-food-donation/" target="u_blank">desta fonte</a>. 
                            Para mais informações a respeito de doação de alimentos, leia a
                            "<a href="https://stas.rs.gov.br/upload/arquivos/202010/16180603-cartilha-de-orientacoes-para-doacao-de-alimentos.pdf" target="_blank">CARTILHA DE ORIENTAÇÕES PARA DOAÇÃO DE ALIMENTOS</a>"
                            que está de acordo com a LEI FEDERAL 14.016/2020.
                        </p>
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