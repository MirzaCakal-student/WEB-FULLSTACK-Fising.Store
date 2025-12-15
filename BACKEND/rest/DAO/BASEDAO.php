<?php
require_once __DIR__ . '/../config.php';

class BaseDao {
    protected $table;
    protected $idColumn;
    protected $connection;

    public function __construct($table, $idColumn = 'id') {
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->connection = Database::connect();
    }

    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->idColumn} = :id"
        );
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function queryUnique($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function query($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        foreach ($data as $col => $val) {
            $fields[] = "{$col} = :{$col}";
        }
        $fieldsStr = implode(", ", $fields);

        $sql = "UPDATE {$this->table} SET {$fieldsStr} WHERE {$this->idColumn} = :id";
        $stmt = $this->connection->prepare($sql);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->connection->prepare(
            "DELETE FROM {$this->table} WHERE {$this->idColumn} = :id"
        );
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>