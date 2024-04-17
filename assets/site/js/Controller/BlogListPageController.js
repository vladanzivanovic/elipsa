import 'webpack-jquery-ui';
import BlogPageRouting from "../Routing/BlogPageRouting";
import blogPageDom from "../Dom/BlogPageDom";
import blogListPageMapper from "../Mapper/BlogListPageMapper";
import BlogListPageService from "../Service/BlogListPageService";
import headerDom from "../Dom/HeaderDom";

class BlogListPageController {
    #mapper;
    #router;
    #blogService;
    #dom;
    #headerDom;

    constructor() {
        this.#mapper = blogListPageMapper;
        this.#router = new BlogPageRouting();
        this.#blogService = new BlogListPageService();
        this.#dom = blogPageDom;
        this.#headerDom = headerDom;

        this.#headerDom.updateLanguageDropDown(LINKS);

        this.registerEvents();
    }

    registerEvents() {
        $(this.#mapper.tagBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.toggleFilter(e, 'tags', e.currentTarget.dataset.search);
        });
    }

    toggleFilter(e, name, value) {
        e.preventDefault();
        e.stopPropagation();

        const elm = $(e.currentTarget);

        if (elm.hasClass('active')) {
            elm.removeClass('active');
        } else {
            $.each($(this.#mapper.tagBtn), (i, elm) => {
                $(elm).removeClass('active');
            });
            elm.addClass('active');
        }

        const apiUrl = this.#router.generateUrl(value);

        this.#blogService.applyFilter(apiUrl);
    }
}

export default BlogListPageController;
