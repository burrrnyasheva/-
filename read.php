<!DOCTYPE html>
<html>
<head>
    <title>Read</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'head.php'; ?>
    <div class="container mt-5">
        <h2>Данные таблицы</h2>
        <?php include 'error.php'; ?>
        <div class="mb-3">
            <a href="/laba/controller.php?table=<?php echo $_GET['table']; ?>&action=create" class="btn btn-success">Добавить</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php if (!empty($rows)): ?>
                        <?php foreach (array_keys($rows[0]) as $header): ?>
                            <th><?php echo ucfirst($header); ?></th>
                        <?php endforeach; ?>
                        <th>Действия</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <a href="/laba/controller.php?table=<?php echo $_GET['table']; ?>&action=update&id=<?php echo $row[$column_id]; ?>" class="btn btn-warning btn-sm">Редактировать</a>
                                <form action="/laba/controller.php?table=<?php echo $_GET['table']?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $row[$column_id]; ?>">
                                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="100%">Нет данных для отображения.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
