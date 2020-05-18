import MapsMapper from "../Mapper/MapsMapper";

class MapsService {
    constructor(mapOptions, panoramaOptions) {
        this.mapper = new MapsMapper();
        this.GEOLOCATION_JSON = 'http://maps.google.com/maps/api/geocode/json';
        this.STATUS = {
            OK: 'OK',
            ZERO_RESULTS: 'ZERO_RESULTS',
            OVER_QUERY_LIMIT: 'OVER_QUERY_LIMIT',
            REQUEST_DENIED: 'REQUEST_DENIED',
            INVALID_REQUEST: 'INVALID_REQUEST',
            UNKNOWN_ERROR: 'UNKNOWN_ERROR'
        };
        this.coordinates = [];

        this.defaultMapOptions = {
            zoom: 17,
            styles: [
                {
                    "featureType": "administrative.land_parcel",
                    "elementType": "labels",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels.text",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
            ],
        };
        this.defaultPanoramaOptions = {
            pov: {
                heading: 34,
                pitch: 10,
            },
        };

        this.setCoordinates('42.2911489', '18.840295');

        if (!window._GoogleMapsApi) {
            this.callbackName = '_GoogleMapsApi.mapLoaded';
            window._GoogleMapsApi = this;
            window._GoogleMapsApi.mapLoaded = this.mapLoaded.bind(this);
        }

        this.mapOptions = Object.assign(this.defaultMapOptions, mapOptions);
        this.panoramaOptions = Object.assign(this.defaultPanoramaOptions, panoramaOptions);

        // $streetTab = streetSelector;

        // var $height = $mapTab.hasAndGetData('parent');
        // if($height) {
        //     $mapTab.height($($mapTab.data('parent')).width() * 0.6);
        // }
        // this.mapOptions.center = this.panoramaOptions.position = this.getLatLng();
        // this.map = new google.maps.Map($mapTab[0], this.mapOptions);

        // if($streetTab) {
        //     $panorama = new google.maps.StreetViewPanorama($streetTab[0], this.panoramaOptions);
        // }
    }

    /**
     * Load
     * Create script element with google maps
     * api url, containing api key and callback for
     * map init.
     * @return {promise}
     * @this {_GoogleMapsApi}
     */
    load() {
        if (!this.promise) {
            this.promise = new Promise(resolve => {
                this.resolve = resolve;

                if (typeof window.google === 'undefined') {
                    const script = document.createElement('script');
                    script.src = `//maps.googleapis.com/maps/api/js?key=${window.mapsApiKey}&callback=${this.callbackName}&libraries=geometry`;
                    script.async = true;
                    document.body.append(script);

                } else {
                    this.resolve();
                }
            });
        }

        return this.promise;
    }

    /**
     * mapLoaded
     * Global callback for loaded/resolved map instance.
     * @this {_GoogleMapsApi}
     */
    mapLoaded() {

        if (this.resolve) {
            this.resolve();
        }
    }

    setCoordinates(lat, lng) {
        this.coordinates = [lat, lng];
        this.mapper.latInput.val(lat || '');
        this.mapper.lngInput.val(lng || '');
    }

    getLatLng()
    {
        return new google.maps.LatLng(this.coordinates[0], this.coordinates[1]);
    };

    measureDistance(lat, lng)
    {
        return google.maps.geometry.spherical.computeDistanceBetween(this.getLatLng(), new google.maps.LatLng(lat, lng));
    }

    showMap(mapContainer)
    {
        mapContainer = mapContainer ? mapContainer : this.mapper.map.get(0);

        if (!this.map) {
            this.map = new google.maps.Map(mapContainer, this.mapOptions);
        }

        this.mapOptions.center = this.panoramaOptions.position = this.getLatLng();

        this.myMarker = new google.maps.Marker({
            position: this.mapOptions.center,
            draggable: true,
            map: this.map,
        });

        google.maps.event.trigger(this.map, "resize");

        this.map.setCenter(this.mapOptions.center);
    };

    showMapWitMultipleMarkersWithPopupCallback(locations, callback)
    {
        const mapContainer = this.mapper.map.get(0);

        this.map = new google.maps.Map(mapContainer, this.mapOptions);

        this.setCoordinates(locations[0].country_lat, locations[0].country_lng);
        this.mapOptions.center = this.panoramaOptions.position = this.getLatLng();

        const bounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(locations[0].country_st_lat, locations[0].country_st_lng),
            new google.maps.LatLng(locations[0].country_nt_lat, locations[0].country_nt_lng) );

        for(let i = 0; i < locations.length; i++) {
            this.setCoordinates(locations[i].lat, locations[i].lng);

            let marker = new google.maps.Marker({
                position: this.getLatLng(),
                draggable: false,
                map: this.map,
            });

            marker.addListener('click', e => callback(locations[i]));
        }

        google.maps.event.trigger(this.map, "resize");

        this.map.setCenter(this.mapOptions.center);

        this.map.fitBounds(bounds);
    };

    /**
     * Address Array Example
     * [ address (with number), city, country ]
     * @param addressArray
     * @returns {Object} Promise
     */
    getMapsDataByAddress(addressArray, doMapUpdate) {
        const geocoder = new google.maps.Geocoder();

        return new Promise((resolve, reject) => {
            geocoder.geocode({'address': addressArray.join()}, (results, status) => {

                if (status == google.maps.GeocoderStatus.OK) {

                    if (doMapUpdate) {
                        this.coordinates = [results[0].geometry.location.lat(), results[0].geometry.location.lng()];

                        const position = this.getLatLng();

                        this.map.setCenter(position);
                        this.myMarker.setPosition(position);
                        this.mapper.latInput.val(this.coordinates[0]);
                        this.mapper.lngInput.val(this.coordinates[1]);
                    }

                    resolve(results);

                    return;
                }

                reject([status, results]);

            })
        });
    };


    registerEvents() {
        google.maps.event.addListener(this.myMarker, 'dragend', evt => {
            this.mapper.latInput.val(evt.latLng.lat().toFixed(3));
            this.mapper.lngInput.val(evt.latLng.lng().toFixed(3));
        });

        google.maps.event.addListener(this.myMarker, 'dragstart', function(evt){
            console.log(evt);
        });
    }
}

export default MapsService;