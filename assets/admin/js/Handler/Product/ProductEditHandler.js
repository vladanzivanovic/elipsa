import Rest from "../../Rest/AdsEditRest";
import Mapper from "../../../../js/Mapper/AdsEditMapper";
import DropZoneService from "../../../../js/Services/DropZoneService";
import BackendValidator from "../../../../js/Validation/BackendValidator";
import FormService from "../../../../js/Services/FormService";
import Notification from "../../../../js/Services/NotificationService";
import AppHelperService from "../../../../js/Helper/AppHelperService";

export default (() => {
    let Public = {};

    Public.setAd = () => {
        let form = Mapper().form.serializeArray();

        form.push({
            name: 'ads_images',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages')),
        });

        Rest().setAd(form)
            .then(response => {
                location.href = Routing.generate('admin.dashboard_page');
            })
            .fail(error => {
                var errors = error.responseJSON;

                if (!AppHelperService.isJsonString(errors.error)) {

                }

                BackendValidator().validate(Mapper().form, errors);
            })
    };

    Public.editAd = () => {
        if (!Mapper().form.valid()) {
            return false;
        }
        let form = Mapper().form.serializeArray();
        const formService = new FormService();
        form = formService.sanitize(form);

        form.push({
            name: 'ads_images',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages')),
        });

        Rest().editAd(form)
            .then(response => {
                location.href = Routing.generate('admin.dashboard_page');
            })
            .fail(error => {
                var errors = error.responseJSON;

                BackendValidator().validate(Mapper().form, errors);
            })
    };

    Public.changeAdStatus = (checkbox, alias, status) => {
        Rest.changeStatus(alias, status)
            .then(response => {
                checkbox.parentElement.firstElementChild.innerText = response.text;
            })
    };

    return Public;
})();