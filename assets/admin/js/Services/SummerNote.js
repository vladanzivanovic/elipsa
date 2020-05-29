import 'summernote/dist/summernote';

class SummerNote {
    constructor()
    {
        this.toolbar = [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture']],
        ];
    }

    initialize(el, callBacks)
    {
        const options = {toolbar: this.toolbar};

        if (callBacks) {
           options.callbacks = callBacks;
        }

        el.summernote(options);
    }
}

export default SummerNote;