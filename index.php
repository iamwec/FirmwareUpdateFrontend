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
		jQuery(document).ready(function($) {
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
			});

			// Instantiate DataTables
			$('#discover').DataTable({
				searching: false,
				paging: false,
				info: false
			});
			$('#connected').DataTable({
				searching: false,
				paging: false,
				info: false
			});
			// !End Instantiating DataTables
		});

		function start_discovery() {

		}
		
		function check_discovery() {
			$.ajax({
				type: "POST",
				url: "handler.php",
				data: {'Task': 'get'},
				success: function(response){
					alert(response);
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
						<th>Device ID</th>
					</tr>
				</thead>
				<tbody>
					<tr class="clickable-row" data-href="#">
						<td>Philip's Hue Lightbulb</td>
						<td>51651832</td>
					</tr>
					<tr class="clickable-row" data-href="#">
						<td>Samsung Television</td>
						<td>15231651</td>
					</tr>
				</tbody>
			</table>
			<button value="Discover" type="Submit">Discover</button>
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
			<button value="Update All" type="Submit">Update All</button>
		</form>

		<h3>Settings</h3>
		<form id="frmSettings">
			<label for="Email">Email: <input type="text" name="Email" /></label>
			<button value="Save" type="Submit">Save</button>
		</form>
	</div>
</body>
</html>