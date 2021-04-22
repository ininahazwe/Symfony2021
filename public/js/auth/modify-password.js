import ConfirmIdentity from "./modules/confirm-identity";

new ConfirmIdentity({
    controller_url: document.querySelector('form[name="reset_password"]').getAttribute('action'),
    element_to_listen: document.querySelector('form[name="reset_password"]'),
    fetch_options: {
        body: null,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Password-Modification': 'true'
        },
        method: 'POST'
    },
    type_of_event: "submit"
})