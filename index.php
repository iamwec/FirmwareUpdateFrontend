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
		var devices = null;

		jQuery(document).ready(function($) {
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
			});

			// Instantiate DataTables
			discoverTable = $('#discover').DataTable({
				searching: false,
				paging: false,
				info: false,
				language: {
					emptyTable: "Click Discover button to start discovery process"
				}
			});
			connectedTable = $('#devices').DataTable({
				searching: false,
				paging: false,
				info: false
			});
			// !End Instantiating DataTables
		});

		var discoveryCheck = null;
		var discoveryCheckCount = 0;
		function start_discovery() {
			send_generic_message('start_enrollment');
			discoveryCheckCount = 0;
			discoveryCheck = setInterval(check_discovery, 1000);
		}
		
		function check_discovery() {
			discoveryCheckCount++;
			if (discoveryCheckCount == 120) {
				alert("Discovery Failed: No response from server");
				clearInterval(discoveryCheck);
				return;
			}
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

						$("#btnSendAccessory").show();
						$("#btnDiscover").hide();
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
			accCheckCount = 0;
			accessoryCheck = setInterval(check_accessory, 1000);
		}

		function check_accessory() {
			accCheckCount++;
			if (accCheckCount == 120) {
				alert("Accessory Check Failed: No response from server");
				clearInterval(accessoryCheck);
				return;
			}
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task': 'get_discoveryResults'},
				success: function(response){
					if (response.length > 1) {
						clearInterval(accessoryCheck);
						discoverTable.rows().remove().draw();
						$('#DiscoverDiv').hide();
						$('#DevicesDiv').show();
						connectedTable.rows().remove().draw();

						response = JSON.parse(response);

						response.forEach(function(element) {
							var rowNode = connectedTable.row.add([
								element.productName,
								element.vendor,
								element.status,
								'<button type="button" value="'+element.deviceId+'" name="Delete" onclick="delete_device(\''+element.deviceId+'\')">Delete</button>'
							]).draw(false).node();
							$(rowNode).attr('id', element.deviceId);
						});
					}
				},
				error: function() {},
			});
		}

		function delete_device(deviceID) {
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task' : 'delete', 'deviceID' : deviceID },
				success: function(response){
					connectedTable.row($(this).parents('tr')).remove().draw();
				},
				error: function() {},
			});
			connectedTable.row($('#'+deviceID)).remove().draw();
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
		<div id="DiscoverDiv">
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
				<button id="btnDiscover" value="Discover" type="button" onclick="start_discovery();">Discover</button>
				<button id="btnSendAccessory" value="SendAccessory" type="button" style="display: none;" onclick="send_accessory_codes();">Send Accessory Codes</button>
			</form>
		</div>

		<div id="DevicesDiv" style="display:none;">
			<h3>Devices</h3>
			<form id="frmDiscover">
				<table id="devices">
					<thead>
						<tr>
							<th>Device</th>
							<th>Vendor</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<button value="Search for Updates" type="button" onclick="send_generic_message('start_searchForUpdates');">Search for Updates</button>
				<button value="Update All" type="button" onclick="send_generic_message('start_applyUpdate');">Update All</button>
			</form>
		</div>

		<div id="AdministrationDiv">
			<h3>Administration</h3>
			<form id="frmSettings">
				<button value="Reset" type="button" onclick="send_generic_message('resetApp'); location.reload();">Reset App</button>
				<button value="Kill" type="button" onclick="send_generic_message('kill');">Kill App</button>
			</form>
		</div>
	</div>
</body>
</html>