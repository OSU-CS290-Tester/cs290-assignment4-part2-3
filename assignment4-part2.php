<?php
ini_set('display_errors', 'On');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection works!<br>";
}
if(isset($_POST['videoName'])){
	echo 'OK';
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

while ($stmt->fetch()) {
    printf("name = %s, category = %s, length = %u, rented= %u \n", $out_name, $out_cat, $out_leng, $out_rented);
}




?>
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