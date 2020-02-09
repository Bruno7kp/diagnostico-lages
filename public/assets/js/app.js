const App = {
    start: function() {
        App.listeners();
    },
    listeners: function() {
        App.login();
        App.logout();
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

document.addEventListener("DOMContentLoaded", function() {
    App.start();
});
