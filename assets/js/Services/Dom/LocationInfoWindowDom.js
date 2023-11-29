class LocationInfoWindowDom {
    #htmlParameters;

    constructor() {
        if (!LocationInfoWindowDom.instance) {
            LocationInfoWindowDom.instance = this;
        }

        return LocationInfoWindowDom.instance;
    }

    setParameters(location) {
        this.#htmlParameters = location;
    }

    getContent()
    {
        const location = this.#htmlParameters;
        const trans = location.translations[LOCALE];
        const imageFirstElm = Object.keys(location.media.images)[1];
        const imageLength = location.media.images[imageFirstElm].length;

        let image = location.media.images[imageFirstElm][0];

        let html = `
            <div class="location-map">
                <h2 class="location-title m-bt-2">${trans.title}</h2>
                <div class="m-bt-2"><img src="${image.file}" alt="Elipsa lokacija - ${trans.title}"></div>
                <div>
                    <ul class="location-details">
                        <li><label class="letter-capitalize">${Translator.trans('address')}:</label> ${trans.address_text}</li>
                        <li><label class="letter-capitalize">${Translator.trans('email')}:</label> ${location.email}</li>
                        <li><label class="letter-capitalize">${Translator.trans('email.order.seller.telephone')}:</label> ${location.telephone}</li>
                        <li><label class="letter-capitalize">${Translator.trans('location.modal.work_time')}:</label> ${location.work_time.work_days}</li>
                        <li><label class="letter-capitalize">${Translator.trans('location.modal.work_time_saturday')}:</label> 
                            ${'' !== location.work_time.saturday ? location.work_time.saturday : Translator.trans('closed')}
                        </li>
                        <li><label class="letter-capitalize">${Translator.trans('location.modal.work_time_sunday')}:</label> 
                            ${'' !== location.work_time.sunday ? location.work_time.sunday : Translator.trans('closed')}
                        </li>
                    </ul>
                </div>
            </div>
        `;

        return {
            html,
            'title': trans.title
        };
    }
}

const infoWindowDom = new LocationInfoWindowDom();

Object.freeze(infoWindowDom);

export default infoWindowDom;
