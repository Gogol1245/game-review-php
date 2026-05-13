<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Reviews</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: #1a1a2e; color: white; padding: 1rem 0; }
        nav { display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin: 0 10px; font-size: 16px; }
        nav a:hover { color: #e94560; }
        .games-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .game-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .game-card img { width: 100%; height: 200px; object-fit: cover; }
        .game-card-content { padding: 15px; }
        .game-card h3 { margin-bottom: 10px; }
        .game-card h3 a { color: #1a1a2e; text-decoration: none; }
        .game-card h3 a:hover { color: #e94560; }
        .btn { display: inline-block; padding: 8px 16px; background: #e94560; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin: 5px; }
        .btn:hover { background: #c73e54; }
        footer { background: #1a1a2e; color: white; text-align: center; padding: 1rem 0; margin-top: 40px; }
        .rating { color: #ffd700; font-weight: bold; }
        .admin-form { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .admin-form input, .admin-form textarea, .admin-form select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 4px; }
        .admin-form label { font-weight: bold; display: block; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        table th { background: #1a1a2e; color: white; }
        table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        h1, h2 { margin: 20px 0; color: #1a1a2e; }
        section { margin: 40px 0; }
        .content-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="/game-review-site/index.php">🎮 Game Reviews</a>
                <div>
                    <a href="/game-review-site/index.php">🏠 Domov</a>
                    <a href="/game-review-site/admin/index.php">🔑 Administrácia</a>
                </div>
            </nav>
        </div>
    </header>
    <main class="container">