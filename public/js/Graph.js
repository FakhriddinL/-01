class Graph {

    constructor(params) {
        this.canvas = document.getElementById('canvas');
        this.context = this.canvas.getContext('2d');
        this.canvas.addEventListener('click', params.cbClick);

        this.images = {
            X: new Image(),
            O: new Image(),
            Empty: new Image(),
        };
        this.images.X.src = 'public/images/imageX.jpg';
        this.images.O.src = 'public/images/imageO.jpg';
        this.images.Empty.src = 'public/images/imageEmpty.jpg';
    }

    clear() {
        this.context.fillStyle = '#fff';
        this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }

    sprite(x, y, type) {
        const SIZE = 60;
        if(type === 'X')
            this.context.drawImage(this.images.X, x, y, SIZE, SIZE);
        if(type === 'O')
            this.context.drawImage(this.images.O, x, y, SIZE, SIZE);
        /*if(type === 'Empty')
            this.context.drawImage(this.images.Empty, x, y, SIZE, SIZE);*/
    }

    bigSprite(x, y, type) {
        const SIZE = 60 * 3;
        if(type === 'X')
            this.context.drawImage(this.images.X, x*SIZE, y*SIZE, SIZE, SIZE);
        if(type === 'O')
            this.context.drawImage(this.images.O, x*SIZE, y*SIZE, SIZE, SIZE);
        /*if(type === 'Empty')
            this.context.drawImage(this.images.Empty, x*SIZE, y*SIZE, SIZE, SIZE);*/
    }

    line(x1, y1, x2, y2, color, width) {
        this.context.strokeStyle = color || '#000';
        this.context.lineWidth = width || 1;
        this.context.moveTo(x1, y1);
        this.context.lineTo(x2, y2);
    }

    begin() {
        this.context.beginPath();
    }
    stroke() {
        this.context.stroke();
    }
}