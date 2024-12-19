<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php if (isset($error)): ?>
        <div class="container" style="margin-top: 50px;">
            <div class="alert alert-danger d-flex flex-column align-items-center p-4 rounded" role="alert" style="max-width: 600px; margin: auto;">
                <h4 class="alert-heading text-center mb-3">An Error Occurred</h4>
                <p class="text-center mb-3"><?php echo htmlspecialchars($error); ?></p>
                <a href="controller.php?table=<?php echo htmlspecialchars($table); ?>&action=read" class="btn btn-outline-light mt-2" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Table
                </a>
            </div>
        </div>
    <?php endif; ?>
</body>
