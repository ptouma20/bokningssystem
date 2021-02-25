

<?php
function tab(){
$conn = mysqli_connect('localhost','root','lovevirus14','bookningcalendar');
if(isset($_POST['date'])){
    $date = $_POST['date'];
    
}else{$date = date('Y-m-d');}

$sql = "select name, email, Tel, timeslot, RN, problem from bookings WHERE date = '$date' ORDER BY timeslot";

$result = $conn->query($sql);
if($result-> num_rows > 0 ){
    while($row = $result->fetch_assoc()){
        echo "<tr><td>". $row["name"] .
            "</td><td>" . $row["email"] . 
            "</td><td>" . $row["Tel"] . 
            "</td><td>". $row["timeslot"] .
            "</td><td>" . $row["RN"] . 
            "</td><td>" . $row["problem"] . 
            "</td></tr>";
    }
    echo "</table>";
}
else{
    echo "0 result";
}
$conn->close();
}

if(array_key_exists('Change', $_POST)){
    
$conn = mysqli_connect('localhost','root','lovevirus14','bookningcalendar');
$date = $_POST['date'];
$name2 = $_POST['name2'];
$time2 = $_POST['time2'];
$time3 = $_POST['time3'];
if($time2 <= 11){
    $time2 = $time2 . "AM";
}
if($time2 >= 12){
    $time2 = $time2 . "PM";
}
if($time3 <= 11){
    $time3 = $time3 . "AM";
}
if($time3 >= 12){
    $time3 = $time3 . "PM";
}
//$time4 = date('h:iA',strtotime($time2));
//$time5 = date('h:iA',strtotime($time3));
$stime = $time2 . "-" . $time3;
   //$stime = $time2->format("H:iA")."-".$time3->format("H:iA");

$sql = "UPDATE bookings
SET date = '$date', timeslot = '$stime'
WHERE name = '$name2' ";

if ($conn->query($sql) === TRUE) {
  echo "Record updated successfully";
} else {
  echo "Error updating record: " . $conn->error;
}

$conn->close();
}

if(array_key_exists('Remove', $_POST)){
    
$conn = mysqli_connect('localhost','root','lovevirus14','bookningcalendar');
$date = $_POST['date'];
$name2 = $_POST['name2'];
$sql = "DELETE FROM bookings
WHERE name = '$name2' ";

if ($conn->query($sql) === TRUE) {
  echo "Remove successfully";
} else {
  echo "Error Remove record: " . $conn->error;
}

$conn->close();
}
if(array_key_exists('lordag', $_POST)){
    
$conn = mysqli_connect('localhost','root','lovevirus14','bookningcalendar');
if(isset($_POST['lor'])){
    $lor = $_POST['lor'];
}else{$lor= false;}

$converted_res = $lor ? 'true' : 'false';
$sql = "UPDATE helg
SET lordag = '$converted_res' ";

if ($conn->query($sql) === TRUE) {
  echo "lordag successfully";
} else {
  echo "lordag not record: " . $conn->error;
}

$conn->close();
}


function check(){
$conn = mysqli_connect('localhost','root','lovevirus14','bookningcalendar');
if(isset($_POST['lor'])){
    $lor = $_POST['lor'];
}else{$lor= false;}

$converted_res = $lor ? 'true' : 'false';
$sql = "SELECT lordag FROM helg ";


  $result = $conn->query($sql);
  $ch = $result->fetch_assoc();
  $str = $ch["lordag"];

if($str == "true"){$r = "checked";}
else{$r = "unchecked";}

$conn->close();
print_r($r);
return $r;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        .container {
    background-color:black;
    text-decoration-color: white;
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
        .center {
  margin-left: auto;
  margin-right: auto;
}
        table {
            border-collapse: collapse;
            width: 87%;
            color: #588c7e;
            font-family: monospace;
            font-size: 25px;
            text-align: left;}
        }
       td, th {
         padding: 10px;
         border: 1px solid #588c7e;
         text-align: left;
        
              }
        tr:nth-child(even) {background-color:dodgerblue;}
input[type="checkbox"]{
  width: 20px; /*Desired width*/
  height: 20px; /*Desired height*/
}
    </style>
    </head>
    <body>
        <div class="container" style="color: #000000; background-color: #dddddd">
            <h1 class="text-center" >Show Book for Date </h1><hr>
            <div class="row">
                <div class="col-md-12">
                
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        
                        <form action = "" method = "post">
                  <label>Date  :</label><input type = "date" name = "date" id="date" class = "box"/><br /><br />
                  <input type = "submit" value = " Submit "/><br />
                        </form>
                        <form action = "" method = "post">
                            <?php check(); ?>
                  <label>LÖRDAG : </label><input  type = "checkbox" name = "lor" id="lor" class = "box"  <?php check(); ?> /><br />
                  <input type = "submit" value = " Submit " name="lordag"/><br />
                        </form>

                    </div>
                </div>
            </div>
        </div>
                                <table class="center" style="color: #ffffff; background-color: #000000">
    <tr>
        <th>name</th>
        <th>email</th>
        <th>Mobile</th>
        <th>Time</th>
        <th>R.number</th>
        <th>problem</th>
    </tr>
                                    <?php tab(); ?>
                        </table>
        <p> </p>
        <p> </p>
        <div class="container" style="color: #000000; background-color: #dddddd">
            <h1 class="text-center">Remove OR Change </h1><hr>
        <div class="row">
            <div class="col-md-2">
                    <div class="form-group">
                           <form action = "" method = "post">
                  <label>Name  :</label><input type = "name" name = "name2" id="name2" class = "box" /><br/><br />
                  <label>Start Time  :</label><input type = "time" name = "time2" id="time2" class = "box" /><br/><br />
                  <label>End Time  :</label><input type = "time" name = "time3" id="time3" class = "box" /><br/><br />
                  <label>Date  :</label><input type = "date" name = "date" id="date" class = "box" /><br/><br />
                  <input type = "submit" value = "Change" name="Change" /> <br />
                               <p> </p>
                  <input   type = "submit" value = "Remove" name="Remove" /><br />
                        </form>
        </div>
                </div>
            </div>
            </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <script>
        
        $(".book").click(function(){
        var timeslot = $(this).attr('data-timeslot');
                $("#slot").html(timeslot);
                $("#timeslot").val(timeslot);
                $("#myModal").modal("show");
        });
        
        </script>
        
    </body>
</html>