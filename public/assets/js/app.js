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
