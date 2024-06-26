<?php
require_once  ("php/functions.php");
userCheckPrivilege(2);
$year = isset($_POST['myID'])?intval($_POST['myID']):getCurrentSOYear();
$studentID = getStudentID($_SESSION['userData']['userID']);
$studentIDWhere = "";
if($studentID)
{
	$studentIDWhere ="AND `student`.`studentID` != $studentID";
}

//get all the tournaments that the student has competed in
function getStudentsTournamentList($studentID)
{
	global $mysqlConn, $schoolID;
	$query = "SELECT `tournament`.`tournamentID`,`teamName`,`tournamentName`,`dateTournament` FROM `student` INNER JOIN `teammate` ON `student`.`studentID`=`teammate`.`studentID` INNER JOIN `team` ON `teammate`.`teamID`=`team`.`teamID` INNER JOIN `tournament` ON `team`.`tournamentID`=`tournament`.`tournamentID`  WHERE `student`.`schoolID`=$schoolID AND `student`.`studentID`=$studentID ORDER BY `dateTournament` DESC, `teamName`";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	$output ="<div id='tournamentDiv'><label for='tournament'>Tournaments</label> ";
	$output .="<select class='form-select' id='tournament' name='tournament' required>";
	if($result && mysqli_num_rows($result)>0)
	{
		while ($row = $result->fetch_assoc()):
			$output .= "<option value='".$row['tournamentID']."'>" . $row['tournamentName'] . " - ". $row['teamName'] ." (" . $row['dateTournament']  .")</option>";
		endwhile;
	}
	$output.="</select></div>";
	return $output;
}

function getAttendanceTypes()
{
	global $mysqlConn;
	//$myYear = isset($myYear) ? $myYear : getCurrentSOYear();
	$output = "<select class='form-select' id='meetingType' name='meetingType' onchange='showAttendanceTable()' required>";
	$query = "SELECT `meetingtype`.`meetingTypeName`, `meetingtype`.`meetingTypeID` FROM `meetingtype`";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed: $query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	// Add default blank option
	$output .= "<option disabled selected value='0'>Select an option</option>";
	while($row = $result->fetch_assoc())
	{
		$name = $row['meetingTypeName'];
		$id = $row['meetingTypeID'];
		// Event Meeting Attendance - Event Leaders
		if($id == 1 && userHasPrivilege(2))
		{
			$output .="<option value='$id' >$name";
			$output .="</option>";
		}
		// ID: 2 | General Meeting Attendance - Secretary and Captain
		// ID: 3 | Officer Meeting Attendance - Secretary and Captain
		// ID: 4 | Event Leader Attendance - Secretary and Captain
		else if(($id == 2 || $id == 3 || $id == 4) && (userHasPrivilege(3)))
		{
			$output .="<option value='$id' >$name";
			$output .="</option>";
		}
	}
	$output .="</select>";
	return $output;
}

//Repurposed function from eventemails.php - get names of all students on an event and creates attendance table
function getEventAttendanceTable($eventID)
{
	global $mysqlConn, $schoolID;
	$output = "";
	$year = getCurrentSOYear();
	$query = "SELECT DISTINCT `student`.`studentID`, `student`.`last`, `student`.`first`, `student`.`email`, `student`.`emailSchool`, `event`.`event` FROM `tournament` 
	INNER JOIN `tournamentevent` USING (`tournamentID`) 
	INNER JOIN `event` USING (`eventID`) 
	INNER JOIN `teammateplace` USING (`tournamenteventID`) 
	INNER JOIN `student` USING (`studentID`) 
	WHERE `student`.`active` = 1 AND `tournamentevent`.`eventID`= $eventID AND `tournament`.`notCompetition` = 1
	ORDER BY `student`.`last`,`student`.`first`";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	if($result)
	{
		$output.="<div>";
		while ($row = $result->fetch_assoc())
		{
			//show student name
			$formattedName = $row['first']." ".$row['last'];
			$studentID = $row['studentID'];	
			$output .= "<div>
				<h3>${formattedName}</h3>
				<p>Attendance: P = Present, AU = Absent Unexcused, AE = Absent Excused (Contacted you with a reason before meeting / Absent from school)</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-P' value='1' checked>
				<label class='form-check-label' for='attendance-${studentID}-P'>P</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AU' value='0'>
				<label class='form-check-label' for='attendance-${studentID}-AU'>AU</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AE' value='-1'>
				<label class='form-check-label' for='attendance-${studentID}-AE'>AE</label>
			</div>
			<p>Engagement: 0 for not engaged, 1 for partially engaged, 2 for fully participated</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='engagement-${studentID}' id='engagement-${studentID}-0' value='0'>
				<label class='form-check-label' for='engagement-${studentID}-0'>0</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='engagement-${studentID}' id='engagement-${studentID}-1' value='1'>
				<label class='form-check-label' for='engagement-${studentID}-1'>1</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='engagement-${studentID}' id='engagement-${studentID}-2' value='2' checked>
				<label class='form-check-label' for='engagement-${studentID}-2'>2</label>
			</div>				
	
			<p>Homework: 0 for Not Submitted or No Homework, 1 for partially incomplete, 2 for fully complete</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='homework-${studentID}' id='homework-${studentID}-0' value='0' checked>
				<label class='form-check-label' for='homework-${studentID}-0'>0</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='homework-${studentID}'' id='homework-${studentID}-1' value='1'>
				<label class='form-check-label' for='homework-${studentID}-1'>1</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='homework-${studentID}' id='homework-${studentID}-2' value='2'>
				<label class='form-check-label' for='homework-${studentID}-2'>2</label>
			</div>	
			</div>
			<hr>";
			}
	}
	$output .= "</div>";
	return $output;
}
function getGeneralAttendanceTable()
{
	global $mysqlConn, $schoolID;
	$output = "";
	$year = getCurrentSOYear();
	$query = "SELECT * FROM `student` 
	WHERE `student`.`active` = 1 AND `schoolID` = ". $_SESSION['userData']['schoolID']. " ORDER BY `last`,`first` ASC";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	if($result)
	{
		$output.="<div>";
		while ($row = $result->fetch_assoc())
		{
			//show student name
			$formattedName = $row['first']." ".$row['last'];
			$studentID = $row['studentID'];	
			$output .= "<div>
				<h3>${formattedName}</h3>
				<p>Attendance: P = Present, AU = Absent Unexcused, AE = Absent Excused (Contacted you with a reason before meeting / Absent from school)</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-P' value='1' checked>
				<label class='form-check-label' for='attendance-${studentID}-P'>P</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AU' value='0'>
				<label class='form-check-label' for='attendance-${studentID}-AU'>AU</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AE' value='-1'>
				<label class='form-check-label' for='attendance-${studentID}-AE'>AE</label>
			</div>
			</div>
			<hr>";
			}
	}
	$output .= "</div>";
	return $output;
}
function getOfficerAttendanceTable()
{
	global $mysqlConn, $schoolID;
	$output = "";
	$year = getCurrentSOYear();
	$query = "SELECT DISTINCT `student`.`studentID`, `student`.`last`, `student`.`first`, `student`.`email`, `student`.`emailSchool`, `officer`.`position`
	FROM `officer` 
	INNER JOIN `student` USING (`studentID`) 
	WHERE `student`.`active` = 1 AND `officer`.`year` = $year
	ORDER BY `officer`.`officerID` ASC";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	if($result)
	{
		$output.="<div>";
		while ($row = $result->fetch_assoc())
		{
			//show student name
			$formattedName = $row['first']." ".$row['last']." - ".$row['position'];
			$studentID = $row['studentID'];	
			$output .= "<div>
				<h3>${formattedName}</h3>
				<p>Attendance: P = Present, AU = Absent Unexcused, AE = Absent Excused (Contacted you with a reason before meeting / Absent from school)</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-P' value='1' checked>
				<label class='form-check-label' for='attendance-${studentID}-P'>P</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AU' value='0'>
				<label class='form-check-label' for='attendance-${studentID}-AU'>AU</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AE' value='-1'>
				<label class='form-check-label' for='attendance-${studentID}-AE'>AE</label>
			</div>
			</div>
			<hr>";
			}
	}
	$output .= "</div>";
	return $output;
}
function getEventLeaderAttendanceTable()
{
	global $mysqlConn, $schoolID;
	$output = "";
	$year = getCurrentSOYear();
	$query = "SELECT DISTINCT `student`.`studentID`, `student`.`last`, `student`.`first`, `student`.`email`, `student`.`emailSchool`, `event`.`event`
	FROM `eventleader` 
	INNER JOIN `student` USING (`studentID`) 
	INNER JOIN `event` USING(`eventID`)
	WHERE `student`.`active` = 1 AND `eventleader`.`year` = $year
	ORDER BY `event`.`event` ASC";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	if($result)
	{
		$output.="<div>";
		while ($row = $result->fetch_assoc())
		{
			//show student name
			$formattedName = $row['first']." ".$row['last']." - ".$row['event'];
			$studentID = $row['studentID'];	
			$output .= "<div>
				<h3>${formattedName}</h3>
				<p>Attendance: P = Present, AU = Absent Unexcused, AE = Absent Excused (Contacted you with a reason before meeting / Absent from school)</p>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-P' value='1' checked>
				<label class='form-check-label' for='attendance-${studentID}-P'>P</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AU' value='0'>
				<label class='form-check-label' for='attendance-${studentID}-AU'>AU</label>
			</div>
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' name='attendance-${studentID}' id='attendance-${studentID}-AE' value='-1'>
				<label class='form-check-label' for='attendance-${studentID}-AE'>AE</label>
			</div>
			</div>
			<hr>";
			}
	}
	$output .= "</div>";
	return $output;
}
function getEventLeadingID($studentID)
{
	global $mysqlConn;
	$year = getCurrentSOYear();
	$query = "SELECT `eventleader`.`eventID` FROM `eventleader` INNER JOIN `student` ON `eventleader`.`studentID` = `student`.`studentID` WHERE `student`.`studentID` = $studentID AND `year` = $year";
	$result = $mysqlConn->query($query) or error_log("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
	
	if($result)
	{
		$row = $result->fetch_assoc();
		return $row['eventID'];
	}
	return "";
}

$eventID = getEventLeadingID($studentID);
$event = getEventLeaderPosition($studentID);
$attendanceTableHTML = json_encode(getEventAttendanceTable($eventID));
$officerAttendanceTableHTML = json_encode(getOfficerAttendanceTable());
$generalAttendanceTableHTML = json_encode(getGeneralAttendanceTable());
$eventLeaderAttendanceTableHTML = json_encode(getEventLeaderAttendanceTable());
$date = date('Y-m-d');
$row = NULL; 
?>

<form id="addTo" method="post" action="javascript:addToSubmit('attendanceadd.php')">

	<label for="meetingType">Meeting Type</label>
	<?=getAttendanceTypes()?>

	<label hidden id="meetingNameLabel" for="meetingName">Event Name</label>
	<br>
	<p hidden id="meetingNameDisplay" value="<?=$eventID?>" ><u><?=$event?></u></p>
	<input id="meetingName" name="meetingName" class="form-control" type="text" value="<?=$eventID?>" hidden>

	<label for="meetingDate">Meeting Date</label>
	<br>
	<p value="<?=$date?>" ><u><?=$date?></u></p>
	<input id="meetingDate" name="meetingDate" type="date" value="<?=$date?>" hidden>

	<p>
		<label for="meetingTimeIn">Meeting Time In</label>
		<input id="meetingTimeIn" name="meetingTimeIn" type="time" required>
	</p>
	<p>
		<label for="meetingTimeOut">Meeting Time Out</label>
		<input id="meetingTimeOut" name="meetingTimeOut" type="time" required>
	</p>

	<div id="attendanceContainer"></div>

	<?=getAllStudents(1, $row['studentID'])?>
	<button class="btn btn-warning" type="button" onclick="javascript:attendanceAddStudent()"><span class='bi bi-plus-circle'> Add Student</button>
	
	<br>
	<label for="desc">Meeting Description</label>
	<textarea id="desc" name="desc" class="form-control" type="text"></textarea>

	<label for="meetingHW">Meeting Homework</label>
	<textarea id="meetingHW" name="meetingHW" class="form-control" type="text"></textarea>

	<button class='btn btn-primary' type="submit">Submit</button>
</form>
<p>
	<button class='btn btn-outline-secondary' onclick='window.history.back()' type='button'><span class='bi bi-arrow-left-circle'></span> Return</button>
</p>
<script defer>
	function removeAttribute() {
		var selectElement = document.getElementById('studentID');
		if (selectElement) {
			selectElement.removeAttribute('required');
		}
	}
	window.onload = removeAttribute();

	var form = document.getElementById('addTo');
	form.addEventListener('submit', function() {
		if(confirm('Are you sure you want to submit your meeting attendance?'))
		{
			alert('Meeting attendance submitted!');
			var eventID = document.getElementById("meetingName").value;
			if(document.getElementById('meetingType').value == 1)
			{
				window.location.href = `#event-analysis-${eventID}`;
			} else {
				window.location.href = `#leaders`;
			}
		}
		else {
			event.preventDefault();
		}
	}, false);

	//Adds an additional student to the meeting attendance page
	function attendanceAddStudent() {
		var studentID = document.getElementById("studentID").value;
		var selectedName = document.getElementById("studentID").options[document.getElementById("studentID").selectedIndex].text;
		var firstLast = selectedName.split(', ');
		var formattedName = firstLast[1] + ' ' + firstLast[0];

		if(studentID.length === 0)
		{
			alert("If you would like to add a student, please select a student to add to the event attendance list.");
			return;
		}
		//check if the new student was already added before
		if(document.getElementsByName('attendance-').length > 0) 
		{
			alert('Student already exists in this meeting!');
			return;
		}
		else
		{
			//create a new div with student information
			if(confirm("Add student: " + formattedName + "?"))
			{
				var newStudent = `<div>
						<h3>${formattedName} (Extra)</h3>
						<p>Attendance: P = Present, AU = Absent Unexcused, AE = Absent Excused (Contacted you with a reason before meeting / Absent from school)</p>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="attendance-${studentID}" id="attendance-${studentID}-P" value="1" checked>
						<label class="form-check-label" for="attendance-${studentID}-P">P</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="attendance-${studentID}" id="attendance-${studentID}-AU" value="0">
						<label class="form-check-label" for="attendance-${studentID}-AU">AU</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="attendance-${studentID}" id="attendance-${studentID}-AE" value="-1">
						<label class="form-check-label" for="attendance-${studentID}-AE">AE</label>
					</div>
					<p>Engagement: 0 for not engaged, 1 for partially engaged, 2 for fully participated</p>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="engagement-${studentID}" id="engagement-${studentID}-0" value="0">
						<label class="form-check-label" for="engagement-${studentID}-0">0</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="engagement-${studentID}" id="engagement-${studentID}-1" value="1">
						<label class="form-check-label" for="engagement-${studentID}-1">1</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="engagement-${studentID}" id="engagement-${studentID}-2" value="2" checked>
						<label class="form-check-label" for="engagement-${studentID}-2">2</label>
					</div>				

					<p>Homework: 0 for Not Submitted or No Homework, 1 for partially incomplete, 2 for fully complete</p>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="homework-${studentID}" id="homework-${studentID}-0" value="0" checked>
						<label class="form-check-label" for="homework-${studentID}-0">0</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="homework-${studentID}" id="homework-${studentID}-1" value="1">
						<label class="form-check-label" for="homework-${studentID}-1">1</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="homework-${studentID}" id="homework-${studentID}-2" value="2">
						<label class="form-check-label" for="homework-${studentID}-2">2</label>
					</div>	

					<hr>`;
				document.getElementById("studentID").insertAdjacentHTML('beforebegin', newStudent);
			}
		}
	}

	function showAttendanceTable() {
		var eventAttendanceTable = <?=$attendanceTableHTML?>;
		var generalAttendanceTable = <?=$generalAttendanceTableHTML?>;
		var officerAttendanceTable = <?=$officerAttendanceTableHTML?>;
		var eventLeaderAttendanceTable = <?=$eventLeaderAttendanceTableHTML?>;
		var eventID = '<?=$eventID?>';
		var meetingType = document.getElementById('meetingType').value;
		if(meetingType == 1)
		{
			// show event attendance table with all students in event
			document.getElementById('attendanceContainer').innerHTML = eventAttendanceTable;
			document.getElementById('meetingNameDisplay').hidden = false;
			document.getElementById('meetingNameLabel').hidden = false;
		}
		// General Meeting
		if(meetingType == 2)
		{
			document.getElementById('attendanceContainer').innerHTML = generalAttendanceTable;
			document.getElementById('meetingNameDisplay').hidden = true;
			document.getElementById('meetingNameLabel').hidden = true;
			document.getElementById('meetingName').value = 0;
		}
		// Officer Meeting
		if(meetingType == 3)
		{
			document.getElementById('attendanceContainer').innerHTML = officerAttendanceTable;
			document.getElementById('meetingNameDisplay').hidden = true;
			document.getElementById('meetingNameLabel').hidden = true;
			document.getElementById('meetingName').value = 0;
		}
		// Event Leader Meeting
		if(meetingType == 4)
		{
			document.getElementById('attendanceContainer').innerHTML = eventLeaderAttendanceTable;
			document.getElementById('meetingNameDisplay').hidden = true;
			document.getElementById('meetingNameLabel').hidden = true;
			document.getElementById('meetingName').value = 0;
		}
	}
</script>
