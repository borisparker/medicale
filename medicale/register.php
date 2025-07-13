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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';
    $confirmer_motdepasse = $_POST['confirmer_motdepasse'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? null;
    $sexe = $_POST['sexe'] ?? null;
    $telephone = $_POST['telephone'] ?? null;
    $groupe_sanguin = $_POST['groupe_sanguin'] ?? null;
    $allergies = $_POST['allergies'] ?? null;

    // Validation
    if (!$nom || !$prenom || !$email || !$motdepasse || !$confirmer_motdepasse) {
        $error = 'Tous les champs obligatoires doivent être remplis.';
    } elseif ($motdepasse !== $confirmer_motdepasse) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($motdepasse) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            // Hash du mot de passe
            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

            // Gestion de la photo
            $photo_path = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $filename = 'photo_patient_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $dest = 'uploads/' . $filename;
                if (!is_dir('uploads')) mkdir('uploads');
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                    $photo_path = $dest;
                }
            }

            try {
                // 1. Insertion dans users
                $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, role, date_naissance, sexe, telephone, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$nom, $prenom, $email, $hash, 'patient', $date_naissance, $sexe, $telephone, $photo_path]);
                $user_id = $pdo->lastInsertId();

                // 2. Insertion dans patients
                $stmt2 = $pdo->prepare('INSERT INTO patients (user_id, groupe_sanguin, allergies, statut) VALUES (?, ?, ?, ?)');
                $stmt2->execute([$user_id, $groupe_sanguin, $allergies, 'actif']);

                $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            } catch(Exception $e) {
                $error = 'Erreur lors de la création du compte : ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Vaidya mitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa 0%, #ffffff 100%);
            font-family: 'Roboto', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #333;
            flex-direction: column;
        }
        .main-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 151, 167, 0.10);
            overflow: hidden;
            min-height: 700px;
        }
        .left-panel {
            flex: 1;
            background: url('assets/images/medical-bg.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            color: #fff;
            text-align: center;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 54, 93, 0.7);
            border-radius: 20px;
        }
        .welcome-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.18);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            max-width: 400px;
            text-align: left;
        }
        .welcome-card h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
            color: #fff;
        }
        .welcome-card p {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #e0f7fa;
        }
        .welcome-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .welcome-card ul li {
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: #fff;
        }
        .welcome-card ul li i {
            margin-right: 10px;
            color: #00bcd4;
        }
        .right-panel {
            flex: 1.5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
        }
        .register-container {
            background: #fff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0,151,167,0.08);
            max-width: 600px;
            width: 100%;
        }
        .register-container h2 {
            color: #1a365d;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 30px;
            text-align: center;
            font-size: 1.8rem;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            flex: 1;
            margin-bottom: 20px;
        }
        .form-group.full-width {
            flex: none;
            width: 100%;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #0097a7;
            font-weight: 500;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        .required {
            color: #d32f2f;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="tel"], select, textarea {
            width: 100%;
            padding: 14px 12px;
            border: 2px solid #0097a7;
            border-radius: 14px;
            font-size: 1.05rem;
            background: #e0f7fa;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            color: #1a365d;
            box-sizing: border-box;
        }
        input::placeholder, textarea::placeholder {
            color: #4dd0e1;
            opacity: 1;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #00bcd4;
            outline: none;
            box-shadow: 0 0 0 2px #b2ebf2;
            background: #fff;
        }
        .file-input-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 12px;
            border: 2px dashed #0097a7;
            border-radius: 14px;
            background: #e0f7fa;
            cursor: pointer;
            transition: all 0.2s;
            color: #0097a7;
            font-weight: 500;
        }
        .file-input-label:hover {
            border-color: #00bcd4;
            background: #b2ebf2;
        }
        .btn-register {
            width: 100%;
            padding: 14px 0;
            background: #1a365d;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.15rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(0,151,167,0.15);
            letter-spacing: 0.5px;
            margin-top: 20px;
        }
        .btn-register:hover {
            background: #0097a7;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 8px 24px rgba(0,151,167,0.18);
        }
        .success {
            color: #2e7d32;
            background: #e8f5e8;
            border: 2px solid #a5d6a7;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 1rem;
        }
        .error {
            color: #d32f2f;
            background: #ffebee;
            border: 2px solid #ffcdd2;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 1rem;
            color: #555;
        }
        .login-link a {
            color: #0097a7;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
            color: #00bcd4;
        }
        .btn-retour-accueil {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin: 28px 0 0 0;
            background: #e0f7fa;
            color: #0097a7;
            border: 2px solid #0097a7;
            border-radius: 12px;
            padding: 14px 0;
            font-size: 1.12rem;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(0,151,167,0.10);
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s, transform 0.2s;
            text-decoration: none;
        }
        .btn-retour-accueil:hover {
            background: #0097a7;
            color: #fff;
            border-color: #007c91;
            box-shadow: 0 8px 24px rgba(0,151,167,0.18);
            transform: translateY(-2px) scale(1.04);
        }
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                max-width: 400px;
            }
            .left-panel {
                padding: 30px;
                border-radius: 20px 20px 0 0;
            }
            .right-panel {
                padding: 30px;
            }
            .welcome-card {
                max-width: none;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .btn-retour-accueil {
                font-size: 1rem;
                padding: 12px 0;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="welcome-card">
                <h1>Rejoignez<br/>Vaidya mitra</h1>
                <p>Créez votre compte patient pour accéder à vos soins médicaux en toute simplicité.</p>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Accès à vos dossiers médicaux</li>
                    <li><i class="fas fa-check-circle"></i> Prise de rendez-vous en ligne</li>
                    <li><i class="fas fa-check-circle"></i> Consultation de vos prescriptions</li>
                    <li><i class="fas fa-check-circle"></i> Communication avec votre médecin</li>
                </ul>
            </div>
        </div>
        <div class="right-panel">
            <div class="register-container">
                <h2>Créer votre compte patient</h2>
                <?php if ($success): ?>
                    <div class="success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" autocomplete="off">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Adresse email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="motdepasse">Mot de passe <span class="required">*</span></label>
                            <input type="password" id="motdepasse" name="motdepasse" placeholder="******" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmer_motdepasse">Confirmer le mot de passe <span class="required">*</span></label>
                            <input type="password" id="confirmer_motdepasse" name="confirmer_motdepasse" placeholder="******" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="sexe">Sexe</label>
                            <select id="sexe" name="sexe">
                                <option value="">Sélectionner</option>
                                <option value="M" <?= ($_POST['sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                                <option value="F" <?= ($_POST['sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="Votre numéro de téléphone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="groupe_sanguin">Groupe sanguin</label>
                            <select id="groupe_sanguin" name="groupe_sanguin">
                                <option value="">Sélectionner</option>
                                <option value="A+" <?= ($_POST['groupe_sanguin'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= ($_POST['groupe_sanguin'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= ($_POST['groupe_sanguin'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= ($_POST['groupe_sanguin'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= ($_POST['groupe_sanguin'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= ($_POST['groupe_sanguin'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= ($_POST['groupe_sanguin'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= ($_POST['groupe_sanguin'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo de profil</label>
                            <div class="file-input-container">
                                <input type="file" id="photo" name="photo" class="file-input" accept="image/*">
                                <label for="photo" class="file-input-label">
                                    <i class="fas fa-upload"></i> Choisir une photo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="allergies">Allergies (optionnel)</label>
                        <textarea id="allergies" name="allergies" placeholder="Listez vos allergies connues..." rows="3"><?= htmlspecialchars($_POST['allergies'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-register">Créer mon compte</button>
                    <p class="login-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>
                </form>
                <a href="index.php" class="btn-retour-accueil">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
    <script>
        // Afficher le nom du fichier sélectionné
        document.getElementById('photo').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const label = document.querySelector('.file-input-label');
            if (fileName) {
                label.innerHTML = `<i class="fas fa-check"></i> ${fileName}`;
            } else {
                label.innerHTML = `<i class="fas fa-upload"></i> Choisir une photo`;
            }
        });
    </script>
</body>
</html> 