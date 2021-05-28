<?php
session_start();
$title = 'FCI | LOGIN';

if(isset($_SESSION['uname']))
    header ("location:reserveSeat.php");

require_once './security/Database.php';
require_once './models/AccountModel.php';

$db = new Database();
$acModel = new AccountModel($db->getConnection());
if(isset($_POST['username']) && isset($_POST['password'])){
    $uname = $_POST['username'];
    $upass =$_POST['password'];
    $readData = $acModel->login($uname,$upass);
    if($readData != null){
        if($readData['status'] == 'ACTIVE'){
            $_SESSION['fullname'] = $readData['fname'] . ' ' . $readData['mname'] . ' ' . $readData['lname'];
            $_SESSION['uname'] = $readData['uname'];
            $_SESSION['utype'] = $readData['utype'];
            header("location:reserveSeat.php");
        } 
        else
            $_SESSION['msg'] = ($readData['status'] == 'TO REVIEW') ? '<span class="fas fa-exclamation-circle text-danger"> Account Not Yet Activated!</span>' : '<span class="fas fa-exclamation-circle text-danger"> Account Deactivated!</span>';
        
    } else {
        $_SESSION['msg'] = '<span class="fas fa-exclamation-circle text-danger"> Incorrect username or password!</span>';
    }
}
include 'header.php';
?>
    <div class="min-h-500 pt-5 p-0">
        <div class="col-12 pb-5 col-md-10 offset-md-1">
            <!--<hr class="border-dark mb-0">-->
            <!--<h4 class="p-2 bg-lightgreen text-center">FCI Sunday Service Seat Online Reservation</h4>-->
            <hr class="border-0 mb-0">
            <h4 class="p-2 text-center"><strong>FCI Sunday Service Seat Online Reservation</strong></h4>
            <form method="POST" action="" class="card col-md-6 offset-md-3 bg-lightgreen">
                <div class="card-header" >
                    <h4 class="modal-title text-white"><strong>User Login</strong></h4>
                </div>
                <div class="card-body">  
                    <div class="input-group form-group my-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="username" class="form-control" placeholder="Username or Email" required autofocus>
                    </div>
                    <div class="input-group form-group my-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                        <div class="input-group-append">
                            <span class="input-group-text" style="cursor: pointer;" id="toggleShowPass"><i class="fas fa-eye" title="Show"></i></span>
                        </div>
                    </div>
                    <a class="btn btn-sm float-right btn-outline-primary text-light border-0" href="createAccount.php" title="No Account Yet?"><span class="fas fa-pencil-alt"></span> <strong>Create Account</strong></a>
                </div>
                <div class="card-footer">     
                    <button class="btn btn-lg btn-primary float-right" type="submit">
                        <span class="fas fa-unlock"> Login</span> 
                    </button>
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

