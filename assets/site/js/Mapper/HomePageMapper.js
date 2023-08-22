class HomePageMapper {
    constructor() {
        if(!HomePageMapper.instance) {
            this.slider = '#home3_slider';
            this.topCarousel = '#top_carousel';
            this.bottomCarousel = '#bottom_carousel';

            HomePageMapper.instance = this;
        }

        return HomePageMapper.instance;
    }
}
const homePageMapper = new HomePageMapper();

Object.freeze(homePageMapper);

export default homePageMapper;
