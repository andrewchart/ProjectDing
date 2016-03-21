<?php

/*
 * About Me:
 * INTERROGATE THE TRANSACTIONS TABLE AND QUEUE UP NOTIFICATION
 * DATA IN THE NOTIFICATIONS TABLE READY FOR SENDING AS PUSH NOTIFICATIONS. 
 *
**/


//Select transactions that have not had a push notification 
//sent yet from the transactions table
$sql = "SELECT order_id FROM pding_transactions WHERE notification_sent = 2";

if ($result = mysqli_query($conn, $sql)) {

	$ids = array();
	
	while ($row = mysqli_fetch_row($result)) {
		array_push($ids, $row[0]); 
	}
}

print_r($ids);
$count = count($ids);


//One order for one item only
if($count == 1) {

	createNotification($ids, 0);
	
} 

else {

	$uniqueIds = array_unique($ids);
	print_r($uniqueIds);
	
	//One order for multiple items
	if(count($uniqueIds) == 1) {
		createNotification($uniqueIds, 1);
	} 
	
	//Multiple orders
	else {
		createNotification($uniqueIds, 2);
	}

}



//Create the appropriate Notification
function createNotification($ids, $msgType) {
	
	//Set initial vars
	$conn = $GLOBALS['conn'];
	$curr = "&pound;";
	$path = $_SERVER['HTTP_HOST'] . "/ProjectDing";
	$num_orders = count(array_unique($ids)); 
	$order_ids = implode($ids, ",");
	$orders_total = 0;
	$other_items_qty = -1;
	$s = null;

	//Execute query
	$sql = "SELECT * from pding_transactions WHERE order_id IN($order_ids) ORDER BY subtotal ASC";
	$result = mysqli_query($conn,$sql);
		
	//Incremental vars
	while ($row = mysqli_fetch_assoc($result)) {
	
		//Sum the subtotals
		$orders_total += $row['subtotal'];
		
		//Increment additional items qty
		$other_items_qty++;
		
		//(Lazy) - setting main item vars each time because last row will be most expensive
		$main_item_name = $row['product_name'];
		$main_item_qty = $row['quantity'];
		$main_item_sku = $row['product_code'];
		
	}
	
	//More vars
	if($other_items_qty > 1) $s = "s";
	$main_item_image = getImage($main_item_sku);
			
	
	//Possible notification types
	$msgs = array(
		0 => array(
			"desc"   => "One order for one item only",
			"title"  => $num_orders . " new web order for " . $curr . $orders_total,
			"body"	 => "New order received for " . $main_item_qty . "x " . $main_item_name . ". Click to view order details.",
			"action" => $path . "/view_order.php?ids=" . $order_ids,
			"image"  => $main_item_image
		),
		
		1 => array(
			"desc" 	 => "One order for multiple items",
			"title"  => $num_orders . " new web order for " . $curr . $orders_total,
			"body"	 => "New order received for " . $main_item_qty . "x " . $main_item_name . " and " . $other_items_qty . " other item$s. Click to view order details.",
			"action" => $path . "/view_order.php?ids=" . $order_ids,
			"image"  => $main_item_image
		),
		
		2 => array(
			"desc" => "Multiple orders",
			"title"  => $num_orders . " new web orders received totalling " . $curr . $orders_total,
			"body"	 => "Click here to view the details of these orders.",
			"action" => $path . "/view_order.php?ids=" . $order_ids,
			"image"  => $main_item_image
		)
	);
	
	echo "<pre>";
	print_r($msgs[$msgType]);
	echo "</pre>";
	
	
	//Insert notification into table
	
}



//Get the image thumbnail url
function getImage($sku) {
	return ".jpg";
}





?>