<?php
    session_start();

    // se sessão está setada, então existe tabela users
    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $conexao = mysqli_connect('localhost', 'root', '') or die('Erro de conexão'.mysqli_connect_error());
        
        $bd = mysqli_select_db($conexao, "donate_system");

        if(empty($bd)) {
            if(!$createBD) {
                die("Erro ao se conectar com banco de dados");
            }
        }

        $result = mysqli_query($conexao, "DELETE FROM users WHERE email = '$email'");

        if($result) {
            // deletar as requisicoes e o endereco do usuario cadastrado no site
            $result = mysqli_query($conexao, "DELETE FROM desejo_doar WHERE id_requisicao IN (SELECT id FROM requisicoes_de_doacoes WHERE email = '$email')");
            $result = mysqli_query($conexao, "DELETE FROM requisicoes_de_doacoes WHERE email = '$email'");
            $result = mysqli_query($conexao, "DELETE FROM endereco_user WHERE email = '$email'");
            $result = mysqli_query($conexao, "DELETE FROM desejo_doar WHERE email_doador = '$email'");
            $result = mysqli_query($conexao, "DELETE FROM chat WHERE email_doador = '$email' OR email_solicitante = '$email'");
            echo "<script>alert('Conta removida com sucesso!')</script>";
        } else {
            echo "<script>alert('Erro ao remover conta!')</script>";
        }

        header("refresh: 0.5; url = login.php");
        mysqli_close($conexao);
    }
?>