<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UpTask | <?php echo $titulo ?? ''; ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="build/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="build/img/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Open+Sans&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="build/css/app.css">
</head>
<body>
    <?php echo $contenido; ?>
    <?php echo $script ?? ''; ?>
</body>
</html> 