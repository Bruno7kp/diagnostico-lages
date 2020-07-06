const Auth = {
    start: function() {
        Auth.login();
        Auth.logout();
    },
    login: function() {
        let form = document.querySelector("form#form-login");
        if (form !== null) {
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                let data = new FormData(form);
                fetch("/auth/login", {
                    method: "post",
                    credentials: "same-origin",
                    headers: {
                        "Accept": "application/json",
                    },
                    body: data,
                }).then((response) => {
                    if (response.status === 200) {
                        window.location.href = "/admin";
                    } else {
                        response.json().then((j) => {
                            Swal.fire({
                                title: 'Oops!',
                                text: j.message,
                                icon: 'error',
                            });
                        });
                    }
                });
            });
        }
    },
    logout: function() {
        let button = document.querySelector("button#button-logout");
        if (button !== null) {
            button.addEventListener("click", function(event) {
                fetch("/auth/logout", {
                    method: "post",
                    credentials: "same-origin"
                }).then((response) => {
                    window.location.href = "/login";
                });
            });
        }
    },
};

const Manager = {
    start: function() {
        this.send();
        this.loadData();
    },
    loadData: function() {
        // Campo para definir o ano
        let input = document.querySelector("#indicator_period");
        if (input === null)
            return;
        // Campos 'hidden' onde o ano deveser atualizado
        let periodsInput = document.querySelectorAll(".period-input");
        // Formulário
        let form = document.querySelector("#indicator-value-form");
        // Todos os campos de valor e descrição, que devem ser atualizados conforme o ano
        let formInputs = form.querySelectorAll("input[type=text], textarea");
        if (input) {
            // Id do índice
            let id = document.querySelector("#indicator_id").value;
            // Botão para atualizar o ano
            let refresh = document.querySelector("#indicator_period_refresh");
            refresh.addEventListener("click", () => {
                let wait = Swal.fire({
                    icon: 'info',
                    title: 'Aguarde...',
                    text: '',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });
                for (let i = 0; i < periodsInput.length; i++) {
                    periodsInput[i].value = input.value;
                }
                for (let i = 0; i < formInputs.length; i++) {
                    formInputs[i].value = "";
                }
                fetch("/indicator-value/" + id + "/" + input.value).then((response) => {
                    wait.close();
                    response.json().then((values) => {
                        for (let j = 0; j < values.length; j++) {
                            let val = values[j];
                            let inputText = document.querySelector(["[name='values[" + val.region_id + "][value]']"]);
                            if (inputText)
                                inputText.value = val.value;
                            let areaText = document.querySelector(["[name='values[" + val.region_id + "][description]']"]);
                            if (areaText)
                                areaText.value = val.description;
                        }
                    });
                });
            });
            refresh.click();
        }
    },
    send: function() {
        let forms = document.querySelectorAll("form[data-send]");
        for (let i = 0; i < forms.length; i++) {
            forms[i].addEventListener("submit", (event) => {
                event.preventDefault();
                let wait = Swal.fire({
                    icon: 'info',
                    title: 'Aguarde...',
                    text: '',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });
                let form = forms[i];
                let data = new FormData(form);
                fetch(form.action, {
                    method: form.method,
                    credentials: "same-origin",
                    headers: {
                        "Accept": "application/json"
                    },
                    body: data,
                }).then((response) => {
                    wait.close();
                    response.json().then((j) => {
                        if (response.status === 200 || response.status === 201) {
                            Swal.fire({
                                title: '',
                                text: j.message,
                                icon: 'success',
                            }).then((result) => {
                                if (typeof j.redirect !== "undefined") {
                                    window.location.href = j.redirect;
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Oops!',
                                text: j.message,
                                icon: 'error',
                            });
                        }
                    });
                });
            });
        }
    },
    tryRemove(url, id) {
        let data = new FormData();
        data.append('id', id);
        Manager.remove(url, data);
    },
    remove(url, data) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Você não poderá desfazer esta ação!",
            icon: 'warning',
            confirmButtonText: 'Sim, remova!',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
        }).then((result) => {
            if (result.value) {
                fetch(url, {
                    method: 'post',
                    credentials: "same-origin",
                    headers: {
                        "Accept": "application/json",
                    },
                    body: data,
                }).then((response) => {
                    if (response.status === 200) {
                        response.json().then((j) => {
                            Swal.fire({
                                title: 'Removido!',
                                text: j.message,
                                icon:'success',
                                timer: 2000,
                            });
                            $('.datatable').DataTable().ajax.reload();
                        });
                    } else {
                        response.json().then((j) => {
                            Swal.fire({
                                title: 'Oops!',
                                text: j.message,
                                icon: 'error',
                                timer: 2500,
                            });
                        });
                    }
                });
            }
        })
    },
};

const App = {
    start: function() {
        Auth.start();
        Manager.start();
    },
};

document.addEventListener("DOMContentLoaded", function() {
    App.start();
});
