<div class="wrap">
	<h1> Coupon Payments </h1>

	<?php settings_errors();?>

	<table id="paymentsTable" class="display compact" style="width:100%;">
		<thead>
			<th>ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Order ID</th>
			<th>Coupon ID</th>
			<th>Amount</th>
			<th>Transaction Ref</th>
			<th>Payment Type</th>
			<th>Payment Date</th>
			<th>Status</th>
			<th>Coupon</th>
		</thead>
		<tbody>
			{% for payment in payments  %}
				<tr>
					<td>{{ payment.id }}</td>
					<td>{{ payment.fname }}</td>
					<td>{{ payment.lname }}</td>
					<td>{{ payment.email }}</td>
					<td>{{ payment.phone }}</td>
					<td>{{ payment.order_id }}</td>
					<td>{{ payment.coupon_id }}</td>
					<td>{{ payment.amount }}</td>
					<td>{{ payment.transaction_ref }}</td>
					<td>{{ payment.payment_type }}</td>
					<td>{{ payment.payment_date }}</td>
					<td>{{ payment.status }}</td>
					<td>{{ payment.customer_coupon }}</td>
				</tr>
			{% endfor %}			
		</tbody>
	</table>
</div>
