class BlogListPageMapper {
    constructor() {
        if(!BlogListPageMapper.instance) {
            this.tagBtn = '.tag-btn';
            this.tagList = '.bw-cat-list';

            BlogListPageMapper.instance = this;
        }

        return BlogListPageMapper.instance;
    }
}

const blogListPageMapper = new BlogListPageMapper();

Object.freeze(blogListPageMapper);

export default blogListPageMapper;