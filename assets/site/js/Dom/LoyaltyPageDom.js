import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";

class LoyaltyPageDom {
    constructor() {
        this.mapper = loyaltyPageMapper;

    }

    generateDays() {
        const month = $(this.mapper.monthField).val();
        const year = $(this.mapper.yearField).val();
        const dayNumbers = new Date(year, month, 0).getDate();

        $(this.mapper.dayField).empty();

        for (let i = 1; i <= dayNumbers; i++) {
            $(this.mapper.dayField).append(
                `<option value="${i}">${i}</option>`
            );
        }
    }

    generateYears() {
        const currentYear = new Date().getFullYear();
        let fromYear = currentYear - 70;
        let toYear = currentYear - 18;

        $(this.mapper.yearField).empty();

        while ( fromYear <= toYear) {
            $(this.mapper.yearField).append(
                `<option value="${fromYear}">${fromYear}</option>`
            );

            fromYear++;
        }
    }
}

export default LoyaltyPageDom;