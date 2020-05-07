class LoaderDom {
    constructor() {
        if (!LoaderDom.instance) {
            $('body').append('<div id="page-loader" class="hide"></div>');
            LoaderDom.instance = this;
        }

        return LoaderDom.instance
    }

    show() {
        $('#page-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });
    }

    hide() {
        $('#page-loader').addClass('hide');
    }
}

const loader = new LoaderDom();

Object.freeze(loader);

export default loader;