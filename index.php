<?php
include_once('total.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>IoT Middleware Application</title>

	<!-- JQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>

	<!-- Datatables -->
	<style src="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"></style>
	<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

	<!-- Local CSS -->
	<link rel="stylesheet" href="style.css">

	<script type="text/javascript">
		var discoverTable = null;
		var connectedTable = null;

		jQuery(document).ready(function($) {
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
			});

			// Instantiate DataTables
			discoverTable = $('#discover').DataTable({
				searching: false,
				paging: false,
				info: false
			});
			connectedTable = $('#connected').DataTable({
				searching: false,
				paging: false,
				info: false
			});
			// !End Instantiating DataTables
		});

		var discoveryCheck = null;
		var discoveryCheckCount = 0;
		function start_discovery() {
			discoveryCheck = setInterval(check_discovery, 1000);
		}
		
		function check_discovery() {
			discoveryCheckCount++;
			if (discoveryCheckCount == 120)
				clearInterval(discoveryCheck);
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task': 'get_enroll'},
				success: function(response){
					if (response.length > 1) {
						clearInterval(discoveryCheck);
						discoverTable.rows().remove().draw();

						devices = JSON.parse(response);
						devices.devices.forEach(function(element) {
							var rowNode = discoverTable.row.add([
								element.productName,
								element.vendor,
								element.deviceId,
								'<input type="textbox" name="accCodes['+element.deviceId+']" />'
							]).draw(false).node();
						});

						$("#SendAccessory").show();
					}
				},
				error: function() {},
			});
		}

		function send_accessory_codes() {
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task' : 'send_accessoryValidation', 'accCodes' : $('#frmDiscover').serializeArray() },
				success: function(response){
					start_accessory_check();
				},
				error: function() {},
			});
		}

		var accessoryCheck = null;
		var accCheckCount = 0;
		function start_accessory_check() {
			accessoryCheck = setInterval(check_accessory, 1000);
		}

		function check_accessory() {
			accCheckCount++;
			if (accCheckCount == 120)
				clearInterval(accessoryCheck);
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task': 'get_discoveryResults'},
				success: function(response){
					if (response.length > 1) {
						clearInterval(accessoryCheck);
					}
				},
				error: function() {},
			});
		}

		function send_generic_message(task) {
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task' : task },
				success: function(response){
					//
				},
				error: function() {},
			});
		}
	</script>
</head>
<body>
	<div id="container">
		<h1>IoT Middleware Application</h1>
		<h3>Discover Devices</h3>
		<form id="frmDiscover">
			<table id="discover">
				<thead>
					<tr>
						<th>Device</th>
						<th>Vendor</th>
						<th>Device ID</th>
						<th>Enter Accessory Code</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<button value="Discover" type="button" onclick="start_discovery();">Discover</button>
			<button id="SendAccessory" value="SendAccessory" type="button" style="display: none;" onclick="send_accessory_codes();">Send Accessory Codes</button>
		</form>

		<h3>Connected Devices</h3>
		<form id="frmDiscover">
			<table id="connected">
				<thead>
					<tr>
						<th>Device</th>
						<th>Status</th>
						<th>Firmware</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Netgear Router</td>
						<td><span class="success">Online</span></td>
						<td>3.2.14</td>
						<td><a href="#">Delete</a></td>
					</tr>
					<tr>
						<td>Smart Car</td>
						<td><span class="update">Firmware Update Available (9.2a)</span></td>
						<td>8.93b</td>
						<td><a href="#">Update</a>, <a href="#">Delete</a></td>
					</tr>
					<tr>
						<td>Emerson Television</td>
						<td><span class="error">Offline</span></td>
						<td>21.75</td>
						<td><a href="#">Delete</a></td>
					</tr>
				</tbody>
			</table>
			<button value="Update All" type="button" onclick="send_generic_message('start_applyUpdate');">Update All</button>
		</form>

		<h3>Settings</h3>
		<form id="frmSettings">
			<label for="Email">Email: <input type="text" name="Email" /></label>
			<button value="Save" type="Submit">Save</button>
		</form>
	</div>
</body>
</html>