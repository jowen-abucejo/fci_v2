<?php
session_start();
$title = 'FCI | SUNDAY SERVICE SEATS';
$selected = 2;

if (!isset($_SESSION['uname']))
    header ("location:log_in.php");
    
require_once './security/Database.php';
require_once './models/AccountModel.php';
require_once './models/SettingsModel.php';
require_once './models/WeeklyServiceModel.php';

$db = new Database();
$acModel = new AccountModel($db->getConnection());
$settings = new SettingsModel($db->getConnection());
$weeklyService = new WeeklyServiceModel($db->getConnection());

$activeUser=$_SESSION['uname'];
$fullname=$_SESSION['fullname'];
$today= date('D');
$nextSunday=date('Y-m-d');
if($today!='Sun'){
    $date = new DateTime();
    $date->modify('next sunday');
    $nextSunday = $date->format('Y-m-d');
}
$data = $settings->getWeeklySettings($nextSunday);

if(isset($_POST['seatNum'])){
    $age = $acModel->computeAge($activeUser);
    $age_limit = $settings->getAgeLimit($nextSunday);
    if(is_countable($age_limit) && ($age_limit['min_age']>$age || $age_limit['max_age']<$age)){
        $_SESSION['msg'] = '<span class="fas fa-exclamation-circle text-danger"> You\'re not allowed for this week!</span>';
    }else{
        $activeUser=$_SESSION['uname'];
        $service_date=$_POST['sdate'];
        $seatnumber=$_POST['seatNum'];
        $service_time=$_POST['stime'];
        $_SESSION['msg'] = ($weeklyService->reserveSeat($activeUser,$service_date,$service_time,$seatnumber))?
                "<span class='fas fa-exclamation-circle fa-1x text-success'> Your Seat Number for $service_time Service is $seatnumber</span>":
                "<span class='fas fa-exclamation-circle text-danger'> Selected Seat Already Reserved!</span>";
        header("location:reserveSeat.php");
    }   
}
if(isset($_POST['resetSeat'])){
    $activeUser=isset($_POST['resetUname'])?$_POST['resetUname']:$_SESSION['uname'];
    $service_date=$_POST['sdate'];
    $service_time=$_POST['stime'];
    $weeklyService->resetSeat($activeUser, $service_date, $service_time);
    isset($_POST['redirectPage'])? header('location:'.$_POST['redirectPage']):'';
}
include 'header.php';
?>
    
    <div class="min-h-500 pt-5 p-0">
        <div class="col-12 mt-4 pb-5 col-md-10 offset-md-1">
            <div class="btn-group mb-1">
                <button class="btn btn-dark border text-left border-left-0" type="button" disabled style="cursor: default">
                    Name : 
                </button>
                <button class="btn btn-dark border text-left border-left-0" type="button" disabled style="font-weight: bold; cursor: default">
                    <?php echo $fullname;?>
                </button>
            </div>
                <?php
                    for($i=0;$i<count($data);$i++){
                        $stime=$data[$i]['service_time'];
                        $data2 = $weeklyService->getWeeklySeats($nextSunday, $stime);
                        $unames= array_column($data2, 0);
                        $occupiedSeats= array_column($data2, 1);
                ?>
            <form method="POST" action="">
                <input type="hidden" value="<?php echo $nextSunday;?>" name="sdate">
                <input type="hidden" value="<?php echo $stime;?>" name="stime">
                <div class="btn-group mb-1">
                    <button type="text" class="btn btn-sm btn-dark border px-1 text-right border-right-0"disabled name="" style="cursor: default"><?php echo $stime;?></button>
                    <button class="btn btn-sm btn-dark border text-left border-left-0" type="button" disabled style="cursor: default">
                        Service Seat # :&nbsp;
                        <span class="btn btn-success btn-sm" style="font-weight: bold; cursor: default">
                        <?php echo $seatN = (in_array($activeUser, $unames))?$occupiedSeats[array_search($activeUser, $unames)]:'_';?>
                        </span>
                    </button>
                    <input type="submit" name="resetSeat" value="RESET" class="btn btn-warning btn-sm border" 
                    <?php //echo ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')? '<input type="submit" name="resetSeat" value="RESET" class="btn btn-warning btn-sm border">':''
                            echo ($seatN=='_')? 'disabled style="cursor: default"':''?>
                    ">
                </div>
            </form>
                <?php }?>
            <div class="btn-group mb-1">
                <button class="btn btn-dark border text-left border-left-0" type="button" disabled style="cursor: default">
                    Service Date : 
                </button>
                <button class="btn btn-dark border text-left border-left-0" type="button" disabled style="cursor: default; font-weight: bold">
                    <?php echo $nextSunday;?>
                </button>
            </div>
            <div id="SelectSeatTimeBtn" class="mb-2 text-center"><b>SELECT TIME & SEAT NUMBER </b><br>
                <?php for($i=0;$i<count($data);$i++){ ?>
                <button class="btn fas <?php echo ($i==0)?'btn-success fa-caret-up':'fa-caret-down';?>" id="SeatTimeBtn<?php echo $i;?>" type="button"> <?php echo $stime=$data[$i]['service_time'];?></button>    
                <?php }?>
            </div>
            <div class="mb-2 text-center">
                <small class="text-danger"><span class="fa fa-exclamation-circle"></span> Age Group Allowed is <b><?php echo $data[0]['min_age'].'-'.$data[0]['max_age'];?></b></small>
            </div>
            <div id="SeatSelection">
            
    <?php
    for($i=0;$i<count($data);$i++){ 
        $stime=$data[$i]['service_time'];
        $data2 = $weeklyService->getWeeklySeats($nextSunday, $stime);
        $unames= array_column($data2, 0);
        $occupiedSeats= array_column($data2, 1);
        echo (in_array($activeUser, $unames))?'<div>':'<form method="POST">';?>
                <input type="hidden" value="<?php echo $stime;?>" name="stime">
                <input type="hidden" value="<?php echo $nextSunday;?>" name="sdate">
                <div class='collapse collapseform<?php echo $i;         echo ($i==0)?' show':'';?>'>
                    <div class="table-responsive">
                        <table class="table table-borderless">
<?php
        for ($index = 1; $index <= intval($data[$i]['seat_limit']); $index++) {
            echo ($index%5==1 || $index==1)?'<tr>':'';
            echo "<td class='w-20'><input type='submit' name='seatNum' class='btn btn-mustard-yellow btn-block text-center' value='";
            echo (in_array($index, $occupiedSeats))?substr($unames[array_search($index, $occupiedSeats)],0,5).'...':$index;
            echo "'";
            echo (in_array($index, $occupiedSeats))?'disabled':'';
            echo "/></td>";
            echo ($index%5==0)?'</tr>':'';
        }?>
                        </table>
                    </div>
                </div>
        <?php   echo (in_array($activeUser, $unames))?'</div>':'</form>';?>
            
            
    <?php }?>
            </div>
            <!--<div class="w-100 pb-3"><a href="log_out.php" class="btn btn-danger btn-lg fas fa-lock float-right mt-2"> Logout</a></div>-->  
        </div>
    </div>
<?php
if(isset($_SESSION['msg'])){
    echo '<script type="text/javascript">$("#popup").modal({backdrop: "static", keyboard: false})</script>';
}
include 'footer.php';
?>

