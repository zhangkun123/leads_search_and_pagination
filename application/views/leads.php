<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Leads</title>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/styles.css">
	<script src="/assets/javascripts/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			//ajax function that handles for paginations generated from initial load, search by name and search by date range.
			$("#wrapper").on("click", '.page_number', function(){
				var page = $(this).attr('data-page');
				var search = $(this).attr('data-search');
				$.getJSON('/leads/get_leads', { page_number: page, search: search }, function(data){
					$("tbody").html(data.html);
				}, "json");
			});
			$("#wrapper").on("submit", "#search", function(){
				var form = $(this);
				$.post(form.attr('action'), form.serialize(), function(data){
					$("tbody").html(data.html);
					$(".btn-group").html(data.pagination);
				}, "json");
				return false;
			});
		});
	</script>
</head>
<body>
	<div id="wrapper">
		<form action="/leads/get_leads" method="post" id="search">
			<label for="name">Name: </label>
			<input type="text" name="first_name"> <br>
		</form>
		<form action="/leads/get_leads" method="post" id="search">
			From: <input name="from" type="text" placeholder="mm/dd/yyyy">
			To: <input name="to" type="text" placeholder="mm/dd/yyyy">
			<input type="submit" value="Search">
		</form>
		<div class="btn-toolbar">
			<div class="btn-group">
<?php 		foreach (range(1, $pages) as $page) 
			{ ?>
				<button data-search="" class='page_number' data-page='<?= $page; ?>'><?= $page; ?></button>
<?php 		} ?>
			</div>
		</div>
		<table class="table table-hovered">
			<thead>
				<tr>
					<th>Leads ID</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th>Registered Datetime</th>
				</tr>
			</thead>
			<tbody>
<?php 		foreach($leads as $lead)
			{ ?>
				<tr>
					<td><?= $lead['leads_id']; ?></td>
					<td><?= $lead['first_name']; ?></td>
					<td><?= $lead['last_name']; ?></td>
					<td><?= $lead['email']; ?></td>
					<td><?= date('M d, Y', strtotime($lead['registered_datetime'])); ?></td>
				</tr>
<?php 		} ?>
			</tbody>
		</table>
	</div>
</body>
</html>