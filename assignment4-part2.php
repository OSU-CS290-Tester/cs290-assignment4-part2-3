<!DOCTYPE html>
<html>
	<body>
	<h3>Video Inventory</h3>
	<form action="assignment4-part2.php" method="POST">
		<fieldset>
		<legend>Add Video</legend>
			<p>Name: <input type="text" name="videoName" ></p>
			<p>Category:<input type="text" name="videoCategory" > </p>
			<p>Length: <input type="number" name="length" min=0></p>
			<input type="submit" value="submit" name="addVideo">
		</fieldset>
		</form>
	</body>
</html>

<?php
ini_set('display_errors', 'On');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} 

/* if($mysqli->query("CREATE TABLE video_inventory(id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL UNIQUE, category VARCHAR(255), length INT UNSIGNED, rented BOOL NOT NULL DEFAULT FALSE)") === TRUE) {
	printf("Table created.\n");
} */

//delete video
if(isset($_POST['deleteVideo'])){	
	if (!($stmt = $mysqli->prepare("DELETE FROM video_inventory WHERE name=(?)"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param("s", $_POST['title'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
}

//update availability
if(isset($_POST['availabilityUpdate'])){
	if(isset($_POST['checkInOut'])) {
		if(!($stmt = $mysqli->prepare("UPDATE video_inventory SET rented = (?) WHERE name=(?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if($_POST['checkInOut'] == 'in') {
			$inOut = false;
		} else if ($_POST['checkInOut'] == 'out') {
			$inOut = true;
		}
		if (!$stmt->bind_param("is", $inOut, $_POST['checkInOutItem'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		$stmt->close();
	}
}

//add video
if(isset($_POST['addVideo'])){
	if(empty($_POST['videoName'])){
		echo "Video name can not be empty <br>";
	} else {
		if (!($stmt = $mysqli->prepare("SELECT name FROM video_inventory"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		$out_name=NULL;
		$sameName=false;
		if (!$stmt->bind_result($out_name)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		while ($stmt->fetch()){
			if($out_name == $_POST['videoName']){
				echo  $_POST['videoName'] . " is already in the list<br>";
				$sameName=true;
				break;
			}
		}
		$stmt->close();
		if(!$sameName){
			$name = $_POST['videoName'];
		}
	}
	if (empty($_POST['videoCategory'])){
		$cat="n/a";
	} else {
		$cat=$_POST['videoCategory'];
	}
	if($_POST['length'] <= 0){
		echo "The length has to be positive integer<br>";
	} else {
		$leng=$_POST['length'];
	}
	if(isset($name, $cat, $leng)){
		if (!($stmt = $mysqli->prepare("INSERT INTO video_inventory (name, category, length) VALUES (?, ?, ?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("ssi", $name, $cat, $leng)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$stmt->close();
	}
}

//Display by category
if(isset($_POST['displayByCat'])){
	if ($_POST['choice'] == 'allMovie') {
		if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM video_inventory"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} 
	} else {
		if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM video_inventory WHERE category=(?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("s", $_POST['choice'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
	}
}

//delete all videos
if(isset($_POST['deleteAllVideo'])){
	if (!($stmt = $mysqli->prepare("DELETE FROM video_inventory"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->close();
	echo '<h3>' . "Video List" . '</h3>';
	echo '<table border="1">';
	echo '<tr><td>Name</td><td>Category</td><td>Length</td><td>Availability</td><td></td><td></td></tr>';
	echo '</table>';
} else {
	//display list
	if(!isset($_POST['displayByCat']) && !isset($_POST['deleteAllVideo'])){
		if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM video_inventory"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$out_name    = NULL;
	$out_cat = NULL;
	$out_leng =NULL;
	$out_rented= NULL;
	if (!$stmt->bind_result($out_name, $out_cat, $out_leng, $out_rented)) {
		echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	echo '<h3>' . "Video List" . '</h3>';
	echo '<table border="1">';
	echo '<tr><td>Name</td><td>Category</td><td>Length</td><td>Availability</td><td></td><td></td></tr>';
	while ($stmt->fetch()) {
		if ($out_rented == 'false') {
			$out_rented = 'Available';
		}else {
			$out_rented = 'Checked out';
		}
		echo '<tr><td>' . $out_name . '<td>' . $out_cat . '<td>' . $out_leng . '<td>' . $out_rented;
		echo '<td><form method="POST" action="assignment4-part2.php">';
		echo "Check-" . "in" . '<input type="radio" name="checkInOut" value=' . "in" . '>';
		echo "out" . '<input type="radio" name="checkInOut" value=' . "out" . '>';
		echo '<input type="hidden" name="checkInOutItem" value=' . $out_name . '>';
		echo '<input type="submit" value="Update" name=' . "availabilityUpdate" . '></form></td>';
		echo '<td><form method="POST" action="assignment4-part2.php">';
		echo '<input type="hidden" name="title" value=' . $out_name;
		echo '><input type="submit" value="Delete Video" name=' . "deleteVideo" . '></form></td>';
	}
	 echo '</table>';
	$stmt->close();
}
//display category
if (!($stmt = $mysqli->prepare("SELECT DISTINCT category FROM video_inventory"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
$out_category=NULL;
if (!$stmt->bind_result($out_category)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
echo '<h3>' . "Display Videos by Category " . '</h3>';
echo '<form method="POST" action="assignment4-part2.php">';
echo '<select name=' . "choice" . '>';
while ($stmt->fetch()){
	echo '<option value=' . $out_category . '>' . $out_category . '</option>';
}
echo '<option selected value=' . "allMovie" . '>' . "all movies" . '</option>';
echo '<input type="submit" value="submit" name=' . "displayByCat" . '>';
echo '</select></form>';

//delete button
echo '<form method="POST" action="assignment4-part2.php">';
echo '<br>';
echo '<input type="submit" value="Delete All Videos" name=' . "deleteAllVideo" . '>';
echo '</form>';

?>
