class AskUsPageMapper {
    constructor() {
        if (!AskUsPageMapper.instance) {
            this.form = '#ask-us-form';
            this.submitBtn = '#ask_us_submit';

            AskUsPageMapper.instance = this;
        }

        return  AskUsPageMapper.instance;
    }
}

const askUsPageMapper = new AskUsPageMapper();

Object.freeze(askUsPageMapper);

export default askUsPageMapper;