<?php

class Database
{
    private $pdo;

    public function __construct($path)
    {
        try {
            $this->pdo = new PDO("sqlite:$path");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function Execute($sql)
    {
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception("Query execution failed: " . $e->getMessage());
        }
    }

    public function Fetch($sql)
    {
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Query fetch failed: " . $e->getMessage());
        }
    }

    public function Create($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Create operation failed: " . $e->getMessage());
        }
    }

    public function Read($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Read operation failed: " . $e->getMessage());
        }
    }

    public function Update($table, $id, $data)
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = :$column";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE $table SET $set WHERE id = :id";
        $data['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Update operation failed: " . $e->getMessage());
        }
    }

    public function Delete($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Delete operation failed: " . $e->getMessage());
        }
    }

    public function Count($table)
    {
        $sql = "SELECT COUNT(*) as count FROM $table";
        try {
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Count operation failed: " . $e->getMessage());
        }
    }
}
