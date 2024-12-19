<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить запись</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php include 'head.php'; ?>
        <h2>Добавить запись в таблицу: <?php echo ucfirst($table_name); ?></h2>
        <?php include 'error.php'; ?>
        <form action="/laba/controller.php" method="POST">
            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table_name); ?>">
            <?php foreach ($columns as $column): ?>
                <?php if ($column['Field'] !== $column_id): ?> <!-- Проверка, чтобы пропустить поле ID -->
                    <div class="form-group">
                        <label for="<?php echo htmlspecialchars($column['Field']); ?>">
                            <?php echo ucfirst($column['Field']); ?>
                        </label>
                        <?php
                        // Генерация полей на основе типа данных
                        switch ($column['Type']) {
                            case 'int':
                            case 'bigint':
                            case 'tinyint':
                                echo '<input type="number" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                            case 'varchar':
                            case 'text':
                                echo '<input type="text" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                            case strpos($column['Type'], 'date') !== false:
                                echo '<input type="date" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                            case 'datetime':
                                echo '<input type="datetime-local" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                            case 'float':
                            case 'double':
                                echo '<input type="number" step="0.01" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                            default:
                                echo '<input type="text" class="form-control" id="'.htmlspecialchars($column['Field']).'" name="'.htmlspecialchars($column['Field']).'" required>';
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" name="action" value="create" class="btn btn-primary">Добавить запись</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
