jQuery(document).ready( function ($) {

    const ipay_setter = $("#live");
    const vendor_id = $("#vendor_id");
    const hashkey = $("#hashkey");

    const smtp_setter = $("#settings");
    const server = $("#server");
    const port = $("#port");
    const encryption = $("encryption");
    const username = $("#username");
    const password = $("#password");

    $(ipay_setter).click(function () {
        if ($(this).is(":checked")) {
            $(vendor_id).removeAttr("disabled");
            $(hashkey).removeAttr("disabled");
            $(vendor_id).focus();
            $(hashkey).focus();
        } else {
            $(vendor_id).attr("disabled", "disabled");
            $(hashkey).attr("disabled", "disabled");
        }
    });

    $(smtp_setter).click(function () {
        if ($(this).is(":checked")) {
            $(server).removeAttr("disabled");
            $(server).focus();
            $(port).removeAttr("disabled");
            $(port).focus();
            $(encryption).removeAttr("disabled");
            $(encryption).focus();
            $(username).removeAttr("disabled");
            $(username).focus();
            $(password).removeAttr("disabled");
            $(password).focus();
        } else {
            $(server).attr("disabled", "disabled");
            $(port).attr("disabled", "disabled");
            $(encryption).attr("disabled", "disabled");
            $(username).attr("disabled", "disabled");
            $(password).attr("disabled", "disabled");

        }
    });

});