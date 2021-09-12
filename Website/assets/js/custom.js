(function ($) {
    "use strict";
    var mainApp = {

        main_fun: function () {
            /*====================================
            METIS MENU 
            ======================================*/
            $('#main-menu').metisMenu();

            /*====================================
          LOAD APPROPRIATE MENU BAR
       ======================================*/
            $(window).bind("load resize", function () {
                if ($(this).width() < 768) {
                    $('div.sidebar-collapse').addClass('collapse')
                } else {
                    $('div.sidebar-collapse').removeClass('collapse')
                }
            });



        },

        initialization: function () {
            mainApp.main_fun();

        }

    }
    // Initializing ///

    $(document).ready(function () {
        mainApp.main_fun();
    });

}(jQuery));


// method Jquery 
$(".table-index-requisicoes-doacoes thead").click(function () {
    window.location.href = "desejo_doar.php";
});

// method Jquery 
$(".table-index-doadores thead").click(function () {
    window.location.href = "desejo_doar.php";
});


// method Jquery para atualizar Paciente na tabela
$(".table-ativos-requisicoes-doacao tr").click(function () {
    var id = $(this).closest('tr').attr('id');
    if (id != undefined) {
        // visualizar itens em uma pagina. apenas visualizar.
        window.location.href = "visualizar_requisicaoDeDoacao.php?id=" + id;
    }
});

// method Jquery para atualizar Paciente na tabela
$(".table-ativos-doacoes-solicitadas tr").click(function () {
    var id = $(this).closest('tr').attr('id');
    if (id != undefined) {
        // visualizar itens em uma pagina. apenas visualizar.
        window.location.href = "visualizar_requisicaoDeDoacao_desejoDoar.php?id=" + id;
    }
});

// method Jquery para visualizar dados de uma requisicao a partir da tela de minhas doacoes.
$(".table-ativos-doacoes-atendidas tr").click(function () {
    var id = $(this).closest('tr').attr('id');
    if (id != undefined) {
        // visualizar itens em uma pagina. apenas visualizar.
        window.location.href = "visualizar_requisicaoDeDoacao_minhasDoacoes.php?id=" + id;
    }
});


// os ENTREGUES nao sao deletados que é para manter o controle do sistema

// method Jquery para remover requisicao de tabela
$(".table_ativos").on('click', '.deleteRequestDoacao', function () {
    var id = $(this).closest('tr').attr('id');
    var status = $(this).closest('tr').attr('class');
    // só pode remover se estiver aguardando ou inspirado, (acionado e atendido não podem ser removido, para manter o controle)
    if (status == 'AGUARDANDO' || status == 'EXPIRADO') {
        if (confirm("Tem certeza que deseja cancelar a requisição desta doação?")) {
            $(this).closest('tr').remove();
            window.location.href = "delete_bdRequisicaoDeDoacao.php?id=" + id;
        } else {
            window.location.href = "minhas_requisicoes.php";
        }
    } else {
        alert("Não é possível cancelar a requisição de doação selecionada. O status do item é " + status);
        window.location.href = "minhas_requisicoes.php";
    }
}); 

$(".table_ativos").on('click', '.deleteDoacaoEntregue', function () {
    var id = $(this).closest('tr').attr('id');
    var status = $(this).closest('tr').attr('class');
    if (confirm("Tem certeza que deseja remover os dados referentes a essa doação já finalizada?")) {
        $(this).closest('tr').remove();
        window.location.href = "delete_bdRequisicaoDeDoacaoEntregue.php?id=" + id;
    } else {
        window.location.href = "minhas_requisicoes.php";
    }
});

// method Jquery para cancelar doacao
// SE FINALIZADO OU REJEITADO NOA PODE REMOVER, PARA EVITAR QUE UMA PESSOA REJEITADA POSSA DOAR NOVAMENTE
$(".table_ativos").on('click', '.cancelarDoacao', function () {
    var id = $(this).closest('tr').attr('id');
    var status = $(this).closest('tr').attr('class');
    // só pode cancelar se o status da doacao nao tiver sido aprovado
    if (status == 'AGUARDANDO') {
        if (confirm("Tem certeza que deseja cancelar a doação ?")) {
            $(this).closest('tr').remove();
            window.location.href = "delete_bdDesejo_doar.php?id=" + id;
        } else {
            window.location.href = "minhas_doacoes.php";
        }
    } else {
        if (status == 'ENTREGUE' || status == 'FINALIZADO') {
            alert("Somente o usuário que demandou o alimento pode remover o item selecionado. Isso serve para manter o controle dos dados do sistema. Desculpe o imprevisto.");
        } else {
            alert("Não é possível cancelar a doação selecionada. O status do item é " + status);
        }
        window.location.href = "minhas_doacoes.php";
    }
});

// method Jquery para cancelar doacao
$(".table_ativos").on('click', '.confirmarRejeicao', function () {
    var id = $(this).closest('tr').attr('id');
    window.location.href = "confirmar_rejeicao_bdDoacao.php?id=" + id;
});

$(".table_ativos").on('click', '.confirmarEntrega', function () {
    var id = $(this).closest('tr').attr('id');
    window.location.href = "confirmar_entrega_bdDoacao.php?id=" + id;
});



// method Jquery para remover requisicao de tabela
$(".table_ativos").on('click', '.atualizarRequestDoacao', function () {
    var id = $(this).closest('tr').attr('id');
    var status = $(this).closest('tr').attr('class');
    // só pode editar se estiver aguardando (inspirado, acionado, atendido não podem ser editados)
    if (status == 'AGUARDANDO') {
        window.location.href = "atualizar_requisicaoDeDoacao.php?id=" + id;
    } else {
        alert("Não é possível editar a requisição de doação selecionada. O status do item é " + status);
        window.location.href = "minhas_requisicoes.php";
    }
});

// method Jquery para remover requisicao de tabela
$(".table_ativos").on('click', '.doarAlimento', function () {
    var id = $(this).closest('tr').attr('id');
    window.location.href = "create_bdDesejoDoar.php?id=" + id;
});

// metodo para abrir chat
$(".table_ativos").on('click', '.abrirChat', function () {
    var id = $(this).closest('tr').attr('id');
    window.location.href = "chat.php?id=" + id;
});

document.querySelector("#Logout").addEventListener('click', e => {
    e.preventDefault();
    window.location.href = "login.php";
});

$("#btn-requisitar-doacao").click(function () {

    let date = document.querySelector("#user_dataLimite");

    var today = new Date();

    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    // só deve permitir datas atuais
    // se o valor escolhido for antes de hoje, não permitir
    if (date.value < today) {
        alert("Escolha uma data válida.");
        return false;
    } else {
        document.querySelector('.form-solicitar-doacao').submit();
    }
});

$("#btn-atualizar-requisicao-de-doacao").click(function () {
    let date = document.querySelector("#user_dataLimite_atualizar");

    var today = new Date();

    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    // só deve permitir datas atuais
    // se o valor escolhido for antes de hoje, não permitir
    if (date.value < today) {
        alert("Escolha uma data válida.");
        return false;
    } else {
        document.querySelector('.form-atualizar-solicitar-doacao').submit();
    }
});


$(".btn-proceder-aprovar-rejeitar").click(function () {
    if (confirm("Você tem certeza da opção escolhida? Você não terá como retroceder.")) {
        document.querySelector('.mudar_status_bdDoacao.php').submit();
    } else {
        return false;
    }
});

$(".btn-acessar-mensagem").click(function () {
    var button_value = $(this).val(); 
    var dado = button_value.split(" "); // pegando id da requisicao e email
    window.location.href = "chat_solicitante.php?id=" + dado[0] + "&email_doador="+dado[1];
});


