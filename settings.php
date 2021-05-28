<?php
session_start();
$title='FCI | SETTINGS';
$selected=5;
require_once './security/Database.php';
require_once './models/SettingsModel.php';
require_once './models/WeeklyServiceModel.php';
$db = new Database();
$settings = new SettingsModel($db->getConnection());
$weeklyService = new WeeklyServiceModel($db->getConnection());

if(isset($_SESSION['uname'])){
    $today= date('D');
    $nextSunday=date('Y-m-d');
    if($today!='Sun'){
        $date = new DateTime();
        $date->modify('next sunday');
        $nextSunday = $date->format('Y-m-d');
    }
?>   
    <div class="min-h-500 pt-5">
        <div class="col-12 pb-5">  
        <?php
        if(isset($_SESSION['utype']) && ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')){
            $feedback = '';
            if(isset($_POST['newDefault'])){
                $weekDate=$_POST['weekRange'];
                $WeeklySet=$_POST['weeklyIsSet'];
                $feedback = '';
                for($i=0;$i<count($_POST['default_service_time']);$i++){
                    $maxSeat=$_POST['defaultLimit'][$i];
                    $service_time=$_POST['default_service_time'][$i];
                    $minAge=$_POST['defaultMinAge'];
                    $maxAge=$_POST['defaultMaxAge'];
                    if($settings->updateDefaultSettings($service_time, $maxSeat, $minAge, $maxAge)){
                        if(!$WeeklySet)
                            $weeklyService->deleteLogsIf ($maxSeat, $weekDate, $service_time);
                    } else {
                        $feedback = '<span class="fas fa-exclamation-circle text-danger"> An error occured on update!</span>';
                    }
                }
                $_SESSION['msg'] = ($feedback!='')? $feedback:'<span class="fas fa-exclamation-circle text-success"> Update Success!</span>';
                
            } else if(isset($_POST['newWeekLimit'])){
                $weekDate=$_POST['weekRange'];
                $feedback = '';
                for($i=0;$i<count($_POST['wServiceTime']);$i++){
                    $maxSeat=$_POST['weekLimit'][$i];
                    $service_time=$_POST['wServiceTime'][$i];
                    $minAge=$_POST['weekMinAge'];
                    $maxAge=$_POST['weekMaxAge'];
                    if($settings->updateCurrentWeekSettings($weekDate, $service_time, $maxSeat, $minAge, $maxAge)){
                        $weeklyService->deleteLogsIf($maxSeat, $weekDate, $service_time);
                    } else {
                        $feedback = '<span class="fas fa-exclamation-circle text-danger"> An error occured on update!</span>';
                    }
                }
                $_SESSION['msg'] = ($feedback!='')?$feedback:'<span class="fas fa-exclamation-circle text-success"> Update Success!</span>';
            } else {
                
            }
            include 'header.php';
            $readDefaults = $settings->getDefaultSettings();
            $readThisWeekSeatLimit = $settings->getWeeklySettings($nextSunday);
             
        ?>
            <hr class="border-dark mb-0">
                <h4 class="p-2 bg-lightgreen">Seat Limit Settings</h4>
            <br>
            <form action="" method="POST">
                <input name="weekRange" type="hidden" value="<?php echo $nextSunday;?>">
                <input name="weeklyIsSet" type="hidden" value="<?php echo $weeklyIsset;?>">
                <div class="table-responsive col-md-10 offset-md-1">
                    <table class="table text-center table-hover" id="defaultSettingsTbl">
                        <tr class="bg-secondary text-light text-left">
                            <th colspan="4">Default Seats</th>
                        </tr>
                        <?php 
                        for($i=0;$i<count($readDefaults);$i++){?>
                        <tr>
                            <td>Service Time : </td>
                            <td><input name="default_service_time[]" type="text" class="form-control" readonly value="<?php echo $readDefaults[$i]['service_time'];?>"></td>
                        
                            <td>Seat Limit : </td>
                            <td><input name="defaultLimit[]" type="number" class="form-control" value="<?php echo $readDefaults[$i]['seat_limit'];?>"/></td>
                        </tr>
                              
                    <?php }?>
                        <tr>
                            <td>Minimum Age : </td>
                            <td><input name="defaultMinAge" type="number" class="form-control" value="<?php echo $readDefaults[0]['min_age'];?>"></td>
                        
                            <td>Maximum Age : </td>
                            <td><input name="defaultMaxAge" type="number" class="form-control" value="<?php echo $readDefaults[0]['max_age'];?>"/></td>
                        </tr>
                        <tr><td colspan="4"><button name="newDefault" type="submit" class="float-right btn btn-lg btn-info fas fa-check" disabled style="cursor:default"> Save Default Limit</button></td></tr>

                    </table>
                </div>
            </form>
            
            <form action="" method="POST">
                <input name="weekRange" type="hidden" value="<?php echo $nextSunday;?>">
                <div class="table-responsive col-md-10 offset-md-1">
                    <table class="table text-center table-hover" id="currWeekSettingsTbl">
                        <tr class="bg-secondary text-light text-left">
                            <th colspan="4">Weekly Seats [ <?php echo $nextSunday;?> ]</th>
                        </tr>
                        <?php 
                        for($i=0;$i<count($readThisWeekSeatLimit);$i++){?>
                        <tr>
                            <td>Service Time : </td>
                            <td><input name="wServiceTime[]" type="text" class="form-control" readonly value="<?php echo $readThisWeekSeatLimit[$i]['service_time'];?>"></td>
                        
                            <td>Seat Limit : </td>
                            <td><input name="weekLimit[]" type="number" class="form-control" value="<?php echo $readThisWeekSeatLimit[$i]['seat_limit'];?>"/></td>
                        </tr>
                              
                    <?php }?>
                        <tr>
                            <td>Minimum Age : </td>
                            <td><input name="weekMinAge" type="number" class="form-control" value="<?php echo $readThisWeekSeatLimit[0]['min_age'];?>"></td>
                        
                            <td>Maximum Age : </td>
                            <td><input name="weekMaxAge" type="number" class="form-control" value="<?php echo $readThisWeekSeatLimit[0]['max_age'];?>"/></td>
                        </tr>
                        <tr><td colspan="4"><button name="newWeekLimit" type="submit" class="float-right btn btn-lg btn-info fas fa-check" disabled style="cursor:default"> Save Week Limit</button></td></tr>

                    </table>
                </div>
            </form>       
        <?php }?>
        </div>
    </div>
<?php
    if(isset($_SESSION['msg'])){
        echo '<script type="text/javascript">$("#popup").modal({backdrop: "static", keyboard: false})</script>';
    }
    include 'footer.php';
} else {
    header('location:log_in.php');
}?>
