class ProductDropZoneDom {
    constructor(dropZoneDom) {
        this.dropzoneDom = dropZoneDom;
    }

    generateHtml(fileWrapper, file) {
        this.dropzoneDom.generateHtml(fileWrapper, file);

        let colorSelect = $('<select class="image-color">', {
            class: 'dropdown-colors',
            name: 'color_id'
        });

        $.each(COLORS, (i, v) => {
            const selectOptions = {
                value: v.value,
                'data-hex': v.hex,
                'data-title': v.title,
            };

            if (file.hasOwnProperty('color_id') && file.color_id === v.value) {
                selectOptions.selected = 'selected';
            }

            if (!file.hasOwnProperty('color_id') && i === 0) {
                file.color_id = v.value;
            }

            colorSelect.append($('<option>', selectOptions));
        });

        $('.dropzone-file__buttons:last-of-type', $('li:last-of-type', fileWrapper)).after(colorSelect);

        colorSelect.select2({
            minimumResultsForSearch: Infinity,
            templateSelection: this.#optionCallback,
            templateResult: this.#optionCallback,
            width: '200px'
        });
    }

    #optionCallback(state)
    {
        let $state = null;
        if (state.element) {
            $state = $(
                `<div class="d-flex justify-content-start align-items-center">
                    <span style="background: ${state.element.dataset.hex};width: 20px; height: 20px; display: block; border: 1px solid #000; margin-right: 15px"></span>
                    ${state.element.dataset.title}
                </div>`
            );
        }
        return $state;
    }
}

export default ProductDropZoneDom;
