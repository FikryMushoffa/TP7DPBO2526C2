<?php
class Veterinarian {
    private $conn;
    private $table = "veterinarians";

    public $id_vet;
    public $name;
    public $gender;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (name, gender) VALUES (:name, :gender)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gender', $this->gender);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id_vet ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_vet = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table} SET name = :name, gender = :gender WHERE id_vet = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':id', $this->id_vet);
        return $stmt->execute();
    }

    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_vet = :id");
        $stmt->bindParam(':id', $this->id_vet);
        $success = $stmt->execute();
        if ($success) {
            $this->conn->query("ALTER TABLE {$this->table} AUTO_INCREMENT = 1");
        }
        return $success;
    }
}
?>

