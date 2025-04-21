<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="/styles/style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <div class="content">
        <?php echo htmlspecialchars($content); ?>
    </div>
</body>
</html>