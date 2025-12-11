<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - O'Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #1f2937;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #bf3a29;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 4px 4px 0px rgba(191, 58, 41, 0.1);
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .error-message {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .btn-home {
            background-color: #bf3a29;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(191, 58, 41, 0.3);
        }

        .btn-home:hover {
            background-color: #d64a39;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(191, 58, 41, 0.4);
            color: white;
        }

        .logo-img {
            height: 60px;
            margin-bottom: 2rem;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <img src="{{ asset('images/biuma_logo_b.PNG') }}" alt="O'Menu Logo" class="logo-img">
        <div class="error-code">404</div>
        <h1 class="error-title">Oups ! Page introuvable</h1>
        <p class="error-message">
            La page que vous recherchez semble avoir été déplacée, supprimée ou n'a jamais existé.
        </p>
        <a href="/" class="btn-home">Retour à l'accueil</a>
    </div>
</body>

</html>