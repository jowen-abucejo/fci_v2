<?php
class WeeklyServiceModel {
    private $table_name = 'service_logs';
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }
    
    private function sanitizedInput($input) {
        return $input = preg_replace('/<(.*)>/i', '', $input);
    }
    
    function getWeeklySeats($weekdate, $service_time) {
        $ReadSeats = "SELECT uname,seat_number FROM {$this->table_name} WHERE service_date=? AND service_time=? ORDER BY seat_number ASC";
        $stmt = $this->connect->prepare($ReadSeats);
        
        $stmt->bindParam(1, $weekdate, PDO::PARAM_STR);
        $stmt->bindParam(2, $service_time, PDO::PARAM_STR);
        if($stmt->execute())
            $ReadSeats = $stmt->fetchAll(PDO::FETCH_NUM);
        return $ReadSeats;
    }
    
    function reserveSeat($activeUser,$service_date,$service_time,$seatnumber) {
        $Reserve = "INSERT INTO {$this->table_name}(uname,service_date,service_time,seat_number) VALUES(?,?,?,?)";
        $stmt = $this->connect->prepare($Reserve);
        
        $stmt->bindParam(1, $activeUser, PDO::PARAM_STR);
        $stmt->bindParam(2, $service_date, PDO::PARAM_STR);
        $stmt->bindParam(3, $service_time, PDO::PARAM_STR);
        $stmt->bindParam(4, $seatnumber, PDO::PARAM_INT);  
        return $stmt->execute();
    }
    
    function resetSeat($uname, $service_date, $service_time) {
        $ResetSeat = "DELETE FROM {$this->table_name} WHERE uname=? AND service_date=? AND service_time=?";
        $stmt = $this->connect->prepare($ResetSeat);
        
        $stmt->bindParam(1, $uname, PDO::PARAM_STR);
        $stmt->bindParam(2, $service_date, PDO::PARAM_STR);
        $stmt->bindParam(3, $service_time, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    function deleteServiceLogs($uname, $service_date) {
        $DeleteRecords = "DELETE FROM {$this->table_name} WHERE uname=? AND service_date=? ";
        $stmt = $this->connect->prepare($DeleteRecords);
        
        $stmt->bindParam(1, $uname, PDO::PARAM_STR);
        $stmt->bindParam(2, $service_date, PDO::PARAM_STR);
        
        $stmt->execute();
    }
    
    function countLogsOfWeek($service_date){
        $ReadCount = null;
        $GetCount = "SELECT COUNT(*) AS count FROM {$this->table_name} sl INNER JOIN accounts a ON sl.uname=a.uname WHERE sl.service_date=?";
        $stmt = $this->connect->prepare($GetCount);
        
        $service_date = $this->sanitizedInput($service_date);
        $stmt->bindParam(1, $service_date, PDO::PARAM_STR);
        $stmt->execute();
        $ReadCount = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ReadCount['count'];  
    }
    
    function viewLogsOfWeek($service_date, $offset, $limit){
        $ReadLogs = null;
        $GetLogs = "SELECT * FROM {$this->table_name} sl INNER JOIN accounts a ON sl.uname=a.uname "
                        . "WHERE sl.service_date=? ORDER BY sl.service_time,sl.seat_number ASC LIMIT ?,?";
        $stmt = $this->connect->prepare($GetLogs);
        
        $service_date = $this->sanitizedInput($service_date);
        
        $stmt->bindParam(1, $service_date, PDO::PARAM_STR);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->bindParam(3, $limit, PDO::PARAM_INT);
        
        if($stmt->execute())
            $ReadLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadLogs;
    }
    
    function countRecordedSundays() {
        $ReadCount = null;
        $GetCount = "SELECT COUNT(DISTINCT(service_date)) AS count FROM {$this->table_name}";
        $stmt = $this->connect->prepare($GetCount);
        $stmt->execute();
        $ReadCount = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ReadCount['count'];  
    }
    
    function viewRecordedSundays($offset,$limit) {
        $ReadSundays = null;
        $GetSundays = "SELECT DISTINCT service_date FROM {$this->table_name} ORDER BY service_date DESC LIMIT ?,?";
        $stmt = $this->connect->prepare($GetSundays);
        
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        
        if($stmt->execute())
            $ReadSundays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ReadSundays;
    }
    
    function deleteLogsIf($seat_limit, $service_date, $service_time){
        $DeleteRecords = "DELETE FROM {$this->table_name} WHERE seat_number > ? AND service_date=? AND service_time=? ";
        $stmt = $this->connect->prepare($DeleteRecords);
        
        $stmt->bindParam(1, $seat_limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $service_date, PDO::PARAM_STR);
        $stmt->bindParam(3, $service_time, PDO::PARAM_STR);
        $stmt->execute();
    }

    function updateServiceRecordsOf($oldUname, $newUname){
        $UpdateQuery = "UPDATE {$this->table_name} SET uname=? WHERE uname=?";
        $stmt = $this->connect->prepare($UpdateQuery);
        $nname = $this->sanitizedInput($newUname);
        $oUname = $this->sanitizedInput($oldUname);
        $stmt->bindParam(1, $nname, PDO::PARAM_STR);
        $stmt->bindParam(2, $oUname, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
