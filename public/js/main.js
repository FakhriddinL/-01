window.onload = function () {

    const server = new Server( { cbIsParty, cbIsAcceptParty, cbUpdateGame });
    const graph = new Graph({ cbClick });

    function cbClick(event) {
        const x = Math.floor(event.offsetX / 60);
        const y = Math.floor(event.offsetY / 60);
        server.turn(x, y);
    }

    function cbUpdateGame(data) {
        if (data && data.endGame) { // игра закончилась
            server.stopCallUpdateGame();
            alert(data.status);
            hide('gameField');
            show('initialWindow');
            show('logoutButton');
            freeUsers();
            server.isCallIsParty = true;
            server.startCallIsParty();
        } else {
            render(data);
        }
    }

    function cbIsParty() {
        show('acceptParty');
        document.getElementById('yesParty').onclick = function() {
            server.acceptParty('yes');
            hide('acceptParty');
            hide('initialWindow');
            show('gameField');
            server.isGame = true;
            server.startCallUpdateGame();
        };
        document.getElementById('noParty').onclick = function() {
            server.acceptParty('no');
            server.isCallIsParty = true;
            server.startCallIsParty();
            hide('acceptParty');
        };
    }
    
    function cbIsAcceptParty() {
        server.stopCallIsAcceptParty();
        hide('initialWindow');
        show('gameField');
        server.isGame = true;
        server.startCallUpdateGame();
    }

    // нарисовать игровое поле
    function render(data) {
        const field = data.field;
        graph.clear(); // очистить поле

        // нарисовать сетку
        graph.begin();
        for (var i = 0; i <= 540; i += 60) {
            graph.line(i, 0, i, 540, 'red');
            graph.line(0, i, 540, i, 'red');
        }
        graph.stroke();
        // рисуем толстую сетку
        graph.begin();
        for (var i = 0; i <= 540; i += 180) {
            graph.line(i, 0, i, 540, 'red', 5);
            graph.line(0, i, 540, i, 'red', 5);
        }
        graph.stroke();

        // нарисовать крестики/нолики
        // идем по большому полю (из больших квадратов)
        for (var i = 0; i < field.length; i++) {
            for (j = 0; j < field[i].length; j++) {
                // пошли по каждому малому полю
                const smallField = field[i][j].field;
                for (var i1 = 0; i1 < smallField.length; i1++) {
                    for (j1 = 0; j1 < smallField[i1].length; j1++) {
                        const y =  i * 180 + i1 * 60;
                        const x =  j * 180 + j1 * 60;
                        if (smallField[i1][j1] === 0) {
                            graph.sprite(x, y, 'Empty');
                        } else {
                            graph.sprite(x, y, smallField[i1][j1]);
                        }
                    }
                }
                // нарисовать жЫрный крестик или нолик
                if (field[i][j].result) {
                    graph.bigSprite(j, i, field[i][j].result);
                }
            }
        }
    }
    
    function hide(id) {
        document.getElementById(id).style.display = 'none';
    };
    
    function show(id) {
        document.getElementById(id).style.display = 'block';
    };
    
    function addUser(user) {
        const li = document.createElement('li');
        const button = document.createElement('button');
        button.innerHTML = 'Пригласить ' + user.login;
        button.addEventListener('click', function () {
            server.newParty(user.id);
            freeUsers();
        });
        li.appendChild(button);
        document.getElementById('freeUsers').appendChild(li);
    }
    
    async function freeUsers() {
        document.getElementById('freeUsers').innerHTML = '';
        const users = await server.getFreeUsers();
        for(var i = 0; i < users.length; i++) {
            addUser(users[i]);
        }
    };
    
    /*document.getElementById('turn').addEventListener('click', async function () {
        console.log(await server.turn(1, 2, 4));
    });*/

    document.getElementById('loginButton').addEventListener('click', async function() {
        const login = document.getElementById('login').value;
        const password = document.getElementById('password').value;
        if (login && password) {
            const token = await server.auth(login, password);
            if(token) {
                hide('auth');
                show('initialWindow');
                show('logoutButton');
                freeUsers();
            }
        } else {
            alert('Введите логин или пароль!!!');
        }
    }); 

    document.getElementById('logoutButton').addEventListener('click', async function() {
        const logout = await server.logout();
        if(logout) {
            show('auth');
            hide('initialWindow');
            hide('gameField');
            hide('logoutButton');
        }
    });

    document.getElementById('registrationButton').addEventListener('click', async function() {
        const login = document.getElementById('login').value;
        const password = document.getElementById('password').value;
        if (login && password) {
            console.log(await server.registration(login, password));
        } else {    
            alert('Введите логин или пароль!!!');
        }
    });
};