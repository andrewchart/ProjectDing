<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

		<title>GA Realtime API Test</title>
		
		<link href='https://fonts.googleapis.com/css?family=Cabin:400,500,600italic,600,800' rel='stylesheet' type='text/css'>
		<link href='styles.css' rel='stylesheet' type='text/css'>
		
	</head>
	<body>
		<header>
			<h1>Project Ding! - GA Realtime API Test</h1>
		</header>	

		<section id="content">
		
			<h2>Service Version</h2>
			
			<p><code>service-version-scripts/get_transactions.php</code> Run on a 2 minute cron job to populate the DB with transaction data</p>
			<p><code>service-version-scripts/process_notifications.php</code> Run on a 2 minute cron job to do something with any unprocessed transactions</p>
			
			<?php include_once('service-version-scripts/get_transactions.php'); ?>
			<?php
				echo "<pre>"; print_r($results); echo "</pre>";
				
				
				/* Manage user's subscription to PUSH notifications */
				
				
			?>
		
		</section>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	</body>
</html>