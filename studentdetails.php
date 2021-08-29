<?php
require_once("../connectsodb.php");
require_once("checksession.php"); //Check to make sure user is logged in and has privileges
require_once("functions.php");
userCheckPrivilege(3);

$studentID = isset($_REQUEST['myID'])?intval($_REQUEST['myID']):0;
$query = "SELECT * FROM `student` WHERE `studentID` = $studentID";
$result = $mysqlConn->query($query) or print("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
if($result)
{
	$row = $result->fetch_assoc();

$output ="<div id='student-$studentID'>";
$output .="<h2>".$row['last'] . ", " . $row['first']."</h2>";
$officerPos = getOfficerPosition($mysqlConn,$studentID);
if($officerPos)
{
	$output .="<h3>$officerPos</h3>";
}

if(userHasPrivilege(3))
{
	$output .= "<div><a href='#student-edit-$studentID'>Edit</a> ";
	$output .= "<a href=\"javascript:studentRemove($studentID,'".$row['last'] . ", " . $row['first']."')\">Remove</a>";
	$output .= "</div>";

	if(empty($row['userID']))
	{
			$output .= "User has never logged in with registered account.";
	}
	else {
		$query = "SELECT * FROM `user` WHERE `userID`=".$row['userID'];// where `field` = $fieldId";
		$output .= $query;
		$resultPrivilege = $mysqlConn->query($query) or print("\n<br />Warning: query failed:$query. " . $mysqlConn->error. ". At file:". __FILE__ ." by " . $_SERVER['REMOTE_ADDR'] .".");
		$rowPriv = $resultPrivilege->fetch_assoc();
		if ($rowPriv['privilege'])
		{
			$output .= "<div>Privilege: ".$rowPriv['privilege']."</div>";
		}
		else
		{
			$output .= "User has never logged in with registered account.";
		}
	}
}
$grade = 9;
if (date("m")>5)
{
	$grade = 12-($row['yearGraduating']-date("Y")-1);
}
else
{
	$grade = 12-($row['yearGraduating']-date("Y"));
}
$output .="<div>Grade: $grade (".$row['yearGraduating'].")</div>";
if($row['email'])
{
	$output .="<div>Google Email: <a href='mailto: ".$row['email']."'>".$row['email']."</a></div>";
}
if($row['emailSchool'])
{
	$output .="<div>School Email: <a href='mailto: ".$row['emailSchool']."'>".$row['emailSchool']."</a></div>";
}
if($row['phone'])
{
	$output .="<div>Phone(".$row['phoneType']."): ".$row['phone']."</div>";
}
if(userHasPrivilege(3))
{
	$officerPosPrev = getPreviousOfficerPosition($mysqlConn,$studentID);
	if($officerPosPrev)
	{
		$output .="<div>Previous Positions: $officerPosPrev</div>";
	}
	if($row['parent1Last'])
	{
		$output .="<br><h3>Parent(s)</h3>";
		$output .="<div>".$row['parent1First']." ".$row['parent1Last'].", ".$row['parent1Email'].", ".$row['parent1Phone']."</div>";
		if($row['parent2Last'])
		{
			$output .="<div>".$row['parent2First']." ".$row['parent2Last'].", ".$row['parent2Email'].", ".$row['parent2Phone']."</div>";
		}
	}
	
	$output .=studentTournamentResults($mysqlConn, $row['studentID']);
	$output .=studentEventPriority($mysqlConn, $row['studentID']);
	$output .=studentCourseCompleted($mysqlConn, $studentID);
	$output .=studentCourseEnrolled($mysqlConn, $row['studentID']);
}


$output .="</div>";
}
echo $output;
?>
