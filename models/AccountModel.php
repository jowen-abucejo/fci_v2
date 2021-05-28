<?php

class AccountModel {

    private $table_name = 'accounts';
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }

    private function sanitizedInput($input) {
        return $input = preg_replace('/<(.*)>/i', '', $input);
    }
    
    function login($username,$password) {
        $ReadDetails = null;
        $GetDetails = "SELECT uname,utype,fname,mname,lname,status FROM {$this->table_name} WHERE uname=? AND upass=?";
        $stmt = $this->connect->prepare($GetDetails);

        $username = $this->sanitizedInput($username);
        $password = $this->sanitizedInput($password);

        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $password);
        if ($stmt->execute())
            $ReadDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ReadDetails;
    }

    function logout() {
        session_destroy();
        unset($_POST['username']);
        unset($_POST['password']);
        unset($_POST['utype']);
        unset($_POST['fullname']);
        if (isset($_SESSION['msg']))
            unset($_SESSION['msg']);
        if (isset($_SESSION['uname']))
            unset($_SESSION['uname']);
        if (isset($_SESSION['utype']))
            unset($_SESSION['utype']);
    }

    function createAccount($uname,$contact,$upass,$utype,$fname,$mname,$lname,$bdate,$address,$status) {
        $addAccount = "INSERT INTO accounts(uname,contact_number,upass,utype,fname,mname,lname,birth_date,address,status) "
                . "VALUES(?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->connect->prepare($addAccount);
        
        $uname = $this->sanitizedInput($uname);
        $contact = $this->sanitizedInput($contact);
        $upass = $this->sanitizedInput($upass);
        $utype =$this->sanitizedInput($utype);
        $fname = $this->sanitizedInput($fname);
        $mname = $this->sanitizedInput($mname);
        $lname = $this->sanitizedInput($lname);
        $bdate = $this->sanitizedInput($bdate);
        $address = $this->sanitizedInput($address);
        
        $stmt->bindParam(1, $uname);
        $stmt->bindParam(2, $contact);
        $stmt->bindParam(3, $upass);
        $stmt->bindParam(4, $utype);
        $stmt->bindParam(5, $fname);
        $stmt->bindParam(6, $mname);
        $stmt->bindParam(7, $lname);
        $stmt->bindParam(8, $bdate);
        $stmt->bindParam(9, $address);
        $stmt->bindParam(10, $status);
        try{
            return $stmt->execute();
        } catch (Exception $er){
            return false;
        }
    }
    
    function computeAge($uname) {
        $readAge = 0;        
        $GetBDate = "SELECT birth_date FROM {$this->table_name} WHERE uname=? ";
        $stmt = $this->connect->prepare($GetBDate);
        $stmt->bindParam(1, $uname);
        if($stmt->execute()){
            $reaBDate = $stmt->fetch(PDO::FETCH_ASSOC);
            $readAge = floor((time() - strtotime($reaBDate['birth_date'])) / 31556926);
        }
        return $readAge;
    }
    
    function activateAccount($id) {
        $Activate = "UPDATE {$this->table_name} SET status='ACTIVE'  WHERE id=?";
        $stmt = $this->connect->prepare($Activate);
        $id = $this->sanitizedInput($id);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    function deactivateAccount($id) {
        $Activate = "UPDATE {$this->table_name} SET status='INACTIVE'  WHERE id=?";
        $stmt = $this->connect->prepare($Activate);
        $id = $this->sanitizedInput($id);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    function resetLogin($id) {
        $ReadName = $this->readProfile($id);
        $resetData = strtolower(str_replace(' ','.',$ReadName['fname'].'.'.$ReadName['lname']));
        $Activate = "UPDATE {$this->table_name} SET uname=?,upass=? WHERE id=?";
        $stmt = $this->connect->prepare($Activate);
        
        $id = $this->sanitizedInput($id);
        $stmt->bindParam(1, $resetData, PDO::PARAM_STR);
        $stmt->bindParam(2, $resetData, PDO::PARAM_STR);
        $stmt->bindParam(3, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    function deleteAccount($id) {
        $DeleteAccount = "DELETE FROM {$this->table_name} WHERE id=?";
        $stmt = $this->connect->prepare($DeleteAccount);
        
        $id = $this->sanitizedInput($id);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        try{
            $stmt->execute();
            return true;
        } catch (Exception $er){
            return false;
        }
    }
    
    function updateProfile($id, $uname,$contact,$upass,$utype,$fname,$mname,$lname,$bdate,$address){
        $addAccount = "UPDATE {$this->table_name} SET uname=?,contact_number=?,upass=?,utype=?,fname=?,mname=?,lname=?,birth_date=?,address=? WHERE id=?";
        $stmt = $this->connect->prepare($addAccount);
        
        $id = $this->sanitizedInput($id);
        $uname = $this->sanitizedInput($uname);
        $contact = $this->sanitizedInput($contact);
        $upass = $this->sanitizedInput($upass);
        $utype =$this->sanitizedInput($utype);
        $fname = $this->sanitizedInput($fname);
        $mname = $this->sanitizedInput($mname);
        $lname = $this->sanitizedInput($lname);
        $bdate = $this->sanitizedInput($bdate);
        $address = $this->sanitizedInput($address);
        
        $stmt->bindParam(1, $uname, PDO::PARAM_STR);
        $stmt->bindParam(2, $contact, PDO::PARAM_STR);
        $stmt->bindParam(3, $upass, PDO::PARAM_STR);
        $stmt->bindParam(4, $utype, PDO::PARAM_STR);
        $stmt->bindParam(5, $fname, PDO::PARAM_STR);
        $stmt->bindParam(6, $mname, PDO::PARAM_STR);
        $stmt->bindParam(7, $lname, PDO::PARAM_STR);
        $stmt->bindParam(8, $bdate, PDO::PARAM_STR);
        $stmt->bindParam(9, $address, PDO::PARAM_STR);
        $stmt->bindParam(10, $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    function readProfile($id) {
        $Profile = null;
        $GetProfile = "SELECT id,uname,upass,utype,contact_number,fname,mname,lname,birth_date,address,status FROM {$this->table_name} WHERE id=?";
        $stmt = $this->connect->prepare($GetProfile);
        
        $id = $this->sanitizedInput($id);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        if($stmt->execute())
            $Profile = $stmt->fetch(PDO::FETCH_ASSOC);
        return $Profile;    
    }
    
    function getAccountId($username){
        $readId = 0;
        $GetId = "SELECT id FROM {$this->table_name} WHERE uname=? ";
        $stmt = $this->connect->prepare($GetId);

        $username = $this->sanitizedInput($username);

        $stmt->bindParam(1, $username);
        if ($stmt->execute())
            $readId = $stmt->fetch(PDO::FETCH_ASSOC);
        return $readId['id'];
    }
    
    function countVerifiedAccounts(){
        $GetCount = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE status != 'TO REVIEW' AND utype!='DEV' ";
        $stmt = $this->connect->prepare($GetCount);
        $stmt->execute();
        $ReadCount = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ReadCount['count'];
    }
    
    function countNotVerifiedAccounts(){
        $GetCount = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE status='TO REVIEW' ";
        $stmt = $this->connect->prepare($GetCount);
        $stmt->execute();
        $ReadCount = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ReadCount['count'];
    }
    
    function readVerifiedAccounts($offset, $limit) {
        $ReadAccounts = null;
        $GetAccounts = "SELECT * FROM accounts WHERE status !='TO REVIEW' AND utype!='DEV' ORDER BY lname,fname,mname ASC LIMIT $offset,$limit";
        $stmt = $this->connect->prepare($GetAccounts);
        $stmt->execute();
        $ReadAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadAccounts;
    }
    
    function readNotVerifiedAccounts($offset, $limit) {
        $ReadAccounts = null;
        $GetAccounts = "SELECT * FROM accounts WHERE status='TO REVIEW' AND utype!='DEV' ORDER BY lname,fname,mname ASC LIMIT $offset,$limit";
        $stmt = $this->connect->prepare($GetAccounts);
        $stmt->execute();
        $ReadAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadAccounts;
    }

    function searchVerifiedAccounts($searchWord){
        $ReadAccounts = null;
        $GetAccounts = "SELECT * FROM accounts WHERE status!='TO REVIEW' AND utype!='DEV' AND (uname LIKE '%$searchWord%' OR lname LIKE '%$searchWord%' OR fname LIKE '%$searchWord%') ORDER BY lname,fname,uname ASC LIMIT 5";
        $stmt = $this->connect->prepare($GetAccounts);
        $stmt->execute();
        $ReadAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadAccounts;
    }

    function searchUnverifiedAccounts($searchWord){
        $ReadAccounts = null;
        $GetAccounts = "SELECT id,uname,lname,fname FROM accounts WHERE status='TO REVIEW' AND utype!='DEV' AND (uname LIKE '%$searchWord%' OR lname LIKE '%$searchWord%' OR fname LIKE '%$searchWord%') ORDER BY lname,fname,uname ASC LIMIT 5";
        $stmt = $this->connect->prepare($GetAccounts);
        $stmt->execute();
        $ReadAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadAccounts;
    }

}

?>