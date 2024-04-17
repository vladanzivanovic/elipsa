import blogPageDom from "../Dom/BlogPageDom";
import blogListPageMapper from "../Mapper/BlogListPageMapper";
import headerDom from "../Dom/HeaderDom";

class BlogListPageService {
    #dom;
    #mapper;
    #headerDom;

    constructor() {
        this.#dom = blogPageDom;
        this.#mapper = blogListPageMapper;
        this.#headerDom = headerDom;

        $.scrollUp.init({
            scrollTrigger: $('<a/>', {
                id: 'scroll-to-blog-list',
                href: '#blogs',
                class: 'hide',
            }),
            scrollTarget: 50,
            easingType: 'linear',
            scrollSpeed: 1500,
            animation: 'fade'
        });
    }

    applyFilter(url)
    {
        $('#page-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: response => {
                $('.blogs').empty()
                    .append(this.#dom.generateBlog(response));

                $('#scroll-to-blog-list').trigger('click');
                $('#page-loader').addClass('hide');
                // $('#locale-dropdown li a').attr('href', response.localized_url);

                this.#headerDom.updateLanguageDropDown(response._web_links);
            },
            error: error => {

            }
        })
    }
}

export default BlogListPageService;
