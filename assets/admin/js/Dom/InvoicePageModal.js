import ConfirmationModalService from "../Services/ConfirmationModalService";

class InvoicePageModal {
    constructor() {
        if (!InvoicePageModal.instance) {
            InvoicePageModal.instance = this;
        }

        return InvoicePageModal.instance;
    }

    invoiceStatusModal()
    {
        const title = 'Izmena statusa porudžbine';

        const buttons = [
            {
                type: 'button',
                text: 'Primeni',
                'class': 'btn btn-primary invoice-state-apply',
                'data-dismiss': "modal"
            },
        ];

        let options = '';

        for (let status in INVOICE_STATUSES) {
            const isSelected = INVOICE_STATUSES[status] === SELECTED_STATUS ? 'selected = \'selected\'' : '';

            options += `<option value="${INVOICE_STATUSES[status]}" ${isSelected}>${Translator.trans(`order.status.${INVOICE_STATUSES[status]}`)}</option>`;
        }

        const body = `
            <form id="order_tracking_info_form" enctype="multipart/form-data">
                <div class="form-group row justify-content-between">
                    <label for="tracking-url-input" class="col-sm-3 col-form-label">Url za praćanje pošiljke</label>
                    <div class="col-md-8">
                        <input id="tracking-url-input" name="tracking_info[url]" type="url" value="${TRACKING_INFO !== null ? TRACKING_INFO.url : ''}" class="form-control" />
                    </div>
                </div>
                <div class="form-group row justify-content-between">
                    <label for="tracking-url-input" class="col-sm-3 col-form-label">Broj pošiljke</label>
                    <div class="col-md-8">
                        <input id="tracking-url-input" name="tracking_info[ref_no]" type="text" value="${TRACKING_INFO !== null ? TRACKING_INFO.ref_no : ''}" class="form-control" />
                    </div>
                </div>
                <div class="form-group row justify-content-between">
                    <label for="tracking-url-input" class="col-sm-3 col-form-label">Status</label>
                    <div class="col-md-8">
                        <select name="status" class="state-select">${options}</select>
                    </div>
                </div>
            </form>
        `;

        this.#generate(title, buttons, body);
    }

    #generate(title, buttons, body)
    {
        const confirmModal = new ConfirmationModalService(title, buttons, body);

        confirmModal.trigger('show');
    }
}
const invoicePageModal = new InvoicePageModal();

Object.freeze(invoicePageModal);

export default invoicePageModal;
