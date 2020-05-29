require ('../../../js/Validators/ValidationRuleHelper');

export default (() => {
    let Public = {};

    Public.validate = (form) => {
        var options = {
            ignore: '',
            rules: {
                rs_title: 'required',
                rs_short_description: 'required',
                rs_description: 'setErrorIfSummernoteIsEmpty',
                en_title: 'required',
                en_short_description: 'required',
                en_description: 'setErrorIfSummernoteIsEmpty',
                main_images: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                }
            },
        };
        $.extend(options, window.helpBlock);

        return form.validate(options);
    };

    return Public;
});