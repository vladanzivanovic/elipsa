import baseFormMapper from "./BaseFormMapper";

class JobEditMapper {
    constructor() {
        if (! JobEditMapper.instance) {
            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`title_${locale}`] = '#title_'+locale;
                this.fields[`description_${locale}`] = '#description_'+locale;
            }

            JobEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return JobEditMapper.instance;
    }
}

const jobEditMapper = new JobEditMapper();

Object.freeze(jobEditMapper);

export default jobEditMapper;
