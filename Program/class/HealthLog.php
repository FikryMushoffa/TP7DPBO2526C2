<?php
class HealthLog {
    private $conn;
    private $table = "health_logs";

    public $id_log;
    public $id_animal;
    public $id_vet;
    public $checkup_date;
    public $diagnosis;
    public $recovery_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (id_animal, id_vet, checkup_date, diagnosis, status) 
                  VALUES (:id_animal, :id_vet, :checkup_date, :diagnosis, 'Dalam Perawatan')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_animal', $this->id_animal);
        $stmt->bindParam(':id_vet', $this->id_vet);
        $stmt->bindParam(':checkup_date', $this->checkup_date);
        $stmt->bindParam(':diagnosis', $this->diagnosis);
        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT h.*, a.nickname, a.species_name, v.name AS vet_name 
                  FROM {$this->table} h 
                  JOIN animals a ON h.id_animal = a.id_animal 
                  JOIN veterinarians v ON h.id_vet = v.id_vet 
                  ORDER BY h.id_log ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id_log = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $status = $this->status ?? 'Dalam Perawatan';
        $recovery = !empty($this->recovery_date) ? $this->recovery_date : null;
        if ($status === 'Sembuh' && empty($recovery)) {
            $recovery = date('Y-m-d H:i:s');
        } else if ($status === 'Dalam Perawatan') {
            $recovery = null;
        }

        $query = "UPDATE {$this->table} 
                  SET id_animal = :id_animal, id_vet = :id_vet, checkup_date = :checkup_date, diagnosis = :diagnosis, status = :status, recovery_date = :recovery_date 
                  WHERE id_log = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_animal', $this->id_animal);
        $stmt->bindParam(':id_vet', $this->id_vet);
        $stmt->bindParam(':checkup_date', $this->checkup_date);
        $stmt->bindParam(':diagnosis', $this->diagnosis);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':recovery_date', $recovery);
        $stmt->bindParam(':id', $this->id_log);
        $success = $stmt->execute();

        if ($success) {
            if ($status === 'Sembuh') {
                $stmtAnimal = $this->conn->prepare("UPDATE animals SET status = 'Siap Rilis' WHERE id_animal = :id_animal");
                $stmtAnimal->bindParam(':id_animal', $this->id_animal);
                $stmtAnimal->execute();
            } else if ($status === 'Dalam Perawatan') {
                $stmtCheck = $this->conn->prepare("SELECT status FROM animals WHERE id_animal = :id_animal");
                $stmtCheck->bindParam(':id_animal', $this->id_animal);
                $stmtCheck->execute();
                $currStatus = $stmtCheck->fetchColumn();
                if ($currStatus === 'Siap Rilis') {
                    $stmtAnimal = $this->conn->prepare("UPDATE animals SET status = 'Karantina' WHERE id_animal = :id_animal");
                    $stmtAnimal->bindParam(':id_animal', $this->id_animal);
                    $stmtAnimal->execute();
                }
            }
        }
        return $success;
    }

    public function completeTreatment($id) {
        $query = "UPDATE {$this->table} 
                  SET status = 'Sembuh', recovery_date = NOW() 
                  WHERE id_log = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $success = $stmt->execute();

        if ($success) {
            // Update status satwa terkait menjadi 'Siap Rilis'
            $log = $this->getById($id);
            if ($log && !empty($log['id_animal'])) {
                $stmtAnimal = $this->conn->prepare("UPDATE animals SET status = 'Siap Rilis' WHERE id_animal = :id_animal");
                $stmtAnimal->bindParam(':id_animal', $log['id_animal']);
                $stmtAnimal->execute();
            }
        }
        return $success;
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id_log = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_log);
        $success = $stmt->execute();
        if ($success) {
            $this->conn->query("ALTER TABLE {$this->table} AUTO_INCREMENT = 1");
        }
        return $success;
    }
}
?>
