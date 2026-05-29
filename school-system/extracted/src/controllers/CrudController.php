<?php
// src/controllers/CrudController.php
require_once __DIR__ . '/BaseController.php';

class CrudController extends BaseController {
    private $table;

    public function __construct($pdo, $table) {
        parent::__construct($pdo);
        $this->table = $table;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getById($idColumn, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$idColumn} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $data = array_map('trim', $data);
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        return $stmt->execute($data);
    }

    public function update($data, $idColumn, $id) {
        $data = array_map('trim', $data);
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ", ");
        $data[$idColumn] = $id;
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$fields} WHERE {$idColumn} = :{$idColumn}");
        return $stmt->execute($data);
    }

    public function delete($idColumn, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$idColumn} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
