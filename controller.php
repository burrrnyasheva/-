<?php
require 'database.php';
require 'actions.php';

$db = new Database();
$pdo = $db->getConnection();

$table = $_GET['table'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

$action = new Action($pdo, $table);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'read') {
        if ($id) {
            $stmt = $action->get_one($id);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $tables = $action->all_tables();
            $column_id = $action->get_id();
            include 'read.php';
        } else {
            $stmt = $action->get_all();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tables = $action->all_tables();
            $column_id = $action->get_id();
            include 'read.php';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'create') {
        $table_name = $_GET['table'];
        $tables = $action->all_tables();
        $column_id = $action->get_id();
        $stmt = $pdo->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'create.php';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'update') {
        $table_name = $_GET['table'];
        $ID = $_GET['id'];
        $columns = $action->get_column_name();

        $stmt = $action->get_one($id);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $column_id = $action->getPrimaryKey();

        include 'edit.php';
    }

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    include 'error.php'; // файл для отображения ошибок
} catch (Exception $e) {
    $error = "Unexpected error: " . $e->getMessage();
    include 'error.php';
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $table_name = $_POST['table'];

        $hasforeign = $action->ConnectTable($id);
        if($hasforeign == true){
                $error = "Ошибка: На запись ссылаются в другой таблице";
                include 'error.php';
                exit;
        }
        $result = $action->delete($id);
        header('Location: controller.php?table='.$table_name.'&action=read');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
        $data = $_POST;
        unset($data['table']);
        unset($data['action']);
        $table_name = $_POST['table'];

        $foreignKeys = $action->getAllPrimaryKeyColumns();
        foreach ($foreignKeys as $key) {
            $foreignTable  = $key['referenced_table_name'];
            $foreignColumn = $key['referenced_column_name'];
            $value         = $data[$key['column_name']];
            if (!$action->checkForeignKey($foreignTable, $foreignColumn, $value)) {
                $error = "Таблица:". $foreignTable . "Не имеет заданных значений";
                include 'error.php'; // Вернуться к форме создания и показать ошибку
                exit; // Прервать выполнение скрипта
            }
        }

        $stmt = $action->create($data, $table_name);
        header('Location: controller.php?table='.$table_name.'&action=read');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
        $data = $_POST;
        $table_name = $_GET['table'];
        $id = $_GET['id'];

        $foreignKeys = $action->getAllPrimaryKeyColumns();
        foreach ($foreignKeys as $key) {
            $foreignTable  = $key['referenced_table_name'];
            $foreignColumn = $key['referenced_column_name'];
            $value         = $data[$key['column_name']];
            if (!$action->checkForeignKey($foreignTable, $foreignColumn, $value)) {
                $error = "Таблица:". $foreignTable . "Не имеет заданных значений";
                include 'error.php'; 
                exit;
            }
        }

        unset($data['action']);
        unset($data['table']);
        unset($data['id']);

        $stmt = $action->update($data, $id, $table_name);
        header('Location: controller.php?table='.$table_name.'&action=read');
        exit();
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    include 'error.php';
} catch (Exception $e) {
    $error = "Unexpected error: " . $e->getMessage();
    include 'error.php';
}
?>