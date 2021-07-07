<?php
session_start();
// encerrar todas as sessões em login
if (isset($_SESSION['email'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
}

$conexao = mysqli_connect('localhost', 'root', '') or die('Erro de conexão' . mysqli_connect_error());

$bd = mysqli_select_db($conexao, "donate_system");

// se banco de dados não existe, criar outro
if (empty($bd)) {
    $createBD = mysqli_query($conexao, "CREATE DATABASE donate_system DEFAULT CHARSET=utf8");
    // se o banco de dados não tiver sido criado
    if (!$createBD) {
        die("Erro ao criar o banco de dados");
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate System</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/login.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<!-- BEGIN BODY -->

<body>

    <!-- PAGE CONTENT -->
    <div class="container">
        <div class="container containerX" id="containerX">
            <div class="form-container form-cadastro sign-up-container">
                <form action="create_bdUsers.php" class="form-cadastro-form" method="POST">
                    <h1>Registre-se</h1>
                    <input name="uNomeCompleto" autocomplete="off" type="text" placeholder="Nome completo" class="form-control uNomeCompleto" onkeypress="removeIncomplete('uNomeCompleto')" required />
                    <div class="form-row">
                        <div class="col">
                            <input name="uEmail" autocomplete="off" type="email" placeholder="Email" class="form-control uEmailCadastro" onkeypress="removeIncomplete('uEmailCadastro')" required />
                        </div>
                        <div class="col">
                            <input name="uSenha" autocomplete="off" type="password" placeholder="Senha" class="form-control uSenhaCadastro" onkeypress="removeIncomplete('uSenhaCadastro')" required />
                        </div>
                    </div>
                    <input name="uCpf" type="text" autocomplete="off" placeholder="CPF" class="form-control ucpf" onkeypress="removeIncomplete('ucpf')" required />
                    <div class="form-row radios">
                        <div class="col-6">
                            <label id="optionsradio_label1">
                                <input type="radio" name="user_genero" id="optionsRadios1" value="feminino" checked /> <img src="assets/img/fem_gender_icon.png" id="genderFemaleIconCadastrar" /> &nbsp; Feminino
                            </label>
                        </div>
                        <div class="col-6">
                            <label id="optionsradio_label2">
                                <input type="radio" name="user_genero" id="optionsRadios2" value="masculino" /> <img src="assets/img/male_gender_icon.png" id="genderMaleIconCadastrar" /> &nbsp; Masculino
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btnRegistrar btlogin-signup">Registrar</button>
                </form>
            </div>
            <div class="form-container sign-in-container">
                <form action="validar_login.php" class="form-login-form" method="POST">
                    <h1>Log In</h1>
                    <input type="email" name="uEmail" autocomplete="off" placeholder="Email" class="uEmail form-control" onkeypress="removeIncomplete('uEmail')" required />
                    <input type="password" name="uSenha" autocomplete="off" placeholder="Senha" class="uSenha form-control" onkeypress="removeIncomplete('uSenha')" required />
                    <a href="#">Forgot your password?</a>
                    <button type="submit" class="btnLogin btlogin-signup">Entrar</button>
                </form>
            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <div class="text-center">
                            <img src="assets/img/icon_food1.jpg" id="logoimg" alt=" Logo" />

                        </div>
                        <button class="ghost" id="signIn">Entrar</button>
                        <footer>
                            <p class="text-center">
                                Developed by <a href="https://github.com/AnneLivia" target="u_black">Anne Livia</a>
                                <br />
                                © All Rights Reserved.
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                            </p>
                        </footer>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <div class="text-center">
                            <img src="assets/img/icon_food2.jpg" id="logoimg" alt=" Logo" />

                        </div>
                        <button class="ghost" id="signUp">Registrar</button>
                        <footer>
                            <p class="text-center">
                                Developed by <a href="https://github.com/AnneLivia" target="u_black">Anne Livia</a>
                                <br />
                                © All Rights Reserved.
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                            </p>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
</body>
<!-- END BODY -->

</html>