import ConfirmationModalService from "../Services/ConfirmationModalService";

class ProductListModal {
    constructor() {
        if (!ProductListModal.instance) {
            ProductListModal.instance = this;
        }

        return ProductListModal.instance;
    }

    removeProduct(productSlug, productTitle)
    {
        const buttons = [
            {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-product', 'data-alias': productSlug, 'data-dismiss': "modal"},
        ];

        const title = `Da li ste sigurni da želite obrišete proizvod "${productTitle}"?`;

        this.#generate(title, buttons);
    }

    actionBox(data, type)
    {
        switch (type) {
            case 'set_bulk_discount':
                this.#discountModal();

                break;
            case 'home_page_status':
                this.#statusModal(data.position);

                break;
        }
    }

    #statusModal(position) {
        const buttons = [
            {
                type: 'button',
                text: 'Primeni',
                'class': 'btn btn-primary home-page-status-apply',
                'data-position': position,
                'data-dismiss': "modal"
            },
        ];

        const title = `Da li ste sigurni da želite da izvršite odabranu akciju?`;

        this.#generate(title, buttons);
    }

    #discountModal()
    {
        const title = 'Popust na izabranim proizvodima';
        const buttons = [
            {
                type: 'button',
                text: 'Primeni',
                'class': 'btn btn-primary discount-apply',
                'data-dismiss': "modal"
            },
        ];

        const body = `
            <div class="form-group row justify-content-between">
                <label for="discount-input" class="col-sm-3 col-form-label">Popust u procentima</label>
                <div class="col-md-8">
                    <input id="discount-input" type="number" value="" class="form-control" />
                </div>
            </div>
        `;

        this.#generate(title, buttons, body);
    }

    #generate(title, buttons, body)
    {
        const confirmModal = new ConfirmationModalService(title, buttons, body);

        confirmModal.trigger('show');
    }
}

const productListModal = new ProductListModal();

Object.freeze(productListModal);

export default productListModal;
