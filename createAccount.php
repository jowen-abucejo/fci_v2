<?php
session_start();
$title='FCI | CREATE ACCOUNT';
$selected=(!isset($_SESSION['uname']))?2:3.3;

if(isset($_SESSION['uname']) && !($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV'))
    header ("location: reserveSeat.php");

require_once './security/Database.php';
require_once './models/AccountModel.php';

$db = new Database();
$acModel = new AccountModel($db->getConnection());
if(isset($_POST['create'])){
    $uname = str_replace(' ', '',$_POST['newUsername']);
    $contact =$_POST['newContactNum'];
    $upass = str_replace(' ', '',$_POST['newPassword']);
    $utype =$_POST['usertype'];
    $fname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['firstname'])));
    $mname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['middlename'])));
    $lname = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['lastname'])));
    $bdate =$_POST['bdate'];
    $address = strtoupper(trim(preg_replace('!\s+!', ' ',$_POST['newAddress'])));
    $status = (isset($_POST['toreview'])) ? $_POST['toreview'] : 'ACTIVE';
    if($acModel->createAccount($uname, $contact, $upass, $utype, $fname, $mname, $lname, $bdate, $address, $status)){
        if (isset($_POST['toreview'])) {
            $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-success'> Account Created Successfully!</span>"
                    . "<br><span class='fas fa-exclamation-circle text-success'> Kindly Wait For Your Account Activation...</span>";
        } else {
            $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-success'> New account saved!</span>";
        }
    }else {
        $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-danger'> Unable to create account!</span>"
                . "<br><span class='fas fa-exclamation-circle text-danger'> An account already exist with the given details!</span>";
    }
}
include 'header.php';
?>
<div class="min-h-500 pt-5">
    <div class="col-12 mt-1 col-md-10 offset-md-1">
        <hr class="border-dark mb-0">
        <h4 class="p-2 bg-lightgreen">Create Account <?php echo (isset($_SESSION['uname']))?'':'<a class="btn btn-sm btn-outline-primary text-light border-0 float-right mb-1" href="log_in.php"><span class="fas fa-backward"></span> <strong>Back to Login</strong></a>';?></h4>
        <form method="POST" action="">
            <?php echo (!isset($_SESSION['uname']))?'<input type="hidden" value="TO REVIEW" name="toreview">':'';?>
                    <div class="table-responsive col-md-10 offset-md-1">
                        <table class="table table-hover text-center">
                            <tr>
                                <td>First Name : </td>
                                <td><input id="newFirstName" class="form-control" type="text" placeholder="First Name" required pattern="[a-zA-ZÑñ ]+" name="firstname"></td>
                            </tr>
                            <tr>
                                <td>Middle Name : </td>
                                <td><input class="form-control" type="text" placeholder="Middle Name" pattern="[a-zA-ZÑñ ]+" name="middlename"></td>
                            </tr>
                            <tr>
                                <td>Last Name : </td>
                                <td><input id="newLastname" class="form-control" type="text" placeholder="Last Name" required pattern="[a-zA-ZÑñ ]+" name="lastname"></td>
                            </tr>
                            <tr>
                                <td>Birthdate : </td>
                                <td><input class="form-control" type="date" required name="bdate" id="newBDate"></td>
                            </tr>
                            <tr>
                            <td>Age : </td>
                            <td><input class="form-control" type="text" id="newAge" placeholder="Age" disabled></td>
                            </tr>
                            <tr>
                                <td>Contact Number : </td>
                                <td><input class="form-control" type="text" placeholder="11-digit Mobile Number" required pattern="[0-9]{11}" required name="newContactNum"></td>
                            </tr>
                            <tr>
                                <td>Address : </td>
                                <td><input class="form-control" type="text" placeholder="Address" required name="newAddress"></td>
                            </tr>
                            <tr>
                                <td>Username : </td>
                                <td><input id="newUsername" class="form-control" type="text" placeholder="Username" required pattern="[a-zA-ZÑñ0-9@_.]+" name="newUsername"></td>
                            </tr>
                            <tr>
                                <td>Password : </td>
                                <td><input id="newPassword" class="form-control" type="text" placeholder="Password" required name="newPassword"></td>
                            </tr>
                            <tr>
                                <td>User Type : </td>
                                <td>
                                    <div class="form-inline"><input id="MemberType" class="form-inline mr-2" type="radio" name="usertype" value="MEMBER" checked><label for="MemberType">MEMBER</label></div>
                                    <?php echo (isset($_SESSION['uname']))?'<div class="form-inline"><input id="MemberType2" class="form-inline mr-2" type="radio" name="usertype" value="ADMIN"><label for="MemberType2">ADMIN</label></div>':'';?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button class="float-right btn btn-lg btn-primary fas fa-save mt-2" type="submit" name="create"> Create Account </button></td>
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
?>

