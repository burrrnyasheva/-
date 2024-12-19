<?php

class Action
{
    private $pdo;
    private $table;

    public function __construct($db, $table) {
        $this->pdo = $db;
        $this->table = $table;
    }

    public function get_all()
    {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function get_one($id)
    {
        $sql = "SHOW COLUMNS FROM " . $this->table;
        $stmt = $this->pdo->query($sql);
        $first_column = $stmt->fetch(PDO::FETCH_ASSOC)['Field'];

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $first_column . " = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt;
    }

    public function create(array $data, $table)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(function($key) {
            return ":" . $key;
        }, array_keys($data)));
        $sql = "INSERT INTO " . $table . " ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update(array $data, $id)
    {
        $primaryKey = $this->getPrimaryKey();
        if (!$primaryKey) {
            throw new Exception("Primary key not found for table " . $this->table);
        }

        $columns = implode(", ", array_map(function($key) {
            return $key . " = :" . $key;
        }, array_keys($data)));

        $sql = "UPDATE " . $this->table . " SET $columns WHERE $primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $primaryKey = $this->getPrimaryKey();
        $sql  = "DELETE FROM " . $this->table . " WHERE $primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getPrimaryKey()
    {
        $sql = "SHOW KEYS FROM " . $this->table . " WHERE Key_name = 'PRIMARY'";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['Column_name'];
    }

    public function get_id() {
        // Предположим, что первичный ключ всегда первый столбец и содержит '_id'
        $sql = "SHOW COLUMNS FROM " . $this->table;
        $stmt = $this->pdo->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($columns as $column) {
            if (strpos($column['Field'], 'ID') !== false) {
                return $column['Field']; // возвращаем имя первичного ключа
            }
        }
        return null; // если не найдено, вернется null
    }

    public function get_column_name()
    {
        $sql = "SHOW COLUMNS FROM " . $this->table;
        $stmt = $this->pdo->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $columns;
    }
    
    public function all_tables()
    {
        $sql = "SHOW TABLES";
        $stmt = $this->pdo->query($sql);
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $tables;
    }

    public function ConnectTable($parentId) {
        // Получаем все внешние ключи, ссылающиеся на текущую таблицу
        $sql = "
            SELECT 
                TABLE_NAME, 
                COLUMN_NAME 
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_NAME = :table
        ";
        
        // Подготавливаем запрос для защиты от SQL-инъекций
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':table', $this->table, PDO::PARAM_STR);
        $stmt->execute();
        
        // Получаем все дочерние таблицы
        $childTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Проверяем, есть ли дочерние записи в этих таблицах
        foreach ($childTables as $table) {
            $childSql = "SELECT COUNT(*) AS count FROM " . $table['TABLE_NAME'] . " WHERE " . $table['COLUMN_NAME'] . " = :parentId";
            $childStmt = $this->pdo->prepare($childSql);
            $childStmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
            $childStmt->execute();
            $result = $childStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Если найдено хотя бы одно совпадение, возвращаем true
                return true; 
            }
        }
        
        // Если не найдено дочерних записей, возвращаем false
        return false;
    }

    public function checkForeignKey($foreignTable, $foreignColumn, $value) {
        $sql  = "SELECT COUNT(*) FROM " . $foreignTable . " WHERE " . $foreignColumn . " = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Вернет true, если запись найдена
    }

    public function getAllPrimaryKeyColumns() {
        $sql  ="SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE  TABLE_NAME = :table AND REFERENCED_TABLE_NAME IS NOT NULL;";

        $stmt = $this->pdo->prepare($sql);

        $stmt ->bindParam(':table', $this->table, PDO::PARAM_STR);
        $stmt ->execute();

        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $primaryKeys = [];

        foreach ($columns as $column) {
            $primaryKeys[] = 
            [
                'constraint_name'        => $column['CONSTRAINT_NAME'],
                'table_name'             => $column['TABLE_NAME'],
                'column_name'            => $column['COLUMN_NAME'],
                'referenced_table_name'  => $column['REFERENCED_TABLE_NAME'],
                'referenced_column_name' => $column['REFERENCED_COLUMN_NAME']
            ];
        }
        return $primaryKeys;
    }
}
?>