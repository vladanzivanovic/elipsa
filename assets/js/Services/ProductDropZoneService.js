import ProductDropZoneDom from "./Dom/ProductDropZoneDom";

class ProductDropZoneService {
    constructor(dropZoneService) {
        this.dropzoneService = dropZoneService;

        this.dropzoneService.dom = new ProductDropZoneDom(this.dropzoneService.dom);
    }

    init(parentWrapper) {
        this.dropzoneService.init(parentWrapper);
    }

    setFiles(files, name) {
        this.dropzoneService.setFiles(files, name);
    }
}

export default ProductDropZoneService;