import fotoramaMapper from "../Mapper/FotoramaMapper";

require ('fotorama/fotorama.js');

class Fotorama {
    #fotorama;
    #element;
    #position;
    #mapper;

    constructor(element, position) {
        this.#element = element;
        this.#position = position;
        this.#mapper = fotoramaMapper;

        this.#fotorama = this.#element.fotorama({
            minwidth: '100%',
            maxwidth: '100%',
            maxheight: '700px',
            loop: true,
        }).data('fotorama');
    }

    load(data) {
        this.#fotorama.load(data);
        this.#fotorama.show(0);
    }

    registerEvents()
    {
        if ('vertical' === this.#position) {
            $(document).on('click', this.#mapper.navBtn.next, e => {
                const wrapperHeight = $(this.#mapper.wrapper)[0].clientHeight;
                const noPrevItems = $(`${this.#mapper.thumbActive}`).prevAll(`${this.#mapper.thumb}`).length + 1;

                const thumbHeight = $(`${this.#mapper.thumb}`)[0].clientHeight;

                const yTranslate = (thumbHeight * noPrevItems) - wrapperHeight;

                $('.fotorama__nav__shaft').css('transform', `translate3d(0px, -${yTranslate}px, 0px)`);
            })

            $(document).on('click', this.#mapper.navBtn.prev, e => {
                const wrapperHeight = $(this.#mapper.wrapper)[0].clientHeight;
                const noPrevItems = $(`${this.#mapper.thumbActive}`).prevAll(`${this.#mapper.thumb}`).length + 1;

                const thumbHeight = $(`${this.#mapper.thumb}`)[0].clientHeight;

                const yTranslate = (thumbHeight * noPrevItems) - wrapperHeight;

                $('.fotorama__nav__shaft').css('transform', `translate3d(0px, -${yTranslate}px, 0px)`);
            })
        }
    }
}

export default Fotorama;
