class CareerPageMapper {
    constructor() {
        if (!CareerPageMapper.instance) {
            this.form = '#career_form';
            this.submitBtn = '#career_submit';

            CareerPageMapper.instance = this;
        }

        return  CareerPageMapper.instance;
    }
}

const careerPageMapper = new CareerPageMapper();

Object.freeze(careerPageMapper);

export default careerPageMapper;