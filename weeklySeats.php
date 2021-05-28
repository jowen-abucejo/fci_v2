<?php
session_start();
$title='FCI | WEEKLY RECORDS';
$selected=4;

if(isset($_SESSION['utype']) && ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')){
    require_once './security/Database.php';
    require_once './models/WeeklyServiceModel.php';
    $db = new Database();
    $weeklyService = new WeeklyServiceModel($db->getConnection());
    
    include 'header.php';
?>   
    <div class="min-h-500 pt-5">
        <div class="col-12 pb-5">
        <?php
            if(isset($_GET['readID'])){
                $read = $_GET['readID'];
                $countAll= $weeklyService->countLogsOfWeek($read);
                $currentPage=(isset($_GET['listpage']))? intval($_GET['listpage']) : 1;
                $limit = 10;
                $offset = ($currentPage>1)? ($currentPage-1) * $limit : 0;
                $fetchDates = $weeklyService->viewLogsOfWeek($read, $offset, $limit);
        ?>
            <hr class="border-dark mb-0">
            <h4 class="p-2 bg-lightgreen">Sunday Service Seats for <?php echo $checkDate=date_format(new DateTime($read), 'M-d-Y');?> <a class="btn bg-lightgreen fas fa-times-circle float-right" title="Close" href="weeklySeats.php?page=<?php echo $_GET['page'];?>"></a></h4>   
            <div class="table-responsive col-md-10 offset-md-1">
                <table class="table table-hover text-center table-bordered">
                    <tr class="bg-secondary text-light">
                        <th>Time</th>
                        <th>Seat Number</th>
                        <th>Full Name</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $GETpagename='readID='.$_GET['readID'].'&listpage';
                    $page_url='weeklySeats.php?';
                    
                    $today= date('D');
                    $nextSunday=date('Y-m-d');
                    if($today!='Sun'){
                        $date = new DateTime();
                        $date->modify('next sunday');
                        $nextSunday = $date->format('Y-m-d');
                    }
                    $lastSunday=date('M-d-Y',strtotime('last Sunday', strtotime($nextSunday)));
                    foreach($fetchDates as $readDATA){
                        echo '<tr>';
                        echo '<td>'.$readDATA['service_time'].'</td>';
                        echo '<td>'.$readDATA['seat_number'].'</td>';
                        echo '<td>'.$readDATA['lname'].', '.$readDATA['fname'].' '.$readDATA['mname'].'</td><td>';
                        echo (strtotime($checkDate)>strtotime($lastSunday))?'<form method="POST" action="saveSelection.php" class="p-0 m-0">'
                        . '<input type="submit" class="btn btn-warning py-0" value="Reset" name="resetSeat">'
                                . '<input type="hidden" value="'.$readDATA['uname'].'" name="resetUname">'
                                . '<input type="hidden" value="'.$nextSunday.'" name="sdate">'
                                . '<input type="hidden" value="'.$readDATA['service_time'].'" name="stime">'
                                . '<input type="hidden" value="'.$page_url.$GETpagename.$currentPage.'" name="redirectPage">'
                                . '</form>':'';
                        echo '</td></tr>';
                    }
                    ?>
                </table>
            </div><?php include 'paging.php';}?>
            
            
            <hr class="border-dark mb-0">
            <div class="col-12 px-0">
                <h4 class="p-2 bg-lightgreen">Weekly Seat</h4>
                <div class="table-responsive col-md-10 offset-md-1">
                    <table class="table table-hover table-bordered table-striped text-center">
                        <tr class="bg-secondary text-light">
                            <th>Service Date</th>
                            <th>Action</th>
                        </tr>
                        <?php
                            $countAll= $weeklyService->countRecordedSundays(); 
                            $currentPage=(isset($_GET['page']))? intval($_GET['page']) : 1;
                            $limit = 5;
                            $offset = ($currentPage>1)? ($currentPage-1) * $limit : 0;
                            if($countAll>0){
                                $sundays = $weeklyService->viewRecordedSundays($offset, $limit);
                                foreach($sundays as $GETinfo){
                                    echo '<tr>';
                                    echo '<td>'.$GETinfo['service_date'].'</td>';
                                    echo '<td><div class="btn-group" role="group"><a class="btn btn-info py-0" href="weeklySeats.php?readID='.$GETinfo['service_date'].'&page='.$currentPage.'">Read</a>';
                                    echo '</div></td>';
                                    echo '</tr>';
                                }
                            }
                            $GETpagename='page';
                            $page_url='weeklySeats.php?';
                        ?>
  
                    </table>
                    
                </div>
                <?php include 'paging.php';?>
            </div>
        </div>
    </div>
<?php
    if(isset($_SESSION['msg'])){
        echo '<script type="text/javascript">$("#popup").modal({backdrop: "static", keyboard: false})</script>';
    }
    include 'footer.php';
} else {
    header('location:log_out.php');
}?>
