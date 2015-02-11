<!DOCTYPE html>
<html>
<body>


<form action="assignment4-part2.php" method="POST">
<fieldset>
<legend>Add Video</legend>
<p>Name: <input type="text" name="videoName" ></p>
<p>Category: <select name="videoCateg[]">
<option value="none">none</option>
<option value="action">action</option>
<option value="comedy">comedy</option>
<option value="drama">drama</option></select></p>
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
} else {
	echo "Connection works!<br>";
}

if(isset($_POST['input'])){
	echo 'name is set';
	echo $_POST['input'];
}else{
	echo 'name is not set';
}
	


/* if($mysqli->query("CREATE TABLE video_inventory(id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL UNIQUE, category VARCHAR(255), length INT UNSIGNED, rented BOOL NOT NULL DEFAULT FALSE)") === TRUE) {
	printf("Table created.\n");
} */

if (!($stmt = $mysqli->prepare("INSERT INTO video_inventory (name, category, length) VALUES (?, ?, ?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if(isset($_POST['addVideo'])){
	if(empty($_POST['videoName'])){
		echo "Video name can not be empty <br>";
		//echo ('<script type="text/javascript">alert ("video name is empty");</script>');
	} else {
		$name=$_POST['videoName'];
	}
	if($_POST['videoCateg'][0] == 'none'){
		echo "You have to choose a category<br>";
	} else {
		$cat=$_POST['videoCateg'];
		//echo $cat[0];
	}
	if($_POST['length'] <= 0){
		echo "The length has to be positive integer<br>";
	} else {
		$leng=$_POST['length'];
	}
	if(isset($name, $cat, $leng)){
		if (!$stmt->bind_param("ssi", $name, $cat[0], $leng)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
		echo "all set";
	}else {
		echo "something is not right";
	}
}

if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM video_inventory"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

/* while ($stmt->fetch()) {
	if ($out_rented == 'false') {
		$out_rented = 'Available';
	}else {
		$out_rented = 'Checked out';
	}
	echo "<form method='post' action='assignment4-part2.php'>
	<table><tr>
	<td>$out_name</td><td>$out_cat</td><td>$out_leng</td><td>$out_rented</td>
	</tr>
	</table>
	</form>";
} */
  echo '<table border="1"><tr>
  <td>Name</td><td>Category</td><td>Length</td><td>Availability</td><td></td></tr>';
	while ($stmt->fetch()) {
	if ($out_rented == 'false') {
		$out_rented = 'Available';
	}else {
		$out_rented = 'Checked out';
	}
		echo '<tr>';
		echo '<td>' . $out_name;
		echo '<td>' . $out_cat . '<td>' . $out_leng . '<td>' . $out_rented;
		echo '<td>';
		echo '<form method="POST" action="assignment4-part2.php" name=$out_name>';
echo '<input type="hidden" name="input" value=' . $out_name;
echo '>';
		echo '<input type="submit" value="Delete"></form>';
		echo '</td>';
	}
  
  echo '</table>';
  
	
	//$out_rented = "Available";
	//printf("name = %s, category = %s, length = %u, rented= %s \n", $out_name, $out_cat, $out_leng, $out_rented);
	




?>
