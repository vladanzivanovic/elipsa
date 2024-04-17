import AppHelperService from "../../../js/Helper/AppHelperService";

class BlogPageDom {
    constructor() {
        if (!BlogPageDom.instance) {

            BlogPageDom.instance = this;
        }

        return BlogPageDom.instance;
    }

    generateBlog(data) {
        let html = '';

        for(let i in data.blogs) {
            let blog = data.blogs[i];
            const trans = blog.translations[LOCALE];

            html += `<div class="single-blog">
                        <div class="blog-img">
                            <a href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: trans.slug})}"><img src="${ blog.media.images[0].file }" alt="${ blog.title }"></a>
                            <div class="blog-img-content">
                                <span class="bc-date">${ blog.date.day }</span>
                                <span class="bc-month">${ blog.date.month }</span>
                            </div>
                        </div>
                        <div class="blog-content">
                            <a class="blog-title" href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: trans.slug})}">${ trans.title }</a>
                            <p class="blog-text">${ trans.short_description }</p>
                            <a class="blog-read-more letter-capitalize" href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: trans.slug})}">${Translator.trans('read_more', null, 'messages', LOCALE)}</a>
                        </div>
                    </div>`;
        }

        return html;
    };
}

const blogPageDom = new BlogPageDom();
Object.freeze(blogPageDom);

export default blogPageDom;
