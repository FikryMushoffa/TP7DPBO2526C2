<?php
class Animal {
    private $conn;
    private $table = "animals";

    public $id_animal;
    public $id_enclosure;
    public $species_name;
    public $nickname;
    public $rescue_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (id_enclosure, species_name, nickname, rescue_date, status) 
                  VALUES (:id_enclosure, :species_name, :nickname, :rescue_date, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_enclosure', $this->id_enclosure);
        $stmt->bindParam(':species_name', $this->species_name);
        $stmt->bindParam(':nickname', $this->nickname);
        $stmt->bindParam(':rescue_date', $this->rescue_date);
        $stmt->bindParam(':status', $this->status);
        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT a.*, e.name AS enclosure_name 
                  FROM {$this->table} a 
                  JOIN enclosures e ON a.id_enclosure = e.id_enclosure 
                  ORDER BY a.id_animal ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id_animal = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table} 
                  SET id_enclosure = :id_enclosure, species_name = :species_name, nickname = :nickname, rescue_date = :rescue_date, status = :status 
                  WHERE id_animal = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_enclosure', $this->id_enclosure);
        $stmt->bindParam(':species_name', $this->species_name);
        $stmt->bindParam(':nickname', $this->nickname);
        $stmt->bindParam(':rescue_date', $this->rescue_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id_animal);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id_animal = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_animal);
        $success = $stmt->execute();
        if ($success) {
            $this->conn->query("ALTER TABLE {$this->table} AUTO_INCREMENT = 1");
        }
        return $success;
    }
}
?>
