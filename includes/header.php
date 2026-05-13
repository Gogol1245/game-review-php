<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Reviews - Recenzie hier a herné novinky</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: #1a1a2e; color: white; padding: 1rem 0; }
        nav { display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin: 0 10px; }
        nav a:hover { color: #e94560; }
        .games-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .game-card { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .game-card img { width: 100%; height: 200px; object-fit: cover; }
        .game-card-content { padding: 15px; }
        .game-card h3 { margin-bottom: 10px; }
        .btn { display: inline-block; padding: 8px 16px; background: #e94560; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #c73e54; }
        footer { background: #1a1a2e; color: white; text-align: center; padding: 1rem 0; margin-top: 40px; }
        .rating { color: #ffd700; font-weight: bold; }
        .admin-form { max-width: 600px; margin: 0 auto; }
        .admin-form input, .admin-form textarea, .admin-form select { width: 100%; padding: 8px; margin: 5px 0 15px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="/">Game Reviews</a>
                <div>
                    <a href="/">Domov</a>
                    <a href="/admin/">Administrácia</a>
                </div>
            </nav>
        </div>
    </header>
    <main class="container">