import toastr from "toastr"

export default (() => {
    var Notification = {};

    Notification.showLoadingMessage = function () {
        toastr.options.timeOut = 0;
        this.show('info', Translator.trans('notifications.please_wait', null, 'messages', LOCALE), true);
    };

    Notification.show = function(type, message, reopen = true, title) {
        this.reset(reopen);
        this.toastr = toastr[type](message, title);

        return this.toastr;
    };

    Notification.reset = function (reopen) {
        if (reopen && this.toastr) {
            toastr.options = {};
            toastr.clear(this.toastr);
        }
    };

    Notification.setOptions = (options) => {
        toastr.options = options;
    };

    Notification.remove = function() {
        toastr.clear(this.toastr);
    };

    return Notification;
});