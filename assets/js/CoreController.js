import NotificationService from "./NotificationService";
import toastrService from "./Services/ToastrService";

const Private = Symbol('private');

class CoreController {
    constructor() {}

    showFlashMsg() {
        if (window.Messages) {
            let toastr = toastrService;
            window.Messages.forEach(message => {
                toastr.addOptions({timeOut: 10000});
                toastr.warning(message);
            });
        }
    }
}

export default CoreController;
