import baseFormMapper from "./BaseFormMapper";

class SizeEditMapper {
    constructor() {
        if (!SizeEditMapper.instance) {
            this.title = '#size';

            SizeEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return SizeEditMapper.instance;
    }
}

const sizeEditMapper = new SizeEditMapper();

Object.freeze(sizeEditMapper);

export default sizeEditMapper;
