<?php
$mysql = new mysqli('localhost','root','lovevirus14','bookningcalendar');
if(isset($_GET['date'])){
    $date = $_GET['date'];
    $stmt = $mysql->prepare("select * from bookings where date = ? ");
    $stmt->bind_param('s',$date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row=$result->fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];
    $Tel = $_POST['Tel'];
    $RN = $_POST['RN'];
    $problem = $_POST['problem'];
    
    $stmt = $mysql->prepare("select * from bookings where date = ? AND timeslot=? ");
    $stmt->bind_param('ss',$date,$timeslot);
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            $msg = "<div class='alert alert-danger'>Already Booked</div>";
        }else{
            $stmt = $mysql->prepare("INSERT INTO bookings(name,email,Tel,timeslot,date,RN,problem) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssss',$name,$email,$Tel,$timeslot,$date,$RN,$problem);
            $stmt->execute();
            $msg = "<div class='alert alert-success'>Bookning Successfull</div>";
            $bookings[]=$timeslot;
            $stmt->close();
            $mysql->close();
        }
    }
    
}

$duration = 60;
$cleanup = 0;
$start = "08:00";
$end = "18:00";

function timeslots($duration,$cleanup,$start,$end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();
    for($intStart=$start;$intStart<$end;$intStart->add($interval)->add($cleanupInterval)){
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if($endPeriod>$end){
            break;
        }
        $slots[] = $intStart->format("H:iA")."-".$endPeriod->format("H:iA");
    }
    return $slots;
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
    </head>
    <body>
        <div class="container" style="color: #000000; background-color: #dddddd">
            <h1 class="text-center">Book for Date:<?php echo date('d/m/y',strtotime($date));?>
            </h1><hr>
            <div class="row">
                <div class="col-md-12">
                    <?php echo isset($msg)?$msg:""; ?>
                </div>
            <?php $timeslots = timeslots($duration,$cleanup,$start,$end);
                foreach($timeslots as $ts){
                    ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <?php if(in_array($ts,$bookings)){ ?>
                        <button class="btn btn-danger"><?php  echo $ts; ?></button>
                        <?php }else{ ?>
                        <button class="btn btn-success book"  data-timeslot="<?php echo $ts; ?>"><?php  echo $ts; ?></button>
                        <?php } ?>
                        
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
        
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Booking: <span id="slot"></span></h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <form action="" method="post">
                    <div class="from-group">
                        <label for="">Timeslot</label>
                        <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                    </div>
                    <div class="from-group">
                        <label for="">Name</label>
                        <input required type="text"  name="name" class="form-control">
                    </div>
                    <div class="from-group">
                        <label for="">Email</label>
                        <input required type="email"  name="email" class="form-control">
                    </div>
                    <div class="from-group">
                        <label for="">Tel.Number</label>
                        <input required type="tel"  name="Tel" class="form-control">
                    </div>
                    <div class="from-group">
                        <label for="">Car Registration</label>
                        <input required type="text"  name="RN" class="form-control" style="text-transform:uppercase">
                    </div>
                    
                    <div class="from-group">
                        <label for="">Service</label>
                          <select type="text"  name="problem" class="form-control">
                            <option value="AC-SERVICE">AC-SERVICE</option>
                            <option value="BÄRGINIG">BÄRGINIG</option>
                            <option value="BILREPARATION">BILREPARATION</option>
                            <option value="LÅNA BIL">LÅNA BIL</option>
                            <option value="BILSERVICE">BILSERVICE</option>
                            <option value="DÄCK">DÄCK</option>
                            <option value="ELSYSTEM">ELSYSTEM</option>
                          </select>
                          <br><br>
                    </div>
                    <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                    </div>
                </form>
            </div>
          </div>
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