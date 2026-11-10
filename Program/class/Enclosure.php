<?php
class Enclosure {
    private $conn;
    private $table = "enclosures";

    public $id_enclosure;
    public $name;
    public $habitat_type;
    public $capacity;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (name, habitat_type, capacity) VALUES (:name, :habitat_type, :capacity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':habitat_type', $this->habitat_type);
        $stmt->bindParam(':capacity', $this->capacity);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id_enclosure ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_enclosure = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table} SET name = :name, habitat_type = :habitat_type, capacity = :capacity WHERE id_enclosure = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':habitat_type', $this->habitat_type);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':id', $this->id_enclosure);
        return $stmt->execute();
    }

    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_enclosure = :id");
        $stmt->bindParam(':id', $this->id_enclosure);
        $success = $stmt->execute();
        if ($success) {
            $this->conn->query("ALTER TABLE {$this->table} AUTO_INCREMENT = 1");
        }
        return $success;
    }
}
?>
