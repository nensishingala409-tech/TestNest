<?php
$DBServerName = "localhost";
$DBUserName   = "root";
$DBPassword   = "";
$DBName       = "exam";

$conn = mysqli_connect($DBServerName, $DBUserName, $DBPassword, $DBName);

if(!$conn) {
    echo "!error";
}

if(isset($_POST["insert"])) {
    $name   = $_POST["name"];
    $id     = $_POST["id"];
    $contact= $_POST["contact_number"];
    $sem    = $_POST["current_sem"];

    $sql="INSERT INTO student (student_id, student_name, phone_no, current_sem) 
          VALUES ('$id', '$name', '$contact', '$sem')";
    $conn->query($sql);
}


if(isset($_POST["delete"])) {
    $id = $_POST["student_id"];
    $sql = "DELETE FROM student WHERE student_id='$id'";
    $conn->query($sql);
}

if(isset($_POST["update"])) {
    $id     = $_POST["student_id"];
    $name   = $_POST["student_name"];
    $contact= $_POST["phone_no"];
    $sem    = $_POST["current_sem"];

    $sql = "UPDATE student 
            SET student_name='$name', phone_no='$contact', current_sem='$sem' 
            WHERE student_id='$id'";
    $conn->query($sql);
}

$s = "SELECT student_id, student_name, phone_no, current_sem FROM student";
$result = $conn->query($s);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
</head>
<body>
    <h1>For insert new student</h1>
    <form method="post" action="">
        ID: <input type="text" name="id" placeholder="enter your id">
        <br><br>
        NAME: <input type="text"  name="name" placeholder="enter your name">
        <br><br>
        CONTACT NUMBER: <input type="text" name="contact_number" placeholder="enter your contact number">
        <br><br>
        CURRENT SEM: <input type="text" name="current_sem" placeholder="enter your current sem">
        <br><br>
        <input type="submit" name="insert" value="Insert">
    </form> 

    <h2>Student List</h2>
    <table cellpadding="5">
        <tr>
            <th>id</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Current Sem</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <form method='post' action=''>
                        <td><input type='text' name='student_id' value='".$row["student_id"]."' readonly></td>
                        <td><input type='text' name='student_name' value='".$row["student_name"]."'></td>
                        <td><input type='text' name='phone_no' value='".$row["phone_no"]."'></td>
                        <td><input type='text' name='current_sem' value='".$row["current_sem"]."'></td>
                        <td>
                            <input type='submit' name='update' value='Update'>
                            <input type='submit' name='delete' value='Delete'>
                        </td>
                        </form>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No students found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
