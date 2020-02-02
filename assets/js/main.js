const initPhoneMask = () => {
    //? 254 phone mask
    const phoneMask = $('.phone-mask');
    if (phoneMask.length === 0) return;

    if ($.isFunction($.fn.mask)) {
        phoneMask.mask('254799999999');
    }
};

const redeemCoupon = () => {
    /**
     * Steps:
     * 1. handle form values
     * 2. fetch iframe url
     * 3. show iframe with ipaygateway
     * 4. on payment finish remove iframe with message
     *
     */

    const form = $('#slash-coupon-redeem-form');
    form.on('submit', event => {
        event.preventDefault();
        const data = {};
        form.serializeArray()
            .forEach(field => {
                data[field['name']] = field['value'];
            });
        // TODO:

        // fetch iframe url
        $.ajax({
            method: 'post',
            url: redeem_form_ajax.ajax_url,
            data: {
                _ajax_nonce: redeem_form_ajax.nonce,
                action: 'redeem_coupon',
                data,
            },
            dataType: 'json'
        }).then(res => {
            console.log(res);
        })
    });
};
jQuery(() => {
    window.$ = jQuery;
    initPhoneMask();
    redeemCoupon();
});
