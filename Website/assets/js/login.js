const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('containerX');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

document.querySelector('.btnLogin').addEventListener('click', e => {
    e.preventDefault();
    let email = document.querySelector(".uEmail");
    let password = document.querySelector(".uSenha");

    if (!email.value) {
        email.classList.add("incomplete");

        if (!password.value) {
            password.classList.add("incomplete");
        }
    } else {

        if (!password.value) {
            password.classList.add("incomplete");
        } else {

            // enviando para o arquivo php, servidor
            // consigo através daqui, enviar os dados para php
            // da o click no bd
            document.querySelector('.form-login-form').submit();
        }
    }
});

function removeIncomplete(inputClass) {
    if (inputClass === 'uEmail') {
        let data = document.querySelector(".uEmail");
        data.classList.remove("incomplete");
    }

    if (inputClass === 'uSenha') {
        let data = document.querySelector(".uSenha");
        data.classList.remove("incomplete");
    }

    if (inputClass === 'uSenhaCadastro') {
        let data = document.querySelector(".uSenhaCadastro");
        data.classList.remove("incomplete");
    }

    if (inputClass === 'ucpf') {
        let data = document.querySelector(".ucpf");
        data.classList.remove("incomplete");
    }

    if (inputClass === 'uNomeCompleto') {
        let data = document.querySelector(".uNomeCompleto");
        data.classList.remove("incomplete");
    }

    if (inputClass === 'uEmailCadastro') {
        let data = document.querySelector(".uEmailCadastro");
        data.classList.remove("incomplete");
    }
}

document.querySelector(".btnRegistrar").addEventListener('click', e => {
    e.preventDefault();
    let email = document.querySelector(".uEmailCadastro");
    let password = document.querySelector(".uSenhaCadastro");
    let cpf = document.querySelector(".ucpf");
    let nome = document.querySelector(".uNomeCompleto");

    let ok = true;

    if (!email.value) {
        email.classList.add("incomplete");
        ok = false;
    }

    if (!password.value) {
        password.classList.add("incomplete");
        ok = false;
    }

    if (!cpf.value) {
        cpf.classList.add("incomplete");
        ok = false;
    }

    if (!nome.value) {
        nome.classList.add("incomplete");
        ok = false;
    }



    if (ok) {
        // enviando para o arquivo php, servidor
        // consigo através daqui, enviar os dados para php
        // da o click no bd
        document.querySelector('.form-cadastro-form').submit();
    }
});