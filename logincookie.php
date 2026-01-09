<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Utente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2196F3;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px 0;
        }
        button:hover {
            background-color: #0b7dda;
        }
        #messaggio {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .successo {
            background-color: #d4edda;
            color: #155724;
        }
        .errore {
            background-color: #f8d7da;
            color: #721c24;
        }
        .link-registrazione {
            text-align: center;
            margin-top: 15px;
        }
        .link-registrazione a {
            color: #2196F3;
            text-decoration: none;
        }
        .link-registrazione a:hover {
            text-decoration: underline;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .remember-me input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }
        .remember-me label {
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
    <h2>Login Utente</h2>
    <form id="formLogin">
        <input type="text" id="username" placeholder="Username" required>
        <input type="password" id="password" placeholder="Password" required>
        
        <div class="remember-me">
            <input type="checkbox" id="rememberMe" checked>
            <label for="rememberMe">Ricorda il mio username</label>
        </div>
        
        <button type="submit">Accedi</button>
    </form>
    <div class="link-registrazione">
        Non hai un account? <a href="client.html">Registrati</a>
    </div>
    <div id="messaggio"></div>

    <script>
        // Funzione per impostare un cookie
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        // Funzione per ottenere un cookie
        function getCookie(name) {
            const nameEQ = name + "=";
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i];
                while (cookie.charAt(0) === ' ') {
                    cookie = cookie.substring(1);
                }
                if (cookie.indexOf(nameEQ) === 0) {
                    return cookie.substring(nameEQ.length);
                }
            }
            return null;
        }

        // Funzione per cancellare un cookie
        function deleteCookie(name) {
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        // Al caricamento della pagina, recupera lo username salvato
        window.addEventListener('load', function() {
            const savedUsername = getCookie('remembered_username');
            if (savedUsername) {
                document.getElementById('username').value = savedUsername;
                document.getElementById('rememberMe').checked = true;
                // Focus sulla password se lo username è già presente
                document.getElementById('password').focus();
            }
        });

        // Gestione del form di login
        document.getElementById('formLogin').addEventListener('submit', function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('rememberMe').checked;
            const messaggio = document.getElementById('messaggio');

            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username: username, password: password })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    // Gestione cookie per remember me
                    if (rememberMe) {
                        // Salva lo username per 30 giorni
                        setCookie('remembered_username', username, 30);
                    } else {
                        // Cancella il cookie se l'utente ha deselezionato l'opzione
                        deleteCookie('remembered_username');
                    }

                    messaggio.className = 'successo';
                    messaggio.textContent = data.message;
                    
                    // Reindirizza alla pagina protetta dopo 1 secondo
                    setTimeout(function() {
                        window.location.href = 'dashboard.html';
                    }, 1000);
                } else {
                    messaggio.className = 'errore';
                    messaggio.textContent = data.message;
                }
            })
            .catch(function () {
                messaggio.className = 'errore';
                messaggio.textContent = 'Errore di connessione al server';
            });
        });
    </script>
</body>
</html>
