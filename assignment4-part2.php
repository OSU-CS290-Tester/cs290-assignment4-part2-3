<?php
ini_set('display_errors', 'On');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection works!<br>";
}

/* if($mysqli->query("CREATE TABLE video_inventory(id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL UNIQUE, category VARCHAR(255), length INT UNSIGNED, rented BOOL NOT NULL DEFAULT FALSE)") === TRUE) {
	printf("Table created.\n");
} */

?>
<!DOCTYPE html>
<html>
<body>
<form action="assignment.php" method="POST">

<table>
  <tr>
    <td>Add video</td>
  </tr>
  <tr>
    <td>Name</td>
  </tr>
</table>
<form action="assignment.php" method="POST">
<fieldset>
<legend>Add Video</legend>
<p>Name: <input type="text" name="videoName" ></p>
<p>Category: <select>
<option value="0">none</option>
<option value="1">action</option>
<option value="2">comedy</option>
<option value="3">drama</option></select></p>
<p>Length: <input type="number" name="length" min=0></p>
<input type="submit" value="submit">
</fieldset>
</form>
</body>


</html>