<?php

/*
 * About Me:
 * RUN ME ON A REGULAR CRON JOB TO ...
 *
 *  - QUERY THE GOOGLE ANALYTICS API FOR
 *    ECOMMERCE TRANSACTION DATA
 *
 *  - POPULATE THE TRANSACTIONS TABLE WITH
 *    ALL POSSIBLE TRANSACTION PRODUCT LINES
 *
 *  - PROCESS THE TRANSACTIONS TABLE DATA AND
 *    GENERATE TURN IT INTO DATA FOR SINGLE-ORDER
 *    OR AMALGAMATED-ORDERS PUSH NOTIFICATIONS IN
 *    THE PUSH NOTIFICATION TABLE
 *
 *	- LOOP THROUGH THE PUSH NOTIFICATIONS TABLE
 *    SENDING NOTIFICATIONS TO SUBSCRIBED USERS
 *
**/


/* Include credentials */
$credentials = json_decode(file_get_contents('private/credentials.json'));

/* Establish Database connection */
require_once('scripts/database_connection.php');

/* Ensure Required Database Tables Are Created */
require_once('scripts/create_tables.php');

/* Query the GA API and write values to the pding_transactions table */
require_once('scripts/get_transaction_data.php');
exit();
/* Turn data from pding_transactions into amalgamated push notifications */
// (and flag transaction lines in pding_transactions as 'queued'
require_once('scripts/create_notifications_from_transactions.php');


/* Send the push notifications in pding_notifications */
// (and remove them from the table so they are only sent once)
require_once('scripts/send_push_notifications.php');



?>
