const initPhoneMask = () => {
    //? 254 phone mask
    const phoneMask = $('.phone-mask');
    if (phoneMask.length === 0) return;

    if ($.isFunction($.fn.mask)) {
        phoneMask.mask('254799999999');
    }
};

const processingForm = isProcessing => {
    console.log({isProcessing});
    const button = $('#slash-coupon-redeem-form button[type=submit]');
    const text = isProcessing ? 'Processing...' : 'Pay now';
    button.text(text);
};

const redeemCoupon = () => {
    const form = $('#slash-coupon-redeem-form');
    form.on('submit', event => {
        event.preventDefault();
        const data = {};
        form.serializeArray()
            .forEach(field => {
                data[field['name']] = field['value'];
            });

        // Show processing
        processingForm(true);

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
            if (!res['success']) {
                // ?Handle error TODO:
                console.log(res);
                return;
            }
            const iframeURl = res['data']['iframeURL'];
            handleIframe(res['data']['iframeURL']);
        });
    });
};

const handleIframe = (url) => {
    const iframe = $("#ipay-iframe");
    const iframeWrapper = $('#iframe-wrapper');
    iframe.attr('src', url);

    iframe.on('load', event => {
        const src = iframe.attr('src');
        if (src === '') return;

        // ?Show iframe wrapper
        iframeWrapper.removeClass('hidden')
            .prev().addClass('hidden');

        // Remove processing
        processingForm(false);
    });

    // ?On payment dismiss
    $('body').on('click', "[data-dismiss='payment']", event => {
        event.preventDefault();

        // ?Reset iframe src
        iframe.attr('src', '');

        // ?Hide iframe wrapper, show form
        iframeWrapper.addClass('hidden')
            .prev().removeClass('hidden');
    });
};

jQuery(() => {
    window.$ = jQuery;
    initPhoneMask();
    redeemCoupon();
});
