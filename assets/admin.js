jQuery(document).ready( function ($) {
    const smtp_setter = $("#settings");
    const server = $("#server");
    const port = $("#port");
    const encryption = $("input[type=radio]");
    const username = $("#username");
    const password = $("#password");
    
    const fields = [
        server,
        port, 
        encryption, 
        username,     
        password,
    ];
    const check = $(smtp_setter).val();

    if (check == "0"){
        $.each(fields, function (index, value) {
            $(value).attr("disabled", "disabled").val("");
        });
    }
    else{
        $.each(fields, function (index, value) {
            $(value).removeAttr("disabled");
        });
    }
    $(smtp_setter).click(function () {
        if ($(this).is(":checked")) {
            $.each(fields, function (index, value) {
                $(value).removeAttr("disabled");
            });
            $(server).focus();
        } 
        else {
            $.each(fields, function (index, value) {
                $(value).attr("disabled", "disabled").val("");
            });
        }
    });
});