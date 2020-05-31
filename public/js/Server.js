class Server {

    token = null;
    isCallIsParty = false;
    isCallIsAcceptParty = false;
    isGame = false;
    hash = 'some first value';

    constructor(params) {
        this.cbIsParty = params.cbIsParty;
        this.cbIsAcceptParty = params.cbIsAcceptParty;
        this.cbUpdateGame = params.cbUpdateGame;
    }

    async send(method, data) {
        const arr = [];
        for (let key in data) {
            arr.push(`${key}=${data[key]}`);
        }
        if (this.token) {
            arr.push(`&token=${this.token}`);
        }
        const response = await fetch(`api/?method=${method}&${arr.join('&')}`);
        const answer = await response.json();
        if (answer && answer.result === 'ok') {
            return answer.data;
        } else if(answer && answer.result === 'error') {
            return false;
        }
    }

    async auth(login, password) {
        const data = await this.send('login', { login, password });
        if (data && data.token) {
            this.token = data.token;
            this.isCallIsParty = true;
            this.startCallIsParty();
        }
        return data;
    }

    logout() {
        this.stopCallIsParty();
        this.stopCallIsAcceptParty();
        return this.send('logout');
    }

    registration(login, password) {
        return this.send('registration', { login, password });
    }

    getFreeUsers() {
        return this.send('getFreeUsers');
    }

    newParty(id) {
        this.stopCallIsParty();
        this.isCallIsAcceptParty = true;
        this.startCallIsAcceptParty();
        return this.send('newParty', { id });
    }

    acceptParty(answer) {
        return this.send('acceptParty', { answer });
    }

    async startCallIsParty() {
        if (this.isCallIsParty) {
            const result = await this.send('isParty');
            if(result) {
                this.cbIsParty(result);
                this.stopCallIsParty();
            }
            this.startCallIsParty();
        }
    }
    stopCallIsParty() {
        this.isCallIsParty = false;
    }

    async startCallIsAcceptParty() {
        if (this.isCallIsAcceptParty) {
            const result = await this.send('isAcceptParty');
            if(result) {
                this.cbIsAcceptParty(result);
                this.stopCallIsAcceptParty();
            }
            this.startCallIsAcceptParty();
        }
    }
    stopCallIsAcceptParty() {
        this.isCallIsAcceptParty = false;
    }

    async startCallUpdateGame() {
        if (this.isGame) {
            const result = await this.send('getGame', { hash: this.hash });
            if (result) {
                this.hash = result.hash;
                this.cbUpdateGame(result);
            }
            this.startCallUpdateGame();
        }
    }
    stopCallUpdateGame() {
        this.isGame = false;
    }

    turn(x, y) {
        return this.send('turn', { x, y });
    }    
}