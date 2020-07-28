class CareerPageMapper {
    constructor() {
        if (!CareerPageMapper.instance) {
            this.form = '#career_form';
            this.firstName = '#first_name';
            this.lastName = '#last_name';
            this.mobilePhone = '#mobile_phone';
            this.email = '#email';
            this.address = '#address';
            this.city = '#city';
            this.school = '#school';
            this.schoolLevel = '#school_level';
            this.schoolTitle = '#school_title';
            this.position = '#position';
            this.accompanyngLetter = '#accompanying_letter';
            this.policy = '#policy';
            this.submitBtn = '#career_submit';

            CareerPageMapper.instance = this;
        }

        return  CareerPageMapper.instance;
    }
}

const careerPageMapper = new CareerPageMapper();

Object.freeze(careerPageMapper);

export default careerPageMapper;