<?php
/**
 * Database Connection Class
 * Handles all database operations
 */

class Database {
    private $host;
    private $user;
    private $password;
    private $database;
    private $connection;

    public function __construct() {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        $this->connect();
    }

    // Create connection
    public function connect() {
        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            DB_PORT
        );

        // Check connection
        if ($this->connection->connect_error) {
            die('Connection Failed: ' . $this->connection->connect_error);
        }

        // Set charset
        $this->connection->set_charset('utf8mb4');
        return $this->connection;
    }

    // Prepare statement
    public function prepare($query) {
        return $this->connection->prepare($query);
    }

    // Execute query
    public function query($query) {
        return $this->connection->query($query);
    }

    // Escape string
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    // Get affected rows
    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    // Close connection
    public function closeConnection() {
        return $this->connection->close();
    }

    // Get connection
    public function getConnection() {
        return $this->connection;
    }

    // Execute prepared statement
    public function executePrepared($query, $params = [], $types = '') {
        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            return [
                'success' => false,
                'error' => $this->connection->error
            ];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $result = $stmt->execute();

        if (!$result) {
            return [
                'success' => false,
                'error' => $stmt->error
            ];
        }

        return [
            'success' => true,
            'stmt' => $stmt
        ];
    }

    // Fetch single row
    public function fetchOne($query) {
        $result = $this->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Fetch all rows
    public function fetchAll($query) {
        $result = $this->query($query);
        $rows = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    // Check if record exists
    public function recordExists($table, $column, $value) {
        $query = "SELECT 1 FROM {$table} WHERE {$column} = '{$this->escape($value)}' LIMIT 1";
        $result = $this->query($query);
        return $result && $result->num_rows > 0;
    }

    // Insert record
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $values = implode("','", array_map([$this, 'escape'], $data));
        $query = "INSERT INTO {$table} ({$columns}) VALUES ('{$values}')";
        
        if ($this->query($query)) {
            return [
                'success' => true,
                'id' => $this->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'error' => $this->connection->error
        ];
    }

    // Update record
    public function update($table, $data, $where) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = '{$this->escape($value)}'";
        }
        $set = implode(',', $set);
        $query = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        if ($this->query($query)) {
            return [
                'success' => true,
                'affected' => $this->affectedRows()
            ];
        }
        return [
            'success' => false,
            'error' => $this->connection->error
        ];
    }

    // Delete record
    public function delete($table, $where) {
        $query = "DELETE FROM {$table} WHERE {$where}";
        
        if ($this->query($query)) {
            return [
                'success' => true,
                'affected' => $this->affectedRows()
            ];
        }
        return [
            'success' => false,
            'error' => $this->connection->error
        ];
    }
}
?>
