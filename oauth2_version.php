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
			
			<nav id="menu">
				<button id="authorise" hidden>Authorise API</button>
			</nav>
		</header>	

		<section id="content">
		
			<h2>Oauth2 version</h2>
		
			<div id="query">
				<button id="getCurrentActiveUsers">Get Current Active Users</button>
				<label>Specify a page: <input id="page" value="" type="text" /></label>
				<span>Leave blank for all pages</span>
			</div>
			
		
			<div id="result">
				<span id="number"></span>
				<span id="message1"></span>
				<span id="message2"></span>
			</div>
		
		</section>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script>
		
			//Realtime API version 3
			var API_BASE = "https://www.googleapis.com/analytics/v3";
			var API_KEY = "<?php include_once('private/client_api_key.txt'); ?>";
			
			//Analytics property AMC UA Main UA-7172785-6
			var GA_VIEW_ID = "<?php include_once('private/ga_view_id.txt') ?>";
			
		</script>
		<script src="oauth2_version.js"></script>
		<script src="https://apis.google.com/js/client.js?onload=authorise"></script>
	</body>
</html>