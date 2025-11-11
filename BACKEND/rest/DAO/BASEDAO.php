<?php
require_once __DIR__ . '/../config.php';

class BaseDAO {
    protected $table;
    protected $connection;

    public function __construct($table) {
        $this->table = $table;
        $this->connection = Database::connect();
    }

    // GET ALL
    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // GET BY ID (dynamic PK)
    public function getById($id) {
        $idColumn = $this->getPrimaryKey();
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$idColumn} = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // INSERT
    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($id, $data) {
        $idColumn = $this->getPrimaryKey();

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        $fieldList = implode(", ", $fields);

        $sql = "UPDATE {$this->table} SET {$fieldList} WHERE {$idColumn} = :id";

        $stmt = $this->connection->prepare($sql);
        $data["id"] = $id;

        return $stmt->execute($data);
    }

    // DELETE
    public function delete($id) {
        $idColumn = $this->getPrimaryKey();

        $stmt = $this->connection->prepare("DELETE FROM {$this->table} WHERE {$idColumn} = :id");
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // Detect primary key (always tableName_id)
    private function getPrimaryKey() {
        return $this->table . "_id";
    }
}
?>
