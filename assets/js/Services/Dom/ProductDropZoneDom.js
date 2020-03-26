class ProductDropZoneDom {
    constructor(dropZoneDom) {
        this.dropzoneDom = dropZoneDom;
    }

    generateHtml(fileWrapper, file) {
        this.dropzoneDom.generateHtml(fileWrapper, file);

        let colorSelect = $('<select>', {
            class: 'dropdown-colors',
            name: 'color'
        });

        $.each(COLORS, (i, v) => {
            const selectOptions = {
                value: v.value,
                'data-hex': v.hex,
            };

            if (file.hasOwnProperty('color') && file.color === v.value) {
                selectOptions.selected = 'selected';
            }

            if (!file.hasOwnProperty('color') && i === 0) {
                file.color = v.value;
            }

            colorSelect.append($('<option>', selectOptions));
        });

        $('.dropzone-file__buttons:last-of-type', $('li:last-of-type', fileWrapper)).append(colorSelect);

        colorSelect.select2({
            minimumResultsForSearch: Infinity,
            templateSelection: this.optionCallback,
            templateResult: this.optionCallback,
        });
    }

    optionCallback(state)
    {
        let $state = null;
        if (state.element) {
            $state = $(
                `<span style="background: ${state.element.dataset.hex};width: 20px; height: 20px; display: block"></span>`
            );
        }
        return $state;
    }
}

export default ProductDropZoneDom;