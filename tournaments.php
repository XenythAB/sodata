<?php
require_once  ("../connectsodb.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="Pragma" content="no-cache">
	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/jquery.validate.min.js"></script>
	  <script type="text/javascript">

	$().ready(function() {
		$("#addTo").hide();
		$("#searchDiv").hide();
		//Load Students
		getList({});
			// validate signup form on keyup and submit
		$("#addTo").validate({
			rules: {
				first: "required",
				last: "required",
				yearGraduating: "required",
				email: {
					required: true,
					email: true
				},
			},
			messages: {
				first: "*Please enter the student\'s first name",
				last: "*Please enter the student\'s last name",
				yearGraduating: {
					required: "*Enter the year the student is graduating",
				},
				email: {
					required: "*Enter the student\'s email.",
				},
			},
			submitHandler: function(form) {
                form.submit();
            }
		});

		//when Find by Name is clicked, this initiates the search
		$("#findTournament").on( "submit", function( event ) {
  		event.preventDefault();
  		getList( $( this ).serialize() );
		});
			//Allow person to pick year
			for (i = new Date().getFullYear()+1; i > 1973; i--)
			{
			    $('#tournamentYear').append($('<option />').val(i).html(i));
			}
	});
	function getList(myData)
	{
		//alert(JSON.stringify(myData) );
		//myData is a json object type
		var request = $.ajax({
		 url: "tournamentslist.php",
		 cache: false,
		 method: "POST",
		 data: myData,
		 dataType: "html"
		});
		request.done(function( html ) {
		 //$("label[for='" + field + "']").append(html);
		 $("#list").html(html);
		});

		request.fail(function( jqXHR, textStatus ) {
		 $("#list").html("Search Error");
		});
	}

</script>
	</head>
	<body>
	<button onclick="$('#searchDiv').show();$(this).hide();">Search</button>
	<div id="searchDiv">
	<form id="findTournament">
		<fieldset>
			<legend>Find Tournament</legend>
			<p>
				<label for="tournamentName">Tournament Name</label>
				<input id="tournamentName" name="tournamentName" type="text">
			</p>
			<p>
				<label for="tournamentYear">Tournament Year</label>
				<select name="tournamentYear" id="tournamentYear"><option value="0">All Years</option></select> <span style="color=blue">This is the end of the school year that the tournament took place.  It may be the year after the tournament date.</span>
			</p>
			<p>
				<input class="submit" type="submit" value="Find Tournament">
			</p>
		</fieldset>
	</form>
</div>
	<button onclick="$('#addTo').show();$(this).hide();">Add</button>
	<form id="addTo" method="post" action="studentadd.php">
		<fieldset>
			<legend>Add Tournament</legend>
			<div>TODO: Change all Fields</div>
			<p>
				<label for="first">Firstname</label>
				<input id="first" name="first" type="text">
			</p>
			<p>
				<label for="last">Lastname</label>
				<input id="last" name="last" type="text">
			</p>
			<p>
				<label for="yearGraduating">Year Graduating</label>
				<input id="yearGraduating" name="yearGraduating" type="text">
			</p>
			<p>
				<label for="email">Email</label>
				<input id="email" name="email" type="email">
			</p>
			<p>
				<label for="emailAlt">Alternate Email</label>
				<input id="emailAlt" name="emailAlt" type="email">
			</p>
			<p>
				<label for="phoneType">Phone Type</label>
				<select id="phoneType" name="text">
					<?=$phoneTypes?>
				</select>
			</p>
			<p>
				<label for="phone">Phone</label>
				<input id="phone" name="phone" type="tel">
			</p>
			<fieldset>
				<legend>Parent 1</legend>
				<p>
					<label for="parent1First">First</label>
					<input id="parent1First" name="parent1First" type="text">
				</p>
				<p>
					<label for="parent1Last">Last</label>
					<input id="parent1Last" name="parent1Last" type="text">
				</p>
				<p>
					<label for="parent1Email">Email</label>
					<input id="parent1Email" name="parent1Email" type="email">
				</p>
				<p>
					<label for="parent1Phone">Phone</label>
					<input id="parent1Phone" name="parent1Phone" type="tel">
				</p>
			</fieldset>
			<p>
				<input class="submit" type="submit" value="Submit">
			</p>
		</fieldset>
	</form>

<div id="list"></div>

</body>
</html>
