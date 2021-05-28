<?php
require_once './security/Database.php';
require_once './models/AccountModel.php';
$db = new Database();
$acModel = new AccountModel($db->getConnection());
$results='';
if(isset($_POST['searchVerified'])){
    $search = $_POST['searchVerified'];
    $accounts = $acModel->searchVerifiedAccounts($search);
    $allAccounts =$acModel->readVerifiedAccounts(0, 10000);
    if($accounts){
        foreach($accounts as $account){
            $index = array_search($account, $allAccounts);
            $page = ceil(($index+1)/10);
            $results.="<div class='card card-body m-0 p-0'><small><a class='btn btn-sm btn-outline-info btn-block border-0 text-dark' href='accounts.php?readID={$account['id']}&page={$page}'>@{$account['uname']}<br>{$account['lname']}, {$account['fname']}</a></small></div>";
        }
    } else {
        $results = "<div class='card card-body'><i>No match found!</i></div>";
    }
    unset($_POST['searchVerified']);
}elseif(isset($_POST['searchUnverified'])){
    $search = $_POST['searchUnverified'];
    $allAccounts2 = $acModel->readNotVerifiedAccounts(0, 10000);
    $accounts2 = $acModel->searchUnverifiedAccounts($search);
    if($accounts2){
        foreach($accounts2 as $account2){
            $index2 = array_search($account2, $allAccounts2);
            $page2 = ceil(($index2 + 1)/10);
            $results.="<div class='card card-body m-0 p-0'><small><a class='btn btn-sm btn-outline-info btn-block border-0 text-dark' href='toReviewAccounts.php?readID={$account2['id']}&page={$page2}'>@{$account2['uname']}<br>{$account2['lname']}, {$account2['fname']}</a></small></div>";
        }
    } else {
        $results = "<div class='card card-body'><small><i>No match found!</i></small></div>";
    }
    unset($_POST['searchUnverified']);
    
}else{

}
echo $results;
?>