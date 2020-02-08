jQuery(document).ready( function () {
    jQuery('#paymentsTable').dataTable({
        scrollX: true,
        "aaSorting": [],
        columnDefs: [
            {targets: [0], orderable: false}
        ]
    });
} );