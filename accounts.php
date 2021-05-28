<?php
session_start();
$title='FCI | ACCOUNTS';
$selected=3.1;

if(isset($_SESSION['utype']) && ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')){
    require_once './security/Database.php';
    require_once './models/AccountModel.php';
    require_once './models/WeeklyServiceModel.php';
    $db = new Database();
    $acModel = new AccountModel($db->getConnection());
    $weeklyService = new WeeklyServiceModel($db->getConnection());

    if(isset($_GET['deactivateID'])){
        $id = $_GET['deactivateID'];
        $acModel->deactivateAccount($id);
    } else if(isset($_GET['activateID'])){
        $id = $_GET['activateID'];
        $acModel->activateAccount($id);
    } else if(isset($_GET['resetID'])){
        $id = $_GET['resetID'];
        $acModel->resetLogin($id);
    } else if(isset($_GET['deleteID'])){
        $id = $_GET['deleteID'];
        $readFName = $acModel->readProfile($id);

        $_GET['deleteID']='';
        $allGet ="del=true&delID=$id&";
        foreach ($_GET as $key => $value) {  
            if($key!='deleteID')
                $allGet=$allGet.$key.'='.$value.'&';
        }
        $_GET=array();
        $_POST['del_msg'] = "<p>Delete Account of <span class='text-danger'>{$readFName['fname']}</span>? </p><a class='btn btn-danger' href='accounts.php?$allGet'>Yes, delete account</a>";
    } else if(isset ($_POST['updateID'])){
        $id = $_POST['updateID'];
        $activeUserId = $acModel->getAccountId($_SESSION['uname']);

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
            $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-success'> Account update successful!</span>";
            if($id==$activeUserId){
                    $_SESSION['fullname'] = $fname.' '.$mname.' '.$lname;
                    $_SESSION['uname'] = $uname;
                    header('location:myAccount.php');
            }
        } else {
            $_SESSION['msg'] = "<span class='fas fa-exclamation-circle text-danger'> Account update failed!</span>";
        }
    } else{
        if(isset($_GET['del']) && $_GET['del']==true){
            $id = $_GET['delID'];
            $today= date('D');
            $nextSunday=date('Y-m-d');
            if($today!='Sun'){
                $date = new DateTime();
                $date->modify('next sunday');
                $nextSunday = $date->format('Y-m-d');
            }

            $readUname = $acModel->readProfile($id);
            $weeklyService->deleteServiceLogs($readUname['uname'], $nextSunday);
            if($acModel->deleteAccount($id)){
                $_SESSION['msg'] = "<span class='fas fa-exclamation-circle fa-1x text-success'> Account Deleted Successfully!</span>";
            }
        }
    }
    include 'header.php';

?>   
    <div class="min-h-500 pt-5">
        <div class="col-12 pb-5">
        <?php
            if(isset($_GET['readID'])){
                $id = $_GET['readID'];
                $profile = $acModel->readProfile($id);
        ?>
            <hr class="border-dark mb-0">
            <h4 class="p-2 bg-lightgreen">Account Details <a class="btn bg-lightgreen fas fa-times-circle float-right" title="Close" href="accounts.php?page=<?php echo isset($_GET['page'])?$_GET['page']:'';?>"></a></h4>
            <form method="POST" action="">
                <input type="hidden" value="<?php echo $id?>" name="updateID">
                <div class="table-responsive col-md-10 offset-md-1">
                    <table class="table table-hover text-center" id="updateTable">
                        <tr>
                            <td>First Name : </td>
                            <td><input class="form-control" type="text" placeholder="First Name" required pattern="[a-zA-ZÑñ ]+" name="firstname" value="<?php echo $profile['fname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Middle Name : </td>
                            <td><input class="form-control" type="text" placeholder="Middle Name" pattern="[a-zA-ZÑñ ]+" name="middlename" value="<?php echo $profile['mname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Last Name : </td>
                            <td><input class="form-control" type="text" placeholder="Last Name" required pattern="[a-zA-ZÑñ ]+" name="lastname" value="<?php echo $profile['lname'];?>"></td>
                        </tr>
                        <tr>
                            <td>Birthdate : </td>
                            <td><input class="form-control " type="date" required name="bdate" id="BDate" value="<?php echo $profile['birth_date'];?>"></td>
                        </tr>
                        <tr>
                            <td>Age : </td>
                            <td><input class="form-control" type="text" id="Age" placeholder="Age" disabled value="<?php $bdate = new DateTime($profile['birth_date']);
                                $now = new DateTime();
                                echo $age = ($bdate->diff($now))->y ?>"></td>
                        </tr>
                        <tr>
                            <td>Contact Number : </td>
                            <td><input class="form-control" type="text" placeholder="11-digit Mobile Number" required pattern="[0-9]{11}" required name="newContactNum" value="<?php echo $profile['contact_number'];?>"></td>
                        </tr>
                        <tr>
                            <td>Address : </td>
                            <td><input class="form-control" type="text" placeholder="Address" required name="newAddress" value="<?php echo $profile['address'];?>"></td>
                        </tr>
                        <tr>
                            <td>Username : </td>
                            <td><input class="form-control" type="text" placeholder="Username" required pattern="[a-zA-ZÑñ0-9@_.]+" name="newUsername" value="<?php echo $profile['uname'];?>"></td>
                        </tr>
                        
                        <tr>
                            <td>Password : </td>
                            <td><input class="form-control" type="text" placeholder="Password" required name="newPassword" value="<?php echo $profile['upass'];?>"></td>
                        </tr>
                        <tr>
                            <td>User Type : </td>
                            <td><div class="form-inline"><input id="MemberType" class="form-inline" type="radio" name="usertype" value="MEMBER" <?php echo ($profile['utype']!='ADMIN')?'checked':'';?> ><label for="MemberType">MEMBER</label></div>
                                <div class="form-inline"><input id="AdminType" class="form-inline" type="radio" name="usertype" value="ADMIN" <?php echo ($profile['utype']=='ADMIN' || $profile['utype']=='DEV')?'checked':'';?>><label for="AdminType">ADMIN</label></div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button class="float-right btn btn-lg btn-info fas fa-save" type="submit" name="update" disabled style="cursor: default"> Update</button></td>
                        </tr>

                    </table>
                </div>
            </form>
        <?php }?>
            
            
            <hr class="border-dark mb-0">
            <div class="col-12 px-0">
                <h4 class="p-2 bg-lightgreen">All Accounts</h4>
                <div class="row p-2 m-2">
                    <div class="col-md-6 col-12 col-xl-4 offset-md-3 offset-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for username, lastname or firstname..." id="searchcAcc">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        <div class="position-absolute w-100" id="searchResults">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped text-center">
                        <tr class="bg-secondary text-light">
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Account Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                            $countAll= $acModel->countVerifiedAccounts();
                            $currentPage=(isset($_GET['page']))? intval($_GET['page']) : 1;
                            $limit = 10;
                            $offset = ($currentPage>1)? ($currentPage-1) * $limit : 0;
                            if($countAll>0){
                                $fetchAccounts = $acModel->readVerifiedAccounts($offset, $limit);
                                foreach($fetchAccounts as $GETinfo){
                                    echo '<tr>';
                                    echo '<td>'.$GETinfo['uname'].'</td>';
                                    echo '<td>'.$GETinfo['lname'].', '.$GETinfo['fname'].' '.$GETinfo['mname'].'</td>';
                                    echo '<td>'.$GETinfo['status'].'</td>';
                                    echo '<td><div class="btn-group btn-group-sm" role="group"><a class="btn btn-info" href="accounts.php?readID='.$GETinfo['id'].'&page='.$currentPage.'">Read</a>';
                                    echo ($GETinfo['status']=='ACTIVE')? '<a class="btn btn-warning" href="accounts.php?deactivateID='.$GETinfo['id'].'&page='.$currentPage.'">Deactivate</a>':
                                        '<a class="btn btn-warning" href="accounts.php?activateID='.$GETinfo['id'].'&page='.$currentPage.'">Activate</a>';
                                    echo '<a class="btn btn-secondary" href="accounts.php?resetID='.$GETinfo['id'].'&page='.$currentPage.'" title="Reset Password and Username">Reset Login</a>';
                                    echo '<a class="btn btn-danger" href="accounts.php?deleteID='.$GETinfo['id'].'&page='.$currentPage.'">Delete</a>';
                                    echo '</div></td>';
                                    echo '</tr>';
                                }
                            }
                            $page_url='accounts.php?';
                            $GETpagename='page';
                        ?>                       
                    </table>  
                </div>
                <?php include 'paging.php';?>
            </div>
        </div>
    </div>
<?php
    if(isset($_SESSION['msg']) || isset($_POST['del_msg'])){
        echo '<script type="text/javascript">$("#popup").modal({backdrop: "static", keyboard: false})</script>';
    } 
    include 'footer.php';
} else {
    header('location:log_out.php');
}?>
