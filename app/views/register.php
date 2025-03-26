<!-- app/views/register.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Financière</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/bootstrap.min.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2 class="text-center">Inscription</h2>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($error as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo BASE_URL; ?>/register">
                <div class="form-group">
                    <label for="nom_utilisateur">Nom du client</label>
                    <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            </form>
            <p class="text-center mt-3">
                Déjà un compte ? <a href="<?php echo BASE_URL; ?>/login">Se connecter</a>
            </p>
        </div>
    </div>
</body>
</html>