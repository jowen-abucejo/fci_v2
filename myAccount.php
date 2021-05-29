<?php
session_start();
$title='FCI | MY ACCOUNT';
$selected=6;
require_once './security/Database.php';
require_once './models/AccountModel.php';
require_once './models/WeeklyServiceModel.php';
$db = new Database();
$acModel = new AccountModel($db->getConnection());
$weeklyService = new WeeklyServiceModel($db->getConnection());

if(isset ($_POST['updateID'])){
    $id = $_POST['updateID'];
    $activeUser = $_SESSION['uname'];

    $uname = str_replace(' ', '',$_POST['newUsername']);
    $contact =$_POST['newContactNum'];
    $upass = str_replace(' ', '',$_POST['newPassword']);
    $utype = $_POST['usertype'];
    $fname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['firstname'])));
    $mname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['middlename'])));
    $lname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['lastname'])));
    $bdate =$_POST['bdate'];
    $address = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['newAddress'])));

    if($acModel->updateProfile($id, $uname, $contact, $upass, $utype, $fname, $mname, $lname, $bdate, $address)){
        $weeklyService->updateServiceRecordsOf($activeUser, $uname);
        $_SESSION['fullname'] = $fname.' '.$mname.' '.$lname;
        $_SESSION['uname'] = $uname;
        $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-success'> Account update successful!</span>";
    } else {
        $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-danger'> Account update failed!</span>";
    }
}

include 'header.php';

if(isset($_SESSION['uname'])){
    $today= date('D');
    $nextSunday=date('Y-m-d');
    if($today!='Sun'){
        $date = new DateTime();
        $date->modify('next sunday');
        $nextSunday = $date->format('Y-m-d');
    }
    $read = $_SESSION['uname'];
    $id = $acModel->getAccountId($read);
    $readDATA = $acModel->readProfile($id);
    
?>   
    <div class="min-h-500 pt-5">
        <div class="col-12">
            <hr class="border-dark mb-0">
            <h4 class="p-2 bg-lightgreen">My Account</h4>
            <form method="POST" action="">
                <input type="hidden" value="<?php echo $readDATA['id']?>" name="updateID">
                <div class="table-responsive col-md-10 offset-md-1">
                    <table class="table table-hover text-center" id="updateTable">
                        <tr>
                            <td>First Name : </td>
                            <td><input class="form-control" type="text" placeholder="First Name" required pattern="[a-zA-ZÑñ ]+" name="firstname" value="<?php echo $readDATA['fname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Middle Name : </td>
                            <td><input class="form-control" type="text" placeholder="Middle Name" pattern="[a-zA-ZÑñ ]+" name="middlename" value="<?php echo $readDATA['mname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Last Name : </td>
                            <td><input class="form-control" type="text" placeholder="Last Name" required pattern="[a-zA-ZÑñ ]+" name="lastname" value="<?php echo $readDATA['lname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Birthdate : </td>
                            <td><input class="form-control " type="date" required name="bdate" id="BDate" value="<?php echo $readDATA['birth_date'].'" ';echo ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')?' ':'readonly' ;?>></td>
                        </tr>
                        <tr>
                            <td>Age : </td>
                            <td><input class="form-control" type="text" id="Age" placeholder="Age" disabled value="<?php $bdate = new DateTime($readDATA['birth_date']);
                                $now = new DateTime();
                                echo $age = ($bdate->diff($now))->y ?>"></td>
                        </tr>
                        <tr>
                            <td>Contact Number : </td>
                            <td><input class="form-control" type="text" placeholder="11-digit Mobile Number" required pattern="[0-9]{11}" required name="newContactNum" value="<?php echo $readDATA['contact_number'];?>"></td>
                        </tr>
                        <tr>
                            <td>Address : </td>
                            <td><input class="form-control" type="text" placeholder="Address" required name="newAddress" value="<?php echo $readDATA['address'];?>"></td>
                        </tr>
                        <tr>
                            <td>Username : </td>
                            <td><input class="form-control" type="text" placeholder="Username" required pattern="[a-zA-ZÑñ0-9@_.]+" name="newUsername" value="<?php echo $readDATA['uname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Password : </td>
                            <td><input class="form-control" type="text" placeholder="Password" required name="newPassword" value="<?php echo $readDATA['upass'];?>"></td>
                        </tr>
                        <tr>
                            <td>User Type : </td>
                            <td><div class="form-inline"><input id="MemberType" class="form-inline" type="radio" name="usertype" value="MEMBER" <?php echo ($readDATA['utype']!='ADMIN')?'checked':'';?> ><label for="MemberType">MEMBER</label></div>
                            <?php if($readDATA['utype']=='ADMIN' || $readDATA['utype']=='DEV'){?>
                                <div class="form-inline"><input id="AdminType" class="form-inline" type="radio" name="usertype" value="ADMIN" <?php echo ($readDATA['utype']=='ADMIN')?'checked':'';?>><label for="AdminType">ADMIN</label></div>
                                <?php if($readDATA['utype']=='DEV'){?><div class="form-inline"><input id="DevType" class="form-inline" type="radio" name="usertype" value="DEV" checked><label for="DevType">DEV</label></div>
                                    <?php }
                                }?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><button class="float-right btn btn-lg btn-info fas fa-check" type="submit" name="update" disabled style="cursor:default"> Update</button></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
<?php
    if(isset($_SESSION['msg'])){
        echo '<script type="text/javascript">$("#popup").modal({backdrop: "static", keyboard: false})</script>';
    }
    include 'footer.php';
} else {
    header('location:reserveSeat.php');
}?>
