<?php

/*
 * About Me:
 * INTERROGATE THE TRANSACTIONS TABLE AND QUEUE UP NOTIFICATION
 * DATA IN THE NOTIFICATIONS TABLE READY FOR SENDING AS PUSH NOTIFICATIONS. 
 *
**/


//Select transactions that have not had a push notification 
//created for them yet from the transactions table
$sql = "SELECT order_id FROM pding_transactions WHERE notification_queued = 0";

if ($result = mysqli_query($conn, $sql)) {

	$ids = array();
	
	while ($row = mysqli_fetch_row($result)) {
		array_push($ids, $row[0]); 
	}
}

//print_r($ids);
$count = count($ids);

//No orders to create notifications for
if($count == 0) {

	echo "No notifications created<br><br>";
	//Have a little rest
}

//One order for one item only
else if($count == 1) {

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
	$curr = utf8_encode("£");
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
	
	$notification = $msgs[$msgType];
	
	echo "<pre>";
	print_r($notification);
	echo "</pre>";
	
	
	
	//Insert notification into table
	$sql = "INSERT INTO pding_notifications VALUES ('', '"
		. mysqli_real_escape_string($conn, $notification['title']) . "', '"
		. mysqli_real_escape_string($conn, $notification['body']) . "', '"
		. mysqli_real_escape_string($conn, $notification['action']) . "', '"
		. mysqli_real_escape_string($conn, $notification['image']) . "', NOW())";
	echo $sql;
	mysqli_query($conn, $sql);
	
	
	//Mark transactions as "sent"
	foreach($ids AS $id) {
		$sql = "UPDATE pding_transactions SET notification_queued=1 WHERE order_id = $id";
		mysqli_query($conn, $sql);
	}
	
	
}


//Get the image thumbnail url
function getImage($sku) {
	return "default.jpg";
}







/*Write the latest notification into a .json file*/

//Originally I decided to store lots of notifications in pding_notificatons and was going to create the
//notifications, mark them as sent, cleanse them occasionally etc. Later, it seemed more sensible to just
//constantly write a recent notification to a single json file and use this when clients request notification data

//The table may still be a useful construct later if users want some notifications and not others. 

//However, this next step just always gets the most-recently-created notification and turns it into
//a .json file which will be available to clients when they receive a GCM push notification in the 
//subsequent steps.

$sql = "SELECT * FROM pding_notifications ORDER BY notification_date DESC LIMIT 1";
$notification = mysqli_fetch_assoc(mysqli_query($conn, $sql));
//print_r($notification);

$file = fopen("latest-notification.json", "w") or die("Unable to open file!");

$txt = '{"title":"' . addslashes($notification['title']) . '", "message":"' . addslashes($notification['body']) . '", "actions":{"defaultAction":"' . addslashes($notification['action']) . '","action1":"' . addslashes($notification['action']) . '"}, "image":"' . addslashes($notification['image']) . '"}';
//echo $txt;

fwrite($file, $txt);
fclose($file);




?>