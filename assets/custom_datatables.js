jQuery(document).ready( function () {
    jQuery('.paymentsTable').DataTable({
        scrollX: true,
        "aaSorting": [],
        columnDefs: [
            {targets: [0,3,4,6], orderable: false},
            {targets: [2], class:"text-right"}
        ]
    });
} );