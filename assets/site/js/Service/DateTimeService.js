import DateTimeDom from "../Dom/DateTimeDom";
import dateTimeMapper from "../Mapper/DateTimeMapper";

class DateTimeService {
    constructor() {
        this.mapper = dateTimeMapper;
        this.dom = new DateTimeDom();

        this.dom.generateYears();
        this.dom.generateDays();

        this.registerEvents();
    }

    getDateTimeString(format)
    {
        return $(this.mapper.monthField).val() + format +
            $(this.mapper.dayField).val() + format +
            $(this.mapper.yearField).val();
    }

    registerEvents() {
        $(this.mapper.monthField).on('change', e => {
            this.dom.generateDays();
        });
        $(this.mapper.yearField).on('change', e => {
            this.dom.generateDays();
        });
    }
}

export default DateTimeService;