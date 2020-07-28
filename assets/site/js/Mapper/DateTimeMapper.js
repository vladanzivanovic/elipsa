class DateTimeMapper {
    constructor() {
        if (!DateTimeMapper.instance) {
            this.dayField = '.bd-date';
            this.monthField = '.bd-month';
            this.yearField = '.bd-year';

            DateTimeMapper.instance = this;
        }

        return  DateTimeMapper.instance;
    }
}

const dateTimeMapper = new DateTimeMapper();

Object.freeze(dateTimeMapper);

export default dateTimeMapper;