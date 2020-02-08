<div class="wrap">
    <h1> Coupon Payments </h1>    <br>

    <?php settings_errors(); ?>

    <table id="paymentsTable" class="table table-striped" style="width:100%;">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Order ID</th>
            <th>Amount</th>
            <th>Channel</th>
            <th>Reference</th>
            <th>Payment Date</th>
            <th>Status</th>
            <th>Coupon</th>
        </tr>
        </thead>
        <tbody>
        {% for payment in payments %}
        <tr>
            <td>{{ loop.index }}</td>
            <td nowrap>{{ "#{payment.fname} #{payment.lname}" }}</td>
            <td nowrap>{{ payment.email }}</td>
            <td>{{ payment.phone }}</td>
            <td nowrap>{{ payment.order_id }}</td>
            <td>{{ payment.amount }}</td>
            <td>{{ payment.payment_type|default('N/A') }}</td>
            <td>{{ payment.transaction_ref|default('N/A') }}</td>
            <td nowrap>{{ payment.payment_date is empty ? "N/A" : payment.payment_date|date('M d, Y') }}</td>
            <td>{{ payment.status }}</td>
            <td nowrap>{{ payment.customer_coupon }}</td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
</div>
