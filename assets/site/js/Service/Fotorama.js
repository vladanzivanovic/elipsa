require ('fotorama/fotorama.js');

class Fotorama {
    #fotorama;
    #element;

    constructor(element) {
        this.#element = element;
        this.#fotorama = this.#element.fotorama({
            minwidth: '100%',
            maxwidth: '100%',
            maxheight: '500px',
            loop: true,
        }).data('fotorama');
    }

    load(data) {
        this.#fotorama.load(data);
        this.#fotorama.show(0);
    }
}

export default Fotorama;
