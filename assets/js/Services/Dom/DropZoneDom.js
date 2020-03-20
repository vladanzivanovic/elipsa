class DropZoneDom {
    generateHtml(fileWrapper, file)
    {
        let mainClass = file.isMain ? 'main-image' : '';
        let colorSelect = '';

        let li = $('<li>', {
            class: 'dropzone-file ' + mainClass,
            'data-name': file.fileName,
        });
        let img = $('<img>', {
            src: file.file,
            class: 'dropzone-file-img'
        });

        let mainBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-icon main-image-btn',
        }).append($('<i>', {
            class: `fas ${file.isMain ? 'fa-check-double' : 'fa-check'}`,
        }));

        let removeBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-icon dropzone-close',
        }).append($('<i>', {
            class: 'fas fa-trash'
        }));

        if (COLORS) {
            colorSelect = $('<select>', {
                class: 'dropdown-colors',
                name: 'color'
            });

            $.each(COLORS, (i, v) => {
                const options = {
                    value: v.value,
                    'data-hex': v.hex,
                };

                if (file.hasOwnProperty('color') && file.color === v.value) {
                    options.selected = 'selected';
                }

                if (!file.hasOwnProperty('color') && i === 0) {
                    file.color = v.value;
                }

                colorSelect.append($('<option>', options));
            });

        }

        let buttonDiv = $('<div>', {
            class: 'dropzone-file__buttons'
        })
            .append(mainBtn)
            .append(removeBtn)
            .append(colorSelect);


        fileWrapper.append(
            li.append(img)
                .append(buttonDiv)
        );

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

export default DropZoneDom;