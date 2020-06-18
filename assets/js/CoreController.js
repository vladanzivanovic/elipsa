import NotificationService from "./NotificationService";

const Private = Symbol('private');

class CoreController {
    constructor() {}

    showFlashMsg() {
        if (window.Messages) {
            let notify = NotificationService();
            window.Messages.forEach(message => {
                notify.show('success', message, true);
            });
        }
    }
}

export default CoreController;