import youtubeMapper from "../Mapper/YoutubeMapper";
import ArrayFilters from "../../../js/Filter/ArrayFilters";
import AppHelperService from "../../../js/Helper/AppHelperService";
import toastrService from "../../../js/Services/ToastrService";
import Cache from "../../../js/Helper/CacheHelper";

class YoutubeService {
    #mapper;
    #notification;
    #youtubePattern = [
        /(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=|\/sandalsResorts#\w\/\w\/.*\/))([^\/&]{10,12})/,
        /(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11})/
    ];
    #cacheKey = 'ads-youtube';
    
    constructor() {
        this.#mapper = youtubeMapper;
        this.#notification = toastrService;
        
        this.#registerEvents();
        this.#reset();
    }

    createVideo (url = null) {
        let youTubeId = url,
            match;

        if(!youTubeId) {
            youTubeId = $(this.#mapper.youtube).val();
        }

        if(AppHelperService.isUrl(youTubeId)){
            for(const i in this.#youtubePattern) {
                match = youTubeId.match(this.#youtubePattern[i]);

                if (match && match[1]) {
                    youTubeId = match[1];
                    break;
                }
            }
        }

        if (!youTubeId) {
            this.#notification.warning(Translator.trans('youtube.id_required', null, 'messages', LOCALE));
        }
        else if(!this.checkExistByProp('YouTubeId', youTubeId)){
            this.#notification.error(Translator.trans('youtube.exists', null, 'messages', LOCALE));
        }else {
            this.#getYouTube(youTubeId)
                .then((response) => {
                    const responseObj = this.#defaultObj();

                    response = response.items[0];

                    responseObj.YouTubeId = response.id;
                    responseObj.Title = response.snippet.title;
                    responseObj.Thumbnails = response.snippet.thumbnails;

                    this.setHtml(responseObj);

                    Cache.add(this.#cacheKey, responseObj);
                })
                .fail(error => {
                    this.#notification.error(Translator.trans('youtube.url_not_valid', null, 'messages', LOCALE));
                })
        }
    };

    setHtml(response) {
        const iframe = $('<iframe>', {src: `https://www.youtube.com/embed/${response.YouTubeId}`, frameborder: 0});

        $(this.#mapper.youtubeList).append(
            $('<li>', {'data-id': response.YouTubeId })
                .append( $('<span>', {
                    class: 'youtube-close',
                }))
                .append(iframe)
        );
        $(this.#mapper.youtube).val("");

    };

    setFromArray(data) {
        for(let i in data){
            this.setHtml(data[i]);

            Cache.add(this.#cacheKey, data[i]);
        }
    };

    getLists() {
        return Cache.get(this.#cacheKey);
    };

    /**
     * Remove youtube from array or
     * if is old add flag isDeleted
     * @param id
     * @return {boolean}
     */
    removeFromLists(id) {
        const youtubeCache = Cache.get(this.#cacheKey);
        const filtered = ArrayFilters.getObjectByParams(youtubeCache, {name: 'YouTubeId', value: id}, true);

        if(filtered.length === 0)
            return false;

        if(filtered[0].data.Id) {
            youtubeCache[filtered[0].index].isDeleted = true;
            return true;
        }

        youtubeCache.splice(filtered[0].index, 1);
    };

    /**
     * Check if youtube is already exist
     * @param prop
     * @param value
     * @return {boolean}
     */
    checkExistByProp(prop, value) {
        const selectedArray = ArrayFilters.getObjectByParams(Cache.get(this.#cacheKey), {name: prop, value: value});

        return selectedArray.length === 0
    }

    #defaultObj () {
        return {
            Id: null,
            AdsId: null,
            YouTubeId: null,
            Title: null,
            Thumbnails: null
        }
    };

    #getYouTube($id) {
        return $.get(`https://www.googleapis.com/youtube/v3/videos?id=${$id}&key=${YOUTUBE_API_KEY}&fields=items(id,snippet,statistics,player)&part=snippet,statistics,player`);
    };
    
    #registerEvents() {
        $(document).on('click touchend', this.#mapper.youtubeButton, e => {
            this.createVideo();
        });
        $(document).on('click touchend', this.#mapper.youtubeButtonClose, e => {
            let li = $(e.currentTarget).parent(),
                id = li.data('id');

            if(!id) {
                return false;
            }

            this.removeFromLists(id);

            li.remove();
        });
    }

    #reset() {
        $(this.#mapper.youtubeList).empty();
        Cache.set(this.#cacheKey, []);
    };
}

export default YoutubeService;
