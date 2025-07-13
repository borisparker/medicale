<?php
session_start();
require_once 'db.php';

// Redirection automatique si déjà connecté
if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['role'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin.php');
        exit();
    } elseif ($_SESSION['user']['role'] === 'docteur') {
        header('Location: docteur.php');
        exit();
    } elseif ($_SESSION['user']['role'] === 'patient') {
        header('Location: patient.php');
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $valid = password_verify($password, $user['mot_de_passe']);
            if ($valid) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'role' => $user['role']
                ];
                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } elseif ($user['role'] === 'docteur') {
                    header('Location: docteur.php');
                } else {
                    header('Location: patient.php');
                }
                exit();
            } else {
                $error = 'Mot de passe incorrect.';
            }
        } else {
            $error = 'Utilisateur non trouvé.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Vaidya Mitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="responsive.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a202c;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        body::before,
        body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        body::before {
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        body::after {
            bottom: -150px;
            right: -150px;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .main-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            min-height: 700px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .left-panel {
            flex: 1.2;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            color: #fff;
            text-align: center;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .welcome-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 450px;
            text-align: left;
            animation: fadeInLeft 1s ease-out 0.3s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .welcome-card h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 24px;
            line-height: 1.1;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-card p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        .welcome-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .welcome-card ul li {
            margin-bottom: 16px;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .welcome-card ul li i {
            margin-right: 12px;
            color: #fff;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            background: #fff;
        }

        .login-container {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            max-width: 450px;
            width: 100%;
            animation: fadeInRight 1s ease-out 0.5s both;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-container h2 {
            color: #1a202c;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 40px;
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.025em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        input[type="email"], 
        input[type="password"] {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            background: #f7fafc;
            transition: all 0.3s ease;
            color: #1a202c;
            font-family: 'Inter', sans-serif;
        }

        input[type="email"]::placeholder, 
        input[type="password"]::placeholder {
            color: #a0aec0;
            opacity: 1;
        }

        input[type="email"]:focus, 
        input[type="password"]:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: #fff;
        }

        input[type="email"]:focus + i, 
        input[type="password"]:focus + i {
            color: #667eea;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 0.95rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            color: #4a5568;
            font-weight: 500;
        }

        .checkbox-container input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .or-separator {
            text-align: center;
            margin: 32px 0;
            color: #a0aec0;
            position: relative;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .or-separator::before, 
        .or-separator::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 35%;
            height: 1px;
            background: #e2e8f0;
        }

        .or-separator::before {
            left: 0;
        }

        .or-separator::after {
            right: 0;
        }

        .btn-google {
            width: 100%;
            padding: 14px 0;
            background: #fff;
            color: #4a5568;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-google:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }

        .btn-google img {
            margin-right: 12px;
            width: 20px;
            height: 20px;
        }

        .signup-link {
            text-align: center;
            margin-top: 32px;
            font-size: 1rem;
            color: #4a5568;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        .error {
            color: #e53e3e;
            background: #fed7d7;
            border: 1px solid #feb2b2;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-retour-accueil {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin: 32px 0 0 0;
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 0;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-retour-accueil:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            color: #2d3748;
            transform: translateY(-1px);
        }

        .btn-retour-accueil svg {
            width: 18px;
            height: 18px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-container {
                max-width: 900px;
                margin: 20px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .main-container {
                flex-direction: column;
                max-width: 500px;
                min-height: auto;
            }

            .left-panel {
                padding: 40px 30px;
                border-radius: 24px 24px 0 0;
            }

            .right-panel {
                padding: 40px 30px;
            }

            .welcome-card {
                max-width: none;
                padding: 30px;
            }

            .welcome-card h1 {
                font-size: 2.2rem;
            }

            .login-container {
                padding: 40px 30px;
            }

            .login-container h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                margin: 10px;
                border-radius: 16px;
            }

            .left-panel,
            .right-panel {
                padding: 30px 20px;
            }

            .welcome-card {
                padding: 25px;
            }

            .welcome-card h1 {
                font-size: 1.8rem;
            }

            .login-container {
                padding: 30px 20px;
            }
            
            /* Améliorations pour très petits écrans */
            input[type="email"], 
            input[type="password"] {
                padding: 14px 14px 14px 44px;
                font-size: 16px; /* Évite le zoom sur iOS */
            }
            
            .btn-login,
            .btn-google,
            .btn-retour-accueil {
                padding: 14px 0;
                font-size: 1rem;
            }
            
            .form-options {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .welcome-card ul li {
                font-size: 0.9rem;
            }
        }
        
        /* Améliorations pour l'accessibilité mobile */
        @media (max-width: 768px) {
            /* Augmenter la taille des éléments cliquables */
            .btn-login,
            .btn-google,
            .btn-retour-accueil,
            input[type="email"],
            input[type="password"] {
                min-height: 44px;
            }
            
            /* Améliorer l'espacement pour le touch */
            .form-group {
                margin-bottom: 20px;
            }
            
            /* Améliorer la lisibilité */
            .welcome-card p {
                font-size: 1rem;
            }
        }
        
        /* Améliorations pour les écrans tactiles */
        @media (hover: none) and (pointer: coarse) {
            .btn-login:hover,
            .btn-google:hover,
            .btn-retour-accueil:hover {
                transform: none;
            }
            
            .btn-login:active,
            .btn-google:active,
            .btn-retour-accueil:active {
                transform: scale(0.98);
            }
        }

            .login-container h2 {
                font-size: 1.6rem;
            }

            .form-options {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }

        /* Loading animation for button */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="welcome-card">
                <h1>Bienvenue sur<br/>Vaidya Mitra</h1>
                <p>Connectez-vous pour accéder à votre espace professionnel et gérer votre clinique en toute simplicité.</p>
                <ul>
                    <li><i class="fas fa-calendar-check"></i> Gestion complète des rendez-vous</li>
                    <li><i class="fas fa-user-shield"></i> Dossiers patients sécurisés</li>
                    <li><i class="fas fa-users"></i> Outils de collaboration</li>
                    <li><i class="fas fa-chart-line"></i> Rapports et analyses</li>
                </ul>
            </div>
        </div>
        <div class="right-panel">
            <div class="login-container">
                <h2>Connectez-vous à votre<br/>compte</h2>
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" autocomplete="off" id="loginForm">
                    <div class="form-group">
                        <label for="email">Adresse email professionnelle</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="votre@email.com" required autofocus>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember_me"> Se souvenir de moi
                        </label>
                        <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                    </div>
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">Se connecter</span>
                    </button>
                    <div class="or-separator">OU CONTINUER AVEC</div>
                    <button type="button" class="btn-google">
                        <img src="https://img.icons8.com/color/20/000000/google-logo.png" alt="Google icon"> Continuer avec Google
                    </button>
                    <p class="signup-link">Nouveau sur Vaidya Mitra ? <a href="register.php">Créer un compte</a></p>
                </form>
                <a href="index.php" class="btn-retour-accueil">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation to login button
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            
            btn.classList.add('loading');
            btnText.style.opacity = '0';
            
            // Re-enable after 3 seconds if no redirect
            setTimeout(() => {
                btn.classList.remove('loading');
                btnText.style.opacity = '1';
            }, 3000);
        });

        // Add focus effects to inputs
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
</body>
</html> 