<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kodomo Framework' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">🎌 Kodomo Framework</a>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1 class="display-4">Welcome to Kodomo Framework</h1>
            <p class="lead"><?= $message ?? 'Your journey starts here!' ?></p>
            <a href="/about" class="btn btn-light btn-lg">Learn More</a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <h3>Fast</h3>
                <p>Lightning-fast performance with minimal overhead.</p>
            </div>
            <div class="col-md-4">
                <h3>Simple</h3>
                <p>Easy to learn and use with intuitive API.</p>
            </div>
            <div class="col-md-4">
                <h3>Powerful</h3>
                <p>All essential features for modern web development.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>