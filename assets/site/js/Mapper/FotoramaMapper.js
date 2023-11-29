class FotoramaMapper {
    constructor() {
        if (!FotoramaMapper.instance) {
            this.wrapper = '.fotorama__nav-wrap';

            this.navBtn = {
                next: '.fotorama__arr.fotorama__arr--next',
                prev: '.fotorama__arr.fotorama__arr--prev',
            };

            this.thumb = '.fotorama__nav__frame.fotorama__nav__frame--thumb';
            this.thumbActive = `${this.thumb}.fotorama__active`,

            FotoramaMapper.instance = this;
        }

        return FotoramaMapper.instance;
    }
}

const fotoramaMapper = new FotoramaMapper();

Object.freeze(fotoramaMapper);

export default fotoramaMapper;
