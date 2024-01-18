import toastrService from "./Services/ToastrService";

class CoreController {
    #toastr;

    constructor() {
        this.#toastr = toastrService;
    }

    showFlashMsg() {
        if (window.Messages) {
            window.Messages.forEach(message => {
                this.#toastr.addOptions({timeOut: 10000});
                this.#toastr.success(message);
            });
        }
    }
}

export default CoreController;
