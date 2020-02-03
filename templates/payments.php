<div class="wrap">
	<h1> Coupon Payments </h1>

	<?php 
	settings_errors();

	// TODO: Call the fetchPayments method here

	?>

	<table id="paymentsTable" class="display compact" style="width:100%">
		<tbody>
			<th>ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Order ID</th>
			<th>Coupon ID</th>
			<th>Amount</th>
			<th>Transaction Ref</th>
			<th>Payment Type</th>
			<th>Payment Date</th>

			<?php
			if (!empty($results)) {
				foreach ($results as $row) {
	    			echo "<tr>";
		   				echo "<td>". $row->id ."</td>";
				    	echo "<td>". $row->fname ."</td>";
				    	echo "<td>". $row->lname ."</td>";
				    	echo "<td>". $row->order_id ."</td>";
					   	echo "<td>". $row->coupon_id ."</td>";
					   	echo "<td>". $row->amount ."</td>";
					   	echo "<td>". $row->transaction_ref ."</td>";
					   	echo "<td>". $row->payment_type ."</td>";
					  	echo "<td>". date("Y-m-d", strtotime($row->payment_date)). "</td>";
				    echo "</tr>";
	     		}
	     	}?>
		</tbody>
	</table>
</div>


<!-- TODO: declare datatables html and php here ??
ajax functionality in myscript.js file -->
