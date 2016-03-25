<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

		<title>GA Realtime API Test</title>
		
		<link href='https://fonts.googleapis.com/css?family=Cabin:400,500,600italic,600,800' rel='stylesheet' type='text/css'>
		<link href='styles.css' rel='stylesheet' type='text/css'>
		<link rel="manifest" href="manifest.json">
		
	</head>
	<body>
		<header>
			<h1>Project Ding! - GA Realtime API Test</h1>
		</header>	

		<section id="content">
		
			<h2>Service Version</h2>
			
			<p><code>start_push_notification_cycle.php</code> Run on a 2 minute cron job to populate the DB with transaction data, create amalgamated push notifications, and send the notifications to subscribed users via a web service.</p>
			
			<?php
				
			
				/* Manage user's subscription to PUSH notifications */
				
				
			?>
		
			<section id="register-for-notifications">
			
				<h4>Register for push notifications</h4>					
				<button id="register" class="unregistered">Subscribe to notifications</button>
				
				<p id="errorMsg" class="error"></p>
							
			</section>

		
		</section>
		
		
		<script src="sw-controller.js"></script>
	</body>
</html>