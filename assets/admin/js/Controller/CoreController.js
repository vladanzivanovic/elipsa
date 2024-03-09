import BaseCoreController from "../../../js/CoreController";
import HeaderEvents from "../Event/HeaderEvents";

class CoreController {
    #handler;
    #mapper;

    constructor() {
        this.baseCore = new BaseCoreController();

        new HeaderEvents();
    }
}

export default CoreController;
