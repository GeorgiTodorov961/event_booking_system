<?php


class EventImport {

    private $conn;

    public function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function handleUser(string $employeeName, string $employeeMail) {
        $employeeMail = $this->conn->real_escape_string($employeeMail);
        $result = $this->conn->query("SELECT id FROM users WHERE email = '$employeeMail'");
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['id'];
        } else {
            return false; 
        }
    }

    public function updateUser(int $userId, string $employeeName, string $employeeMail) {
        $employeeName = $this->conn->real_escape_string($employeeName);
        $employeeMail = $this->conn->real_escape_string($employeeMail);
        $this->conn->query("UPDATE users SET name = '$employeeName' WHERE id = $userId");
    }

    public function insertUser(string $employeeName, string $employeeMail) {
        $employeeName = $this->conn->real_escape_string($employeeName);
        $employeeMail = $this->conn->real_escape_string($employeeMail);
        $this->conn->query("INSERT INTO users (name, email) VALUES ('$employeeName', '$employeeMail')");
        return $this->conn->insert_id;
    }

    public function handleEvent(string $eventName, string $eventDate) {
        $eventName = $this->conn->real_escape_string($eventName);
        $eventDate = $this->conn->real_escape_string($eventDate);
        $result = $this->conn->query("SELECT id FROM events WHERE name = '$eventName' AND date = '$eventDate'");
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['id'];
        } else {
            return false;
        }
    }

    public function updateEvent(int $eventId, string $eventName, string $eventDate) {
        $eventName = $this->conn->real_escape_string($eventName);
        $eventDate = $this->conn->real_escape_string($eventDate);
        $this->conn->query("UPDATE events SET name = '$eventName' WHERE id = $eventId");
    }

    public function insertEvent(string $eventName, string $eventDate) {
        $eventName = $this->conn->real_escape_string($eventName);
        $eventDate = $this->conn->real_escape_string($eventDate);
        $this->conn->query("INSERT INTO events (name, date) VALUES ('$eventName', '$eventDate')");
        return $this->conn->insert_id;
    }

    public function processData(array $data) {
        foreach ($data as $entry) {
            $employeeName = $this->conn->real_escape_string($entry['employee_name']);
            $employeeMail = $this->conn->real_escape_string($entry['employee_mail']);
            $eventName = $this->conn->real_escape_string($entry['event_name']);
            $eventDate = $this->conn->real_escape_string($entry['event_date']);
            $participationFee = $this->conn->real_escape_string($entry['participation_fee']);
            $version = isset($entry['version']) ? $this->conn->real_escape_string($entry['version']) : '';

            $userId = $this->handleUser($employeeName, $employeeMail);
            if (!$userId) {
                $userId = $this->insertUser($employeeName, $employeeMail);
            } else {
                $this->updateUser($userId, $employeeName, $employeeMail);
            }
    
            $eventId = $this->handleEvent($eventName, $eventDate);
            if (!$eventId) {
                $eventId = $this->insertEvent($eventName, $eventDate);
            } else {
                $this->updateEvent($eventId, $eventName, $eventDate);
            }
    
            $result = $this->conn->query("SELECT COUNT(*) as count FROM participations WHERE user_id = '$userId' AND event_id = '$eventId'");
            $count = $result->fetch_assoc()['count'];
            
            if ($count == 0) {
                $this->conn->query("INSERT INTO participations (user_id, event_id, participation_fee, event_date, version) 
                                    VALUES ('$userId', '$eventId', '$participationFee', '$eventDate', '$version')");
            }
        }
    }
    public function closeConnection() {
        $this->conn->close();
    }
}
?>