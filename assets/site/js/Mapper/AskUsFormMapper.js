class AskUsFormMapper {
    constructor() {
        if (!AskUsFormMapper.instance) {
            this.form = '#ask-us-form';
            this.submitBtn = '#ask_us_submit';

            this.fields = {
                telephone: '#telephone',
            };

            AskUsFormMapper.instance = this;
        }

        return  AskUsFormMapper.instance;
    }
}

const askUsFormMapper = new AskUsFormMapper();

Object.freeze(askUsFormMapper);

export default askUsFormMapper;
