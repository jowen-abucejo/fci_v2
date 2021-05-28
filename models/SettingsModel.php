<?php
class SettingsModel {
    private $table_name = 'settings';
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }
    
    function getWeeklySettings($weekdate) {
        $ReadSettings = null;
        if(!$this->isSetCurrentWeek($weekdate))
            $ReadSettings = $this->getDefaultSettings();
        else{
            $GetSettings = "SELECT seat_limit,service_time,min_age,max_age FROM {$this->table_name} WHERE service_date = ? ";
            $stmt = $this->connect->prepare($GetSettings);
            $stmt->bindParam(1, $weekdate, PDO::PARAM_STR);
            $stmt->execute();
            $ReadSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $ReadSettings;  
    }
    
    function getDefaultSettings() {
        $ReadSettings = null;
        $GetSettings = "SELECT seat_limit,service_time,min_age,max_age FROM {$this->table_name} WHERE status='ACTIVE'";
        $stmt = $this->connect->prepare($GetSettings);        
        if($stmt->execute())
            $ReadSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadSettings;  
    }
    
    function getAgeLimit($weekdate) {
        $ReadAgeLimit = null;
        $GetAgeLimit = "SELECT min_age,max_age FROM {$this->table_name} WHERE service_date=? OR status='ACTIVE' ORDER BY service_date DESC";
        $stmt = $this->connect->prepare($GetAgeLimit);
        $stmt->bindParam(1, $weekdate, PDO::PARAM_STR);
        if($stmt->execute()){
            $ReadAgeLimit = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $ReadAgeLimit;
    }
    
    function updateDefaultSettings($service_time, $seat_limit, $min_age, $max_age){
        $NewSettings = "UPDATE {$this->table_name} SET seat_limit=?,min_age=?,max_age=? WHERE status='ACTIVE' AND service_time=?";
        $stmt = $this->connect->prepare($NewSettings);
        
        $stmt->bindParam(1, $seat_limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $min_age, PDO::PARAM_INT);
        $stmt->bindParam(3, $max_age, PDO::PARAM_INT);
        $stmt->bindParam(4, $service_time, PDO::PARAM_STR);
        
        return($stmt->execute());
    }
    
    function updateCurrentWeekSettings($service_date, $service_time, $seat_limit, $min_age, $max_age){
        $NewSettings = ($this->isSetCurrentWeek($service_date))? "UPDATE {$this->table_name} SET seat_limit=?,min_age=?,max_age=? WHERE service_date=? AND service_time=?":
                "INSERT INTO {$this->table_name}(seat_limit,min_age,max_age,service_date,service_time) VALUES(?,?,?,?,?)";
        $stmt = $this->connect->prepare($NewSettings);
        
        $stmt->bindParam(1, $seat_limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $min_age, PDO::PARAM_INT);
        $stmt->bindParam(3, $max_age, PDO::PARAM_INT);
        $stmt->bindParam(4, $service_date, PDO::PARAM_STR);
        $stmt->bindParam(5, $service_time, PDO::PARAM_STR);
    
        return($stmt->execute());
    }
    
    function isSetCurrentWeek($service_date) {
        $Check = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE service_date=? ";
        $stmt = $this->connect->prepare($Check);
        $stmt->bindParam(1, $service_date, PDO::PARAM_STR);
        if($stmt->execute()){
            $ReadCount = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($ReadCount['count']>1);
        }
    }
    
}
