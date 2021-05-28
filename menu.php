<nav class="navbar navbar-expand-lg navbar-dark bg-success border-top border-bottom fixed-top d-flex">
    <a class="navbar-brand" href="https://faithcenterimus.wixsite.com/faithcenterimus">
        <span class="fas fa-place-of-worship fa-lg"></span>
        Faith Center Imus 
    </a>
<?php if(isset($_SESSION['uname'])){ ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav nav-fill">
            <li class="nav-item px-2 <?php if($selected==2) echo 'active';?>">
                <a class="nav-link" href="reserveSeat.php">Sunday Service Seat</a>
            </li>
            <?php
            if(isset($_SESSION['utype']) && ($_SESSION['utype']=='ADMIN' || $_SESSION['utype']=='DEV')){
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php if(floor($selected/1)==3) echo 'active';?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Manage Accounts</a>
                <div class="dropdown-menu bg-success">
                  <a class="dropdown-item-custom text-center <?php if($selected==3.1) echo 'active';?>" href="accounts.php">All Accounts</a>
                  <a class="dropdown-item-custom text-center <?php if($selected==3.2) echo 'active';?>" href="toReviewAccounts.php">Accounts to Review</a>
                  <a class="dropdown-item-custom text-center <?php if($selected==3.3) echo 'active';?>" href="createAccount.php">Add New Account</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 <?php if($selected==4) echo 'active';?>" href="weeklySeats.php">Weekly Records</a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 <?php if($selected==5) echo 'active';?>" href="settings.php">Weekly Settings</a>
            </li>
            <?php }?> 
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php if($selected==6) echo 'active';?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">My Account</a>
                <div class="dropdown-menu bg-success">
                    <a class="dropdown-item-custom text-center <?php if($selected==6) echo 'active';?>" href="myAccount.php">Profile</a>
                    <a class="dropdown-item-custom text-center" href="log_out.php"><span class="fa fa-lock"></span> Logout</a>
                </div>
            </li>
        </ul>
    </div>
<?php }?>
</nav>