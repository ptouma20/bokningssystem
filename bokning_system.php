<?php 
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

if($str == "true"){$r = "saturday";}
else{$r = "unchecked";}

$conn->close();
//print_r($r);
return $r;
}

function build_calender($month,$year){
    $mysql = new mysqli('localhost','root','lovevirus14','bookningcalendar');
    /*$stmt = $mysql->prepare("select * from bookings where MONTH(date) = ? AND YEAR(date) = ?");
    $stmt->bind_param('ss',$month,$year);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row=$result->fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }*/
    //create array containing abbreviation of dayes of week
    $daysOfWeeka = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

    //what is the first day of the month in question
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    //How many days does this month contain
    $numberDays = date('t',$firstDayOfMonth);

    //retrieve some information about the first day of the month in question
    $dateComponents = getdate($firstDayOfMonth);

    //what is the name of the month in question
    $monthName = $dateComponents['month'];

    //what is the index value (0-6) of the first day of the month
    $daysOfWeek = $dateComponents['wday'];
    

    //create the table tag opener and day headers
    $dateToday = date('Y-m-d');

    $calendar = "<table class ='table table-bordered'>";
    $calendar.= "<center><h2>$monthName $year</h2>";

    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m' ,mktime(0,0,0, $month-1, 1,
     $year))."&year=".date('Y',mktime(0,0,0, $month -1, 1, $year))."'>Previous Month</a>";

    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a>";

    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m',mktime(0,0,0,$month+1,1,
    $year))."&year=".date('Y',mktime(0,0,0,$month+1,1,$year))."'>Next Month</a></center><br>";
    $calendar.= "<br><table class='table table-bordered'>";
    $calendar.= "<tr>";

    //creating the calender headers
        foreach ($daysOfWeeka as $d)
        {
        $calendar.= "<th class='header'>$d</th>";
        }
    
    $calendar.= "</tr><tr>";

    //the variable $daysOfWeek will make sure that there must be only 7 columns on table
    if($daysOfWeek > 0 ){
        for($k=0;$k<$daysOfWeek;$k++) { $calendar.="<td></td>"; }
    }
    $currentDay = 1;
    $month = str_pad($month,2,"0",STR_PAD_LEFT);

    while($currentDay<=$numberDays)
    {
        //if seventh coiumn (saturday) reached start a new row
        if($daysOfWeek == 7){
            $daysOfWeek = 0;
            $calendar.= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay,2,"0",STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        
        $dayname = strtolower(date('l',strtotime($date)));
        $eventNum = 0;
        $today = $date==date('Y-m-d')?"today":"";
        
        if($dayname== check()){
            $calendar.= "<td><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>Holiday</button>";
        }elseif($date<date('Y-m-d')){
            $calendar.= "<td><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button>";
        }
        else{
            $totalbookings = checkSlots($mysql,$date);
            // how many slotstims in the day --- for demo 2 slots in the day
            if($totalbookings==10){
                $calendar.= "<td class='$today'><h4>$currentDay</h4><a href='#' class='btn btn-danger btn-xs'>All Booked</a>";
            }else{
                $calendar.= "<td class='$today'><h4>$currentDay</h4><a href='book.php?date=".$date."' class='btn btn-success btn-xs'>Book</a>";
            }
            
        }
        //if($dateToday==$date){
        //    $calendar.="<td class='today'><h4>$currentDay</h4>";
        //}else{
        //    $calendar.="<td><h4>$currentDay</h4>";
        //}
        
        $calendar.= "</td>";
        //incrementing the counters
        $currentDay++;
        $daysOfWeek++;
    }
    //compiling the row of the last week in month if necessary
    if($daysOfWeek != 7){
        $remainingDays = 7-$daysOfWeek;
        for($i=0;$i<$remainingDays;$i++){
            $calendar.="<td></td>";
        }
    }
    $calendar.= "</tr>";
    $calendar.= "</table>";
    return $calendar;  
}
function checkSlots($mysql,$date){
    $stmt = $mysql->prepare("select * from bookings where date = ? ");
    $stmt->bind_param('s',$date);
    $totalbookings = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row=$result->fetch_assoc()){
                $totalbookings++;
            }
            $stmt->close();
        }
    }
    return $totalbookings;
}


?>
<html>

<head>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        table {
            table-layout: fixed;
        }
        
        td {
            width: 33%;
        }
        
        .tbody {
            background: yellow;
        }
    </style>

</head>

<body>
    <div class="container" >
        <div class="row">
            <div class="col-md-12">
                <?php 
                $dateComponents = getdate();
                if(isset($_GET['month'])&&isset($_GET['year'])){
                    $month = $_GET['month'];
                    $year = $_GET['year'];
                }else{                    
                    $month = $dateComponents['month'];
                    $month = date('m', strtotime($month));
                    $year = $dateComponents['year'];
                }
                
                echo build_calender($month,$year);
                ?>
            </div>
        </div>
    </div>
</body>

</html>
