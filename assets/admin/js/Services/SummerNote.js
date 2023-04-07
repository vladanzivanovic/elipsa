import 'summernote/dist/summernote';
import 'summernote-file';
import SummerNoteHandler from "../Handler/SummerNoteHandler";

class SummerNote {
    constructor()
    {
        this.handler = new SummerNoteHandler();

        this.toolbar = [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize', 'fontname', 'fontsizeunit']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture', 'video', 'file']],
        ];
    }

    initialize(el, callBacks)
    {
        const options = {toolbar: this.toolbar};

        options.fontSizes = ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '82', '150', '200', '250', '300'];
        options.fontSizeUnits = ['vw'];
        options.lineHeights = [
            '0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '1.6', '1.7', '1.9', '2.0', '2.1', '2.3', '2.5', '2.7', '2.8', '2.9', '3.0'
        ]

        if (callBacks) {
           options.callbacks = callBacks;
        }

        el.summernote(options);
    }

    createCallBacksSummernote(el, entity)
    {
        return {
            onImageUpload: files => {
                this.handler.sendSummernoteImage(el, files[0], entity)
                    .then(response => {
                        el.summernote('insertImage', response.file_url, function ($image) {
                            $image.attr('data-filename', response.file_name);
                        });
                    })
            },
            onMediaDelete: target => {
                this.handler.removeSummernoteImage(target[0].dataset.filename);
            },
            onFileUpload: files => {
                this.handler.sendSummernoteFile(el, files[0])
                    .then(response => {
                        const file = files[0];
                        let elem = document.createElement("a");
                        let linkText = document.createTextNode(file.name);
                        elem.appendChild(linkText);
                        elem.title = file.name;
                        elem.href = response.file_url;
                        el.summernote('editor.insertNode', elem);
                    });
            }
        }
    }
}

export default SummerNote;
