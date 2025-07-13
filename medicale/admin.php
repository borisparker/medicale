<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

// Récupérer les infos de l'admin connecté
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT nom, prenom, photo, email, telephone FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
$nom = $user_info['nom'] ?? '';
$prenom = $user_info['prenom'] ?? '';
$email = $user_info['email'] ?? '';
$telephone = $user_info['telephone'] ?? '';

// Gestion améliorée de la photo avec vérification plus robuste
$photo_url = 'https://ui-avatars.com/api/?name=' . urlencode($prenom . '+' . $nom) . '&background=6366f1&color=fff&size=128&font-size=0.4&bold=true'; // Image par défaut avec initiales
if (!empty($user_info['photo'])) {
    $photo_file = basename($user_info['photo']);
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/medicale/uploads/' . $photo_file;
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/uploads/' . $photo_file;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Gestion Clinique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dossiers_medicaux.css">
    <link rel="stylesheet" href="admin-responsive.css">
    <link rel="stylesheet" href="admin-sections-responsive.css">
    <script src="admin_settings_improved.js"></script>
    <style>
      .modal-bg { background: rgba(0,0,0,0.4); }
      .modal-gradient { background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%); }
      
      /* Masquer la barre de défilement */
      .overflow-y-auto::-webkit-scrollbar {
        display: none;
      }
      .overflow-y-auto {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }
      
      /* Animations améliorées */
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
      }
      
      @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
      }
      
      @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
      }
      
      @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
      }
      
      @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
      }
      
      .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
      }
      
      .animate-slideInLeft {
        animation: slideInLeft 0.5s ease-out;
      }
      
      .animate-slideInRight {
        animation: slideInRight 0.5s ease-out;
      }
      
      .animate-scaleIn {
        animation: scaleIn 0.4s ease-out;
      }
      
      .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }
      
      .animate-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
      }
      
      /* Effets de survol améliorés */
      .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
      
      .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }
      
      /* Ombres dynamiques */
      .shadow-dynamic {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
      }
      
      .shadow-dynamic:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      }
      
      /* Gradients améliorés */
      .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }
      
      .gradient-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      }
      
      .gradient-success {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      }
      
      .gradient-warning {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
      }
      
      .gradient-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      }
      
      /* Effets de glassmorphism */
      .glass {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
      }
      
      /* Bordures animées */
      .border-animated {
        position: relative;
        overflow: hidden;
      }
      
      .border-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
        transition: left 0.5s;
      }
      
      .border-animated:hover::before {
        left: 100%;
      }
      
      /* Indicateurs de statut animés */
      .status-indicator {
        position: relative;
      }
      
      .status-indicator::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse 2s infinite;
      }
      
      /* Cards avec effet de profondeur */
      .card-3d {
        transform-style: preserve-3d;
        transition: transform 0.3s ease;
      }
      
      .card-3d:hover {
        transform: rotateY(5deg) rotateX(5deg);
      }
      
      /* Boutons avec effets */
      .btn-modern {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
      }
      
      .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
      }
      
      .btn-modern:hover::before {
        left: 100%;
      }
      
      /* Sidebar améliorée */
      .sidebar-link {
        position: relative;
        overflow: hidden;
      }
      
      .sidebar-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
      }
      
      .sidebar-link:hover::before {
        left: 100%;
      }
      
      /* Loading states */
      .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
      }
      
      /* Responsive improvements */
      @media (max-width: 768px) {
        .stats-card {
          margin-bottom: 1rem;
        }
        
        /* Améliorations pour mobile */
        .mobile-card {
          margin-bottom: 1rem;
          padding: 1rem;
        }
        
        .mobile-table {
          font-size: 0.875rem;
        }
        
        .mobile-table th,
        .mobile-table td {
          padding: 0.5rem 0.25rem;
        }
        
        /* Grille responsive pour les statistiques */
        .stats-grid {
          grid-template-columns: 1fr;
          gap: 1rem;
        }
        
        /* Ajustements pour les formulaires */
        .mobile-form {
          padding: 1rem;
        }
        
        .mobile-form input,
        .mobile-form select,
        .mobile-form textarea {
          font-size: 16px; /* Évite le zoom sur iOS */
        }
        
        /* Boutons plus grands sur mobile */
        .mobile-btn {
          padding: 0.75rem 1rem;
          font-size: 1rem;
        }
        
              /* Sidebar mobile */
      #sidebar {
        width: 280px;
        z-index: 50;
      }
      
      /* S'assurer que la sidebar est bien visible quand elle est ouverte */
      #sidebar:not(.md\\:translate-x-0) {
        transform: translateX(-100%);
      }
      
      /* Animation pour l'ouverture/fermeture */
      #sidebar {
        transition: transform 0.3s ease-in-out;
      }
        
        /* Overlay mobile */
        #mobile-overlay {
          backdrop-filter: blur(2px);
        }
      }
      
      /* Tablettes */
      @media (min-width: 769px) and (max-width: 1024px) {
        .stats-grid {
          grid-template-columns: repeat(2, 1fr);
          gap: 1.5rem;
        }
        
        #sidebar {
          width: 240px;
        }
      }
      
      /* Scrollbar personnalisée */
      ::-webkit-scrollbar {
        width: 6px;
      }
      
      ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
      }
      
      ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
      }
      
      ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
      }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-shrink-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col w-64 min-h-screen bg-gradient-to-b from-indigo-800 via-indigo-700 to-indigo-900 shadow-2xl transition-all duration-500 ease-in-out">
                <div class="flex items-center justify-center h-20 px-4 bg-indigo-900 shadow-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-heartbeat text-3xl animate-pulse text-pink-400 drop-shadow-lg"></i>
                        <span class="text-2xl font-extrabold tracking-wide text-white">Vaidya Mitra</span>
                    </div>
                </div>
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-400 rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Tableau de bord</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-injured mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Patients</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-purple-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Dossiers médicaux</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-yellow-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-pills mr-3 text-xl group-hover:scale-125 group-hover:text-yellow-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médicaments</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-300 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-md mr-3 text-xl group-hover:scale-125 group-hover:text-pink-300 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médecins</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-red-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-chart-bar mr-3 text-xl group-hover:scale-125 group-hover:text-red-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Statistiques</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-cog mr-3 text-xl group-hover:scale-125 group-hover:text-indigo-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Paramètres</span>
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-indigo-700 bg-indigo-900 shadow-inner animate-fadeIn">
                    <div class="flex items-center">
                        <div class="relative group">
                            <img class="w-12 h-12 rounded-full border-2 border-pink-400 shadow-lg transition-all duration-300 hover:scale-110 hover:border-pink-300" 
                                 src="<?php echo htmlspecialchars($photo_url); ?>" 
                                 alt="Photo de profil"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($prenom . '+' . $nom); ?>&background=6366f1&color=fff&size=128&font-size=0.4&bold=true'">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-bold text-white"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                            <p class="text-xs text-indigo-200">Administrateur</p>
                            <a href="logout.php" class="block mt-2 text-xs text-pink-300 hover:text-pink-500 font-semibold transition-all duration-300 hover:scale-105"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200">
                <div class="flex items-center md:hidden">
                    <button id="hamburger-menu" class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100" onclick="console.log('Hamburger clicked')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <button onclick="testHamburger()" class="ml-2 text-xs bg-blue-500 text-white px-2 py-1 rounded">Test</button>
                </div>
                <div class="flex-1 max-w-md ml-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="w-full py-2 pl-10 pr-4 text-sm bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="text-gray-500 focus:outline-none">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <div class="relative">
                        <button class="flex items-center focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-indigo-200 shadow-sm" 
                                 src="<?php echo htmlspecialchars($photo_url); ?>" 
                                 alt="Profile"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($prenom . '+' . $nom); ?>&background=6366f1&color=fff&size=128&font-size=0.4'">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6">
                <!-- Le contenu de chaque page sera injecté ici -->
                <div id="page-content"></div>
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <div id="patientModal" class="fixed inset-0 z-50 hidden modal-bg flex items-center justify-center">
      <div class="modal-gradient bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 relative overflow-hidden">
        <div class="flex items-center gap-2 px-6 pt-6 pb-2 border-b border-indigo-100">
          <span class="bg-indigo-100 text-indigo-600 rounded-full p-2"><i class="fas fa-user-plus fa-lg"></i></span>
          <h3 class="text-xl font-bold text-indigo-800">Ajouter un patient</h3>
          <button class="ml-auto text-gray-400 hover:text-indigo-600 text-2xl font-bold" id="closePatientModal">&times;</button>
        </div>
        <form class="px-6 py-4" id="form-ajout-patient">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-user"></i> Identité</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="nom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="prenom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-envelope"></i> Compte</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-envelope"></i></span>
                  <input type="email" name="email" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-lock"></i></span>
                  <input type="password" name="motdepasse" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Photo</label>
                <input type="file" name="photo" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-id-card"></i> Personnel</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Date de naissance</label>
                <input type="date" name="date_naissance" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Sexe</label>
                <select name="sexe" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="">-- Sélectionner --</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                  <option value="Autre">Autre</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Téléphone</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-phone"></i></span>
                  <input type="tel" name="telephone" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-heartbeat"></i> Médical</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Groupe sanguin</label>
                <select name="groupe_sanguin" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="">-- Sélectionner --</option>
                  <option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Allergies</label>
                <input type="text" name="allergies" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Statut</label>
                <select name="statut" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="actif">Actif</option>
                  <option value="inactif">Inactif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="flex justify-end mt-6 gap-2">
            <button type="button" id="closePatientModal2" class="bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition text-sm">Annuler</button>
            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white font-bold px-5 py-1.5 rounded-lg shadow-lg transition flex items-center gap-2 text-sm"><i class="fas fa-check"></i> Ajouter</button>
          </div>
        </form>
      </div>
    </div>

    <div id="doctorModal" class="fixed inset-0 z-50 hidden modal-bg flex items-center justify-center">
      <div class="modal-gradient bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 relative overflow-hidden">
        <div class="flex items-center gap-2 px-6 pt-6 pb-2 border-b border-indigo-100">
          <span class="bg-indigo-100 text-indigo-600 rounded-full p-2"><i class="fas fa-user-md fa-lg"></i></span>
          <h3 class="text-xl font-bold text-indigo-800">Ajouter un médecin</h3>
          <button class="ml-auto text-gray-400 hover:text-indigo-600 text-2xl font-bold" id="closeDoctorModal">&times;</button>
        </div>
        <form class="px-6 py-4" id="form-ajout-docteur">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-user"></i> Identité</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="nom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="prenom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-envelope"></i> Compte</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-envelope"></i></span>
                  <input type="email" name="email" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-lock"></i></span>
                  <input type="password" name="motdepasse" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Photo</label>
                <input type="file" name="photo" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-id-card"></i> Personnel</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Date de naissance</label>
                <input type="date" name="date_naissance" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Sexe</label>
                <select name="sexe" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
          <option value="">-- Sélectionner --</option>
          <option value="M">Masculin</option>
          <option value="F">Féminin</option>
          <option value="Autre">Autre</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-indigo-700 mb-1">Téléphone</label>
        <div class="relative">
          <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-phone"></i></span>
          <input type="tel" name="telephone" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
        </div>
      </div>
    </div>
    <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
      <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-stethoscope"></i> Professionnel</h4>
      <div>
        <label class="block text-xs font-semibold text-indigo-700 mb-1">Spécialité</label>
        <input type="text" name="specialite" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-indigo-700 mb-1">Disponibilité</label>
        <input type="text" name="disponibilite" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm" placeholder="Ex: Lun-Ven, 8h-18h">
      </div>
    </div>
  </div>
  <div class="flex justify-end mt-6 gap-2">
    <button type="button" id="closeDoctorModal2" class="bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition text-sm">Annuler</button>
    <button type="submit" class="bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white font-bold px-5 py-1.5 rounded-lg shadow-lg transition flex items-center gap-2 text-sm"><i class="fas fa-check"></i> Ajouter</button>
  </div>
</form>
      </div>
    </div>

    <!-- Modale d'ajout de médicament -->
    <div id="medicamentModal" class="fixed inset-0 z-50 hidden modal-bg flex items-center justify-center">
      <div class="modal-gradient bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 relative overflow-hidden">
        <div class="flex items-center gap-2 px-6 pt-6 pb-2 border-b border-indigo-100">
          <span class="bg-indigo-100 text-indigo-600 rounded-full p-2"><i class="fas fa-pills fa-lg"></i></span>
          <h3 class="text-xl font-bold text-indigo-800">Ajouter un médicament</h3>
          <button class="ml-auto text-gray-400 hover:text-indigo-600 text-2xl font-bold" id="closeMedicamentModal">&times;</button>
        </div>
        <form class="px-6 py-4" id="form-ajout-medicament">
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">Nom <span class="text-red-500">*</span></label>
              <input type="text" name="nom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm" required>
            </div>
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">DCI</label>
              <input type="text" name="dci" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">Forme</label>
              <select name="forme" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                <option value="">-- Sélectionner --</option>
                <option>Comprimé</option>
                <option>Gélule</option>
                <option>Spray</option>
                <option>Sirop</option>
                <option>Pommade</option>
                <option>Suppositoire</option>
                <option>Solution injectable</option>
                <option>Patch</option>
                <option>Autre</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">Dosage</label>
              <input type="text" name="dosage" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm" placeholder="Ex: 500 mg, 10 mg, 100 µg/dose...">
            </div>
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">Stock</label>
              <input type="number" name="stock" min="0" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm" value="0">
            </div>
            <div>
              <label class="block text-xs font-semibold text-indigo-700 mb-1">Catégorie</label>
              <select name="categorie" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                <option value="">-- Sélectionner --</option>
                <option>Antibiotique</option>
                <option>Antihypertenseur</option>
                <option>Antidouleur</option>
                <option>Antidiabétique</option>
                <option>Psychotrope</option>
                <option>Bronchodilatateur</option>
                <option>Anti-inflammatoire</option>
                <option>Vitamines</option>
                <option>Autre</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end mt-6 gap-2">
            <button type="button" id="closeMedicamentModal2" class="bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition text-sm">Annuler</button>
            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white font-bold px-5 py-1.5 rounded-lg shadow-lg transition flex items-center gap-2 text-sm"><i class="fas fa-check"></i> Ajouter</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modale d'édition de médecin -->
    <div id="editDoctorModal" class="fixed inset-0 z-50 hidden modal-bg flex items-center justify-center">
      <div class="modal-gradient bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 relative overflow-hidden">
        <div class="flex items-center gap-2 px-6 pt-6 pb-2 border-b border-indigo-100">
          <span class="bg-indigo-100 text-indigo-600 rounded-full p-2"><i class="fas fa-user-md fa-lg"></i></span>
          <h3 class="text-xl font-bold text-indigo-800">Éditer un médecin</h3>
          <button class="ml-auto text-gray-400 hover:text-indigo-600 text-2xl font-bold" id="closeEditDoctorModal">&times;</button>
        </div>
        <form class="px-6 py-4" id="form-edit-docteur">
          <input type="hidden" name="doctor_id" id="edit-doctor-id">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-user"></i> Identité</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="nom" id="edit-nom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="prenom" id="edit-prenom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-envelope"></i> Compte</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-envelope"></i></span>
                  <input type="email" name="email" id="edit-email" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Mot de passe (laisser vide pour ne pas changer)</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-lock"></i></span>
                  <input type="password" name="motdepasse" id="edit-motdepasse" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Photo</label>
                <input type="file" name="photo" id="edit-photo" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-id-card"></i> Personnel</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Date de naissance</label>
                <input type="date" name="date_naissance" id="edit-date_naissance" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Sexe</label>
                <select name="sexe" id="edit-sexe" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="">-- Sélectionner --</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                  <option value="Autre">Autre</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Téléphone</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-phone"></i></span>
                  <input type="tel" name="telephone" id="edit-telephone" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-stethoscope"></i> Professionnel</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Spécialité</label>
                <input type="text" name="specialite" id="edit-specialite" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Disponibilité</label>
                <input type="text" name="disponibilite" id="edit-disponibilite" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm" placeholder="Ex: Lun-Ven, 8h-18h">
              </div>
            </div>
          </div>
          <div class="flex justify-end mt-6 gap-2">
            <button type="button" id="closeEditDoctorModal2" class="bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition text-sm">Annuler</button>
            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white font-bold px-5 py-1.5 rounded-lg shadow-lg transition flex items-center gap-2 text-sm"><i class="fas fa-check"></i> Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <div id="editPatientModal" class="fixed inset-0 z-50 hidden modal-bg flex items-center justify-center">
      <div class="modal-gradient bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 relative overflow-hidden">
        <div class="flex items-center gap-2 px-6 pt-6 pb-2 border-b border-indigo-100">
          <span class="bg-indigo-100 text-indigo-600 rounded-full p-2"><i class="fas fa-user-edit fa-lg"></i></span>
          <h3 class="text-xl font-bold text-indigo-800">Éditer un patient</h3>
          <button class="ml-auto text-gray-400 hover:text-indigo-600 text-2xl font-bold" id="closeEditPatientModal">&times;</button>
        </div>
        <form class="px-6 py-4" id="form-edit-patient">
          <input type="hidden" name="patient_id" id="edit-patient-id">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-user"></i> Identité</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="nom" id="edit-patient-nom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-user"></i></span>
                  <input type="text" name="prenom" id="edit-patient-prenom" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-envelope"></i> Compte</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-envelope"></i></span>
                  <input type="email" name="email" id="edit-patient-email" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm" required>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Mot de passe (laisser vide pour ne pas changer)</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-lock"></i></span>
                  <input type="password" name="motdepasse" id="edit-patient-motdepasse" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Photo</label>
                <input type="file" name="photo" id="edit-patient-photo" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-id-card"></i> Personnel</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Date de naissance</label>
                <input type="date" name="date_naissance" id="edit-patient-date_naissance" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Sexe</label>
                <select name="sexe" id="edit-patient-sexe" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="">-- Sélectionner --</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                  <option value="Autre">Autre</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Téléphone</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-indigo-400 text-sm"><i class="fas fa-phone"></i></span>
                  <input type="tel" name="telephone" id="edit-patient-telephone" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg pl-8 py-1.5 text-sm">
                </div>
              </div>
            </div>
            <div class="bg-white/80 rounded-xl shadow p-3 space-y-2 border border-indigo-50 flex flex-col justify-between">
              <h4 class="text-base font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="fas fa-heartbeat"></i> Médical</h4>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Groupe sanguin</label>
                <select name="groupe_sanguin" id="edit-patient-groupe_sanguin" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="">-- Sélectionner --</option>
                  <option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Allergies</label>
                <input type="text" name="allergies" id="edit-patient-allergies" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-indigo-700 mb-1">Statut</label>
                <select name="statut" id="edit-patient-statut" class="w-full border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-lg py-1.5 text-sm">
                  <option value="actif">Actif</option>
                  <option value="inactif">Inactif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="flex justify-end mt-6 gap-2">
            <button type="button" id="closeEditPatientModal2" class="bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition text-sm">Annuler</button>
            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 text-white font-bold px-5 py-1.5 rounded-lg shadow-lg transition flex items-center gap-2 text-sm"><i class="fas fa-check"></i> Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <script>
        // JavaScript pour la navigation et le chargement dynamique des pages
        document.addEventListener('DOMContentLoaded', function() {
            // Charger le tableau de bord par défaut
            loadDashboard();
            
            // Gestion des clics sur les liens de navigation
            document.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.querySelector('i').className.match(/fa-([a-z-]+)/)[1];
                    
                    // Retirer la classe active de tous les liens
                    document.querySelectorAll('nav a').forEach(el => {
                        el.classList.remove('bg-indigo-600', 'text-white');
                        el.classList.add('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                    });
                    
                    // Ajouter la classe active au lien cliqué
                    this.classList.add('bg-indigo-600', 'text-white');
                    this.classList.remove('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                    
                    // Charger la page correspondante
                    switch(page) {
                        case 'tachometer-alt':
                            loadDashboard();
                            break;
                        case 'calendar-alt':
                            loadAppointments();
                            break;
                        case 'user-injured':
                            loadPatients();
                            break;
                        case 'file-medical':
                            loadMedicalRecords();
                            break;
                        case 'pills':
                            loadMedications();
                            break;
                        case 'user-md':
                            loadDoctors();
                            break;
                        case 'chart-bar':
                            loadStatistics();
                            break;
                        case 'cog':
                            loadSettings();
                            break;
                    }
                });
            });
            
            function loadDashboard() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2 animate-fadeInUp">Tableau de bord</h2>
                        <p class="text-gray-600 text-lg animate-fadeInUp">Aperçu des activités de la clinique</p>
                    </div>
                    <!-- Widgets modernes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 border border-blue-200 shadow-dynamic animate-fadeInUp stats-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 font-semibold text-xs uppercase tracking-wider">Rendez-vous aujourd'hui</p>
                                    <h3 class="text-3xl font-extrabold text-blue-800 mt-2" id="widget-rdv-jour">...</h3>
                                </div>
                                <div class="bg-blue-500 rounded-full p-4">
                                    <i class="fas fa-calendar-alt text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span class="text-green-500 text-sm font-medium" id="widget-rdv-evol">...</span>
                                <span class="text-gray-500 text-xs ml-2">vs hier</span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border border-green-200 shadow-dynamic animate-fadeInUp stats-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-600 font-semibold text-xs uppercase tracking-wider">Nouveaux patients</p>
                                    <h3 class="text-3xl font-extrabold text-green-800 mt-2" id="widget-nouveaux-patients">...</h3>
                                </div>
                                <div class="bg-green-500 rounded-full p-4">
                                    <i class="fas fa-user-plus text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span class="text-green-500 text-sm font-medium" id="widget-patients-evol">...</span>
                                <span class="text-gray-500 text-xs ml-2">vs semaine dernière</span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-amber-100 rounded-2xl p-6 border border-yellow-200 shadow-dynamic animate-fadeInUp stats-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-600 font-semibold text-xs uppercase tracking-wider">Consult. en attente</p>
                                    <h3 class="text-3xl font-extrabold text-yellow-800 mt-2" id="widget-consult-attente">...</h3>
                                </div>
                                <div class="bg-yellow-400 rounded-full p-4">
                                    <i class="fas fa-clock text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span class="text-red-500 text-sm font-medium" id="widget-consult-evol">...</span>
                                <span class="text-gray-500 text-xs ml-2">vs hier</span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-2xl p-6 border border-purple-200 shadow-dynamic animate-fadeInUp stats-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 font-semibold text-xs uppercase tracking-wider">Patients</p>
                                    <h3 class="text-3xl font-extrabold text-purple-800 mt-2" id="widget-total-patients">...</h3>
                                </div>
                                <div class="bg-purple-500 rounded-full p-4">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span class="text-gray-500 text-xs">Total</span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl p-6 border border-indigo-200 shadow-dynamic animate-fadeInUp stats-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-indigo-600 font-semibold text-xs uppercase tracking-wider">Médecins</p>
                                    <h3 class="text-3xl font-extrabold text-indigo-800 mt-2" id="widget-total-docteurs">...</h3>
                                </div>
                                <div class="bg-indigo-500 rounded-full p-4">
                                    <i class="fas fa-user-md text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center">
                                <span class="text-gray-500 text-xs">Total</span>
                            </div>
                        </div>
                    </div>
                    <!-- Prochains rendez-vous et activités -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Prochains rendez-vous -->
                        <div class="lg:col-span-2 bg-white rounded-2xl shadow-dynamic animate-fadeInUp overflow-hidden">
                            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-900">Prochains rendez-vous</h3>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Voir tous</a>
                            </div>
                            <div class="divide-y divide-gray-100" id="dashboard-prochains-rdv">
                                <div class="p-8 text-center text-gray-400">
                                    <div class="loading-spinner mx-auto mb-2"></div>
                                    Chargement...
                                </div>
                            </div>
                        </div>
                        <!-- Activités récentes -->
                        <div class="bg-white rounded-2xl shadow-dynamic animate-fadeInUp overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900">Activités récentes</h3>
                            </div>
                            <div class="divide-y divide-gray-100" id="dashboard-activites">
                                <div class="p-8 text-center text-gray-400">
                                    <div class="loading-spinner mx-auto mb-2"></div>
                                    Chargement des activités...
                                    </div>
                                    </div>
                            <div class="p-4 bg-gray-50 text-center">
                                <a href="activities.php" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Voir toutes les activités</a>
                                </div>
                                    </div>
                                    </div>
                `;
                // Charger les activités récentes dynamiquement
                loadRecentActivities();
            }

            // Fonction pour charger dynamiquement les activités récentes
            function loadRecentActivities() {
                fetch('get_recent_activities.php')
                    .then(response => response.json())
                    .then(data => {
                        const activitiesContainer = document.getElementById('dashboard-activites');
                        if (data.success && data.activities.length > 0) {
                            activitiesContainer.innerHTML = data.activities.map(activity => `
                                <div class="p-6 flex items-center gap-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="bg-${activity.color}-100 rounded-full p-3">
                                        <i class="${activity.icon} text-${activity.color}-600 text-xl"></i>
                                </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">${activity.action}</p>
                                        <p class="text-xs text-gray-400 mt-1">${activity.time_ago}</p>
                                    </div>
                                    </div>
                            `).join('');
                        } else {
                            activitiesContainer.innerHTML = `
                                <div class="p-8 text-center text-gray-400">
                                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                                    <p>Aucune activité récente</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        const activitiesContainer = document.getElementById('dashboard-activites');
                        activitiesContainer.innerHTML = `
                            <div class="p-8 text-center text-red-400">
                                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                <p>Erreur lors du chargement des activités</p>
                    </div>
                `;
                    });
            }
            
            function loadAppointments() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Gestion des rendez-vous</h2>
                            <p class="text-gray-600">Planification et suivi des consultations</p>
                        </div>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i> Nouveau rendez-vous
                        </button>
                    </div>
                    
                    <!-- Filtres -->
                    <div class="bg-white rounded-lg shadow p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="filter-date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                <select id="filter-doctor" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les médecins</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <select id="filter-status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les statuts</option>
                                    <option value="confirmé">Confirmé</option>
                                    <option value="en attente">En attente</option>
                                    <option value="annulé">Annulé</option>
                                    <option value="terminé">Terminé</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button id="btn-filter" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg">
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calendrier et liste -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-gray-900">Liste des rendez-vous</h3>
                                    <div class="flex space-x-2">
                                        <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <button class="p-2 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Heure</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médecin</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rendezvous-tbody" class="bg-white divide-y divide-gray-200">
                                            <tr><td colspan="6" class="text-center py-6 text-gray-400">Chargement...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Précédent</a>
                                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Suivant</a>
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700" id="pagination-info">
                                                Affichage de <span class="font-medium">0</span> à <span class="font-medium">0</span> sur <span class="font-medium">0</span> résultats
                                            </p>
                                        </div>
                                        <div>
                                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="pagination-nav">
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Calendrier -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Calendrier</h3>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800" id="calendar-month-year">Juin 2023</h4>
                                    <div class="flex space-x-2">
                                        <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200" id="calendar-prev-month">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200" id="calendar-next-month">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Lun</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Mar</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Mer</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Jeu</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Ven</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Sam</div>
                                    <div class="text-center text-xs font-medium text-gray-500 py-1">Dim</div>
                                </div>
                                
                                <div class="grid grid-cols-7 gap-1" id="calendar-grid">
                                    <!-- Le calendrier sera généré dynamiquement ici -->
                                </div>
                            </div>
                            
                            <div class="p-4 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Légende</h4>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                                        <span class="text-xs text-gray-600">Aujourd'hui</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-xs text-gray-600">Rendez-vous</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                        <span class="text-xs text-gray-600">En attente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            
                // Charger les médecins pour les filtres
                loadDoctorsForFilter();
                
                // Charger les rendez-vous
                loadAppointmentsData();
                
                // Initialiser le calendrier
                initCalendar();
                
                // Ajouter les événements
                document.getElementById('btn-filter').addEventListener('click', loadAppointmentsData);
            }
            
            function loadPatients() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 animate-fadeInUp">Gestion des patients</h2>
                            <p class="text-gray-600 text-lg animate-fadeInUp">Liste complète des patients enregistrés</p>
                        </div>
                        <button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" id="openPatientModal">
                            <i class="fas fa-plus mr-3"></i> Nouveau patient
                        </button>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                        <div class="relative max-w-xs mx-auto">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                <i class="fas fa-search text-indigo-300"></i>
                            </div>
                            <input type="text" id="search-patient" class="w-full py-3 pl-12 pr-4 bg-white/20 border border-indigo-100 rounded-xl text-gray-700 placeholder-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all duration-300" placeholder="Rechercher un patient...">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="patients-list">
                        <div class="col-span-3 text-center text-gray-400 py-8">
                            <div class="loading-spinner mx-auto mb-2"></div>
                            Chargement...
                        </div>
                    </div>
                `;
                // Appel AJAX pour charger les patients
                fetch('lister_patients.php')
                    .then(response => response.json())
                    .then(data => {
                        const patientsList = document.getElementById('patients-list');
                        if(data.success && data.patients.length > 0) {
                            window._patientsList = data.patients;
                            patientsList.innerHTML = data.patients.map(p => `
                                <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-dynamic p-6 flex flex-col gap-4 hover:shadow-xl transition-all duration-300 animate-fadeInUp patient-item">
                                    <div class="flex items-center gap-4 mb-2">
                                        <img class="h-16 w-16 rounded-full border-2 border-indigo-200 shadow object-cover bg-white" src="${p.photo && p.photo !== 'null' ? '/medicale/' + p.photo : 'https://randomuser.me/api/portraits/lego/1.jpg'}" alt="">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-lg font-bold text-gray-900 patient-name truncate">${p.nom} ${p.prenom}</h4>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${p.statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                                    <i class="fas fa-circle mr-1 ${p.statut === 'actif' ? 'text-green-500' : 'text-yellow-500'}"></i>
                                                    ${p.statut.charAt(0).toUpperCase() + p.statut.slice(1)}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 mb-1">
                                                <span><i class="fas fa-user mr-1"></i>${p.sexe ? p.sexe : ''}${p.date_naissance ? ', ' + getAge(p.date_naissance) + ' ans' : ''}</span>
                                                ${p.groupe_sanguin ? `<span class='flex items-center px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full'><i class='fas fa-tint mr-1'></i>${p.groupe_sanguin}</span>` : ''}
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                                <i class="fas fa-phone mr-1"></i>${p.telephone ? p.telephone : 'Non renseigné'}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <span class="flex items-center px-2 py-1 bg-purple-100 text-purple-700 rounded-full"><i class="fas fa-prescription-bottle-medical mr-1"></i> ${p.allergies ? p.allergies : 'Aucune allergie'}</span>
                                        <span class="flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full"><i class="fas fa-calendar-alt mr-1"></i> ${p.date_naissance ? formatDate(p.date_naissance) : 'Date inconnue'}</span>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button class="text-indigo-600 hover:text-indigo-900 font-semibold btn-edit-patient" data-id="${p.patient_id}"><i class="fas fa-edit mr-1"></i>Editer</button>
                                        <button class="text-red-600 hover:text-red-900 font-semibold btn-supprimer-patient" data-id="${p.patient_id}"><i class="fas fa-trash mr-1"></i>Supprimer</button>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            patientsList.innerHTML = '<div class="col-span-3 text-center py-8 text-gray-400">Aucun patient trouvé</div>';
                        }
                    })
                    .catch(() => {
                        const patientsList = document.getElementById('patients-list');
                        patientsList.innerHTML = '<div class="col-span-3 text-center py-8 text-red-400">Erreur lors du chargement des patients</div>';
                    });

                // Fonctions utilitaires pour l'affichage
                function formatDate(dateStr) {
                    if(!dateStr) return '';
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('fr-FR');
                }
                function getAge(dateStr) {
                    if(!dateStr) return '';
                    const d = new Date(dateStr);
                    const diff = Date.now() - d.getTime();
                    const age = new Date(diff).getUTCFullYear() - 1970;
                    return age;
                }

                // Recherche dynamique avec animation
                let searchTimeout;
                document.getElementById('search-patient').addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase();
                        const patientItems = document.querySelectorAll('.patient-item');
                        patientItems.forEach(item => {
                            const patientName = item.querySelector('.patient-name').textContent.toLowerCase();
                            if (patientName.includes(searchTerm)) {
                                item.style.display = 'block';
                                item.classList.add('animate-fadeInUp');
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }, 200);
                });
            }
            
            function loadMedicalRecords() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-4xl font-extrabold text-indigo-800 mb-2 animate-fadeIn">Dossiers médicaux</h2>
                                <p class="text-gray-500 text-lg animate-fadeIn">Historique complet des consultations et traitements</p>
                            </div>
                            <div class="flex space-x-4">
                                <button class="bg-white border-2 border-indigo-200 text-indigo-700 px-6 py-3 rounded-xl flex items-center hover:bg-indigo-50 hover:border-indigo-300 transition-all duration-300 shadow-sm animate-fadeIn" id="btn-filter-dossiers">
                                    <i class="fas fa-filter mr-3 text-lg"></i> 
                                    <span class="font-semibold">Filtrer</span>
                                </button>
                               
                            </div>
                        </div>
                        <!-- Statistiques rapides -->
                        <div class="responsive-grid-2 mb-8">
                            <div class="responsive-card bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 animate-scaleIn">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-600 font-semibold text-sm uppercase tracking-wide">Total dossiers</p>
                                        <p class="text-3xl font-extrabold text-blue-800 mt-2" id="stats-total-dossiers">0</p>
                                    </div>
                                    <div class="bg-blue-500 rounded-full p-3">
                                        <i class="fas fa-folder-medical text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="responsive-card bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200 animate-scaleIn">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 font-semibold text-sm uppercase tracking-wide">Consultations</p>
                                        <p class="text-3xl font-extrabold text-green-800 mt-2" id="stats-total-consultations">0</p>
                                    </div>
                                    <div class="bg-green-500 rounded-full p-3">
                                        <i class="fas fa-stethoscope text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="responsive-card bg-gradient-to-br from-purple-50 to-violet-100 border border-purple-200 animate-scaleIn">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-600 font-semibold text-sm uppercase tracking-wide">Ordonnances</p>
                                        <p class="text-3xl font-extrabold text-purple-800 mt-2" id="stats-total-prescriptions">0</p>
                                    </div>
                                    <div class="bg-purple-500 rounded-full p-3">
                                        <i class="fas fa-prescription-bottle-medical text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="responsive-card bg-gradient-to-br from-orange-50 to-amber-100 border border-orange-200 animate-scaleIn">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-orange-600 font-semibold text-sm uppercase tracking-wide">Patients actifs</p>
                                        <p class="text-3xl font-extrabold text-orange-800 mt-2" id="stats-patients-actifs">0</p>
                                    </div>
                                    <div class="bg-orange-500 rounded-full p-3">
                                        <i class="fas fa-users text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Interface principale -->
                    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
                        <!-- Liste des patients -->
                        <div class="xl:col-span-1 order-2 xl:order-1">
                            <div class="responsive-card bg-white shadow-xl border border-gray-100 overflow-hidden animate-fadeIn">
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                                    <h3 class="text-white font-bold text-lg mb-4 flex items-center">
                                        <i class="fas fa-user-friends mr-3"></i>
                                        Patients
                                    </h3>
                                    <div class="relative mb-4">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                            <i class="fas fa-search text-indigo-300"></i>
                                        </div>
                                        <input type="text" id="search-patient" class="form-input w-full py-3 pl-12 pr-4 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all duration-300" placeholder="Rechercher un patient...">
                                    </div>
                                </div>
                                <div class="overflow-y-auto" style="max-height: 500px;">
                                    <div class="divide-y divide-gray-100" id="patients-list">
                                        <div class="p-6 text-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                                            <p class="text-gray-500 mt-2">Chargement...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Détails du dossier sélectionné -->
                        <div class="xl:col-span-3 order-1 xl:order-2">
                            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-fadeIn">
                                <!-- En-tête du dossier -->
                                <div class="bg-gradient-to-r from-gray-50 to-indigo-50 p-6 lg:p-10 border-b border-gray-200 flex flex-col lg:flex-row items-center lg:items-start gap-6 lg:gap-8" id="dossier-header">
                                    <div class="flex flex-col items-center lg:items-start gap-4 text-center lg:text-left">
                                        <div class="w-20 h-20 lg:w-28 lg:h-28 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user-injured text-3xl lg:text-5xl text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-800 mb-1">Sélectionnez un patient</h3>
                                            <p class="text-gray-600 text-sm lg:text-base">Choisissez un patient dans la liste pour consulter son dossier médical complet</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Onglets -->
                                <div class="border-b border-gray-200 bg-gray-50">
                                    <nav class="flex -mb-px px-4 lg:px-6 gap-2 overflow-x-auto" id="dossier-tabs">
                                        <a href="#" class="border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 whitespace-nowrap py-3 lg:py-4 px-4 lg:px-6 border-b-2 font-medium text-sm lg:text-base transition-all duration-300 rounded-t-lg">Sélectionnez un patient</a>
                                    </nav>
                                </div>
                                <!-- Contenu du dossier -->
                                <div class="p-6 lg:p-10 min-h-[300px]" id="dossier-content">
                                    <div class="text-center text-gray-500">
                                        <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                                            <i class="fas fa-file-medical text-2xl lg:text-4xl text-gray-400"></i>
                                        </div>
                                        <h4 class="text-lg lg:text-xl font-semibold text-gray-700 mb-2">Aucun dossier sélectionné</h4>
                                        <p class="text-gray-500 text-sm lg:text-base">Sélectionnez un patient dans la liste pour consulter son dossier médical</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Charger la liste des dossiers médicaux
                loadDossiersMedicaux();
                
                // Gestion de la recherche avec debounce
                let searchTimeout;
                document.getElementById('search-patient').addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase();
                        const patientItems = document.querySelectorAll('.patient-item');
                        
                        patientItems.forEach(item => {
                            const patientName = item.querySelector('.patient-name').textContent.toLowerCase();
                            if (patientName.includes(searchTerm)) {
                                item.style.display = 'block';
                                item.classList.add('animate-fadeIn');
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }, 300);
                });
            }
            
            function loadDossiersMedicaux() {
                fetch('lister_dossiers_medicaux.php')
                    .then(response => response.json())
                    .then(data => {
                        const patientsList = document.getElementById('patients-list');
                        
                        if (data.success && data.dossiers.length > 0) {
                            window._dossiersList = data.dossiers;
                            
                            // Mettre à jour les statistiques
                            document.getElementById('stats-total-dossiers').textContent = data.dossiers.length;
                            const totalConsultations = data.dossiers.reduce((sum, dossier) => sum + dossier.statistiques.consultations_count, 0);
                            const totalPrescriptions = data.dossiers.reduce((sum, dossier) => sum + dossier.statistiques.prescriptions_count, 0);
                            const patientsActifs = data.dossiers.filter(dossier => dossier.patient_info.statut === 'actif').length;
                            
                            document.getElementById('stats-total-consultations').textContent = totalConsultations;
                            document.getElementById('stats-total-prescriptions').textContent = totalPrescriptions;
                            document.getElementById('stats-patients-actifs').textContent = patientsActifs;
                            
                            patientsList.innerHTML = data.dossiers.map((dossier, index) => `
                                <div class="p-3 lg:p-4 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 cursor-pointer patient-item transition-all duration-300 ${index === 0 ? 'bg-gradient-to-r from-indigo-100 to-purple-100 border-l-4 border-indigo-500' : 'hover:border-l-4 hover:border-indigo-300'}" 
                                     data-medical-record-id="${dossier.medical_record_id}" 
                                     onclick="selectDossier(${dossier.medical_record_id})">
                                    <div class="flex items-start space-x-3 lg:space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="relative">
                                                <img class="h-10 w-10 lg:h-12 lg:w-12 rounded-full border-2 border-white shadow-lg object-cover" 
                                                     src="${dossier.patient_info.photo && dossier.patient_info.photo !== 'null' ? '/medicale/' + dossier.patient_info.photo : 'https://randomuser.me/api/portraits/lego/1.jpg'}" 
                                                     alt="">
                                                <div class="absolute -bottom-1 -right-1 w-3 h-3 lg:w-4 lg:h-4 bg-green-400 border-2 border-white rounded-full"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="text-xs lg:text-sm font-bold text-gray-900 patient-name truncate">${dossier.patient_name}</h4>
                                                <span class="inline-flex items-center px-1 lg:px-2 py-1 rounded-full text-xs font-medium ${dossier.patient_info.statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                                    ${dossier.patient_info.statut === 'actif' ? 'Actif' : 'Inactif'}
                                                </span>
                                            </div>
                                            <div class="flex items-center text-xs text-gray-500 mb-2">
                                                <i class="fas fa-user mr-1"></i>
                                                <span>${dossier.patient_info.sexe ? dossier.patient_info.sexe : ''}${dossier.patient_info.date_naissance ? ', ' + getAge(dossier.patient_info.date_naissance) + ' ans' : ''}</span>
                                            </div>
                                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between text-xs gap-1 lg:gap-0">
                                                <div class="flex items-center space-x-2 lg:space-x-3">
                                                    <span class="flex items-center text-blue-600">
                                                        <i class="fas fa-stethoscope mr-1"></i>
                                                        ${dossier.statistiques.consultations_count}
                                                    </span>
                                                    <span class="flex items-center text-purple-600">
                                                        <i class="fas fa-prescription-bottle-medical mr-1"></i>
                                                        ${dossier.statistiques.prescriptions_count}
                                                    </span>
                                                </div>
                                                <span class="text-gray-400 text-xs">
                                                    ${dossier.statistiques.derniere_consultation ? formatDate(dossier.statistiques.derniere_consultation) : 'Aucune visite'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            
                            // Sélectionner le premier dossier par défaut
                            if (data.dossiers.length > 0) {
                                setTimeout(() => {
                                    selectDossier(data.dossiers[0].medical_record_id);
                                }, 100);
                            }
                        } else {
                            patientsList.innerHTML = `
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-folder-open text-2xl text-gray-400"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Aucun dossier trouvé</h4>
                                    <p class="text-gray-500">Aucun dossier médical n'est disponible pour le moment</p>
                                </div>
                            `;
                        }
                    })
                    .catch(() => {
                        const patientsList = document.getElementById('patients-list');
                        patientsList.innerHTML = `
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-red-700 mb-2">Erreur de chargement</h4>
                                <p class="text-red-500">Impossible de charger les dossiers médicaux</p>
                            </div>
                        `;
                    });
            }
            window.selectDossier = selectDossier;
            
            function selectDossier(medicalRecordId) {
                // Mettre à jour la sélection visuelle avec animation
                document.querySelectorAll('.patient-item').forEach(item => {
                    item.classList.remove('border-l-4', 'border-indigo-500', 'bg-indigo-50', 'selected');
                    item.classList.add('animate-fadeIn');
                });
                
                const selectedItem = document.querySelector(`[data-medical-record-id="${medicalRecordId}"]`);
                if (selectedItem) {
                    selectedItem.classList.add('border-l-4', 'border-indigo-500', 'selected');
                    selectedItem.classList.remove('bg-indigo-50');
                    selectedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                
                // Afficher un indicateur de chargement
                const content = document.getElementById('dossier-content');
                content.innerHTML = `
                    <div class="flex items-center justify-center py-12">
                        <div class="loading-spinner"></div>
                        <span class="ml-4 text-gray-600 font-medium">Chargement du dossier...</span>
                    </div>
                `;
                
                // Charger les détails du dossier
                fetch(`get_dossier_details.php?medical_record_id=${medicalRecordId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Ajouter une animation de transition
                            content.style.opacity = '0';
                            content.style.transform = 'translateY(20px)';
                            
                            setTimeout(() => {
                                displayDossierDetails(data);
                                content.style.transition = 'all 0.4s ease';
                                content.style.opacity = '1';
                                content.style.transform = 'translateY(0)';
                            }, 200);
                        } else {
                            content.innerHTML = `
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold text-red-700 mb-2">Erreur de chargement</h4>
                                    <p class="text-red-500">${data.message}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(() => {
                        content.innerHTML = `
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-red-700 mb-2">Erreur de connexion</h4>
                                <p class="text-red-500">Impossible de charger le dossier médical</p>
                            </div>
                        `;
                    });
            }
            
            function displayDossierDetails(data) {
                const dossier = data.dossier;
                
                // Mettre à jour l'en-tête
                document.getElementById('dossier-header').innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <img class="h-20 w-20 rounded-full border-4 border-white shadow-xl object-cover" 
                                     src="${dossier.patient_info.photo && dossier.patient_info.photo !== 'null' ? '/medicale/' + dossier.patient_info.photo : 'https://randomuser.me/api/portraits/lego/1.jpg'}" 
                                     alt="">
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-400 border-3 border-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-1">${dossier.patient_info.nom} ${dossier.patient_info.prenom}</h3>
                                <div class="flex items-center space-x-6 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-indigo-500"></i>
                                        ${dossier.patient_info.sexe ? dossier.patient_info.sexe : ''}${dossier.patient_info.date_naissance ? ', ' + getAge(dossier.patient_info.date_naissance) + ' ans (' + formatDate(dossier.patient_info.date_naissance) + ')' : ''}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-phone mr-2 text-green-500"></i>
                                        ${dossier.patient_info.telephone || 'Non renseigné'}
                                    </span>
                                    <span class="flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-tint mr-1"></i>
                                        ${dossier.patient_info.groupe_sanguin || 'Non renseigné'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Dossier créé le</p>
                                <p class="text-sm font-semibold text-gray-700">${formatDate(dossier.dossier_info.date_creation)}</p>
                            </div>
                            <button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" onclick="printDossier(${dossier.medical_record_id})">
                                <i class="fas fa-print mr-3"></i> 
                                <span class="font-semibold">Imprimer</span>
                            </button>
                        </div>
                    </div>
                `;
                
                // Mettre à jour les onglets
                document.getElementById('dossier-tabs').innerHTML = `
                    <a href="#" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all duration-300" onclick="showTab('resume')">
                        <i class="fas fa-chart-line mr-2"></i>Résumé
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all duration-300" onclick="showTab('historique')">
                        <i class="fas fa-history mr-2"></i>Historique
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all duration-300" onclick="showTab('ordonnances')">
                        <i class="fas fa-prescription-bottle-medical mr-2"></i>Ordonnances
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all duration-300" onclick="showTab('examens')">
                        <i class="fas fa-microscope mr-2"></i>Examens
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all duration-300" onclick="showTab('allergies')">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Allergies
                    </a>
                `;
                
                // Afficher le contenu par défaut (résumé)
                showTabContent('resume', data);
            }
            
            function showTab(tabName) {
                // Mettre à jour les onglets
                document.querySelectorAll('#dossier-tabs a').forEach(tab => {
                    tab.classList.remove('border-indigo-500', 'text-indigo-600');
                    tab.classList.add('border-transparent', 'text-gray-500');
                });
                event.target.classList.remove('border-transparent', 'text-gray-500');
                event.target.classList.add('border-indigo-500', 'text-indigo-600');
                
                // Charger le contenu de l'onglet
                const currentDossierId = document.querySelector('.patient-item.border-l-4').getAttribute('data-medical-record-id');
                fetch(`get_dossier_details.php?medical_record_id=${currentDossierId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showTabContent(tabName, data);
                        }
                    });
            }
            
            function showTabContent(tabName, data) {
                const content = document.getElementById('dossier-content');
                
                switch(tabName) {
                    case 'resume':
                        content.innerHTML = generateResumeContent(data);
                        break;
                    case 'historique':
                        content.innerHTML = generateHistoriqueContent(data);
                        break;
                    case 'ordonnances':
                        content.innerHTML = generateOrdonnancesContent(data);
                        break;
                    case 'examens':
                        content.innerHTML = generateExamensContent(data);
                        break;
                    case 'allergies':
                        content.innerHTML = generateAllergiesContent(data);
                        break;
                }
            }
            
            function generateResumeContent(data) {
                const dossier = data.dossier;
                return `
                    <div class="space-y-8">
                        <!-- Informations médicales -->
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-heartbeat text-red-500 mr-3"></i>
                                Informations médicales
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gradient-to-br from-red-50 to-pink-100 rounded-2xl p-6 border border-red-200 shadow-sm">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-red-500 rounded-full p-2 mr-3">
                                            <i class="fas fa-history text-white text-sm"></i>
                                        </div>
                                        <h5 class="text-red-700 font-bold text-sm uppercase tracking-wide">ANTÉCÉDENTS MÉDICAUX</h5>
                                    </div>
                                    <p class="text-red-800 text-sm">Informations à compléter par le médecin</p>
                                </div>
                                <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-2xl p-6 border border-orange-200 shadow-sm">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-orange-500 rounded-full p-2 mr-3">
                                            <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                                        </div>
                                        <h5 class="text-orange-700 font-bold text-sm uppercase tracking-wide">ALLERGIES</h5>
                                    </div>
                                    <p class="text-orange-800 text-sm">${dossier.patient_info.allergies || 'Aucune allergie connue'}</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 border border-blue-200 shadow-sm">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-blue-500 rounded-full p-2 mr-3">
                                            <i class="fas fa-pills text-white text-sm"></i>
                                        </div>
                                        <h5 class="text-blue-700 font-bold text-sm uppercase tracking-wide">TRAITEMENTS EN COURS</h5>
                                    </div>
                                    <p class="text-blue-800 text-sm">Aucun traitement en cours</p>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border border-green-200 shadow-sm">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-green-500 rounded-full p-2 mr-3">
                                            <i class="fas fa-running text-white text-sm"></i>
                                        </div>
                                        <h5 class="text-green-700 font-bold text-sm uppercase tracking-wide">HABITUDES DE VIE</h5>
                                    </div>
                                    <p class="text-green-800 text-sm">Informations à compléter</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dernières consultations -->
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-stethoscope text-blue-500 mr-3"></i>
                                Dernières consultations
                            </h4>
                            <div class="space-y-4">
                                ${data.consultations.length > 0 ? data.consultations.slice(0, 3).map((consultation, index) => `
                                    <div class="bg-white border-l-4 border-blue-500 pl-6 py-4 rounded-r-2xl shadow-sm hover:shadow-md transition-all duration-300">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center">
                                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                                    <i class="fas fa-user-md text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <h5 class="font-bold text-gray-900">${consultation.type}</h5>
                                                    <p class="text-sm text-gray-600">${consultation.doctor_name} - ${formatDate(consultation.date_consultation)}</p>
                                                </div>
                                            </div>
                                            <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full flex items-center">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Terminé
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div>
                                                <span class="font-semibold text-gray-700">Motif:</span>
                                                <p class="text-gray-600 ml-2">${consultation.motif}</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Observations:</span>
                                                <p class="text-gray-600 ml-2">${consultation.observations || 'Aucune observation'}</p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('') : `
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                                        </div>
                                        <h5 class="text-lg font-semibold text-gray-700 mb-2">Aucune consultation</h5>
                                        <p class="text-gray-500">Aucune consultation enregistrée pour ce patient</p>
                                    </div>
                                `}
                            </div>
                        </div>
                        
                      
                `;
                
                // Charger la liste des médecins
                loadDoctorsForConsultation();
            }
            
            function generateHistoriqueContent(data) {
                return `
                    <div class="space-y-4">
                        ${data.consultations.length > 0 ? data.consultations.map(consultation => `
                            <div class="border-l-4 border-indigo-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-medium text-gray-900">${consultation.type}</h5>
                                        <p class="text-sm text-gray-600">${consultation.doctor_name} - ${formatDate(consultation.date_consultation)}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Terminé</span>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p class="font-medium">Motif:</p>
                                    <p>${consultation.motif}</p>
                                    <p class="font-medium mt-2">Observations:</p>
                                    <p>${consultation.observations || 'Aucune observation'}</p>
                                </div>
                            </div>
                        `).join('') : '<p class="text-gray-500">Aucune consultation enregistrée</p>'}
                    </div>
                `;
            }
            
            function generateOrdonnancesContent(data) {
                return `
                    <div class="space-y-4">
                        ${data.prescriptions.length > 0 ? data.prescriptions.map(prescription => `
                            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-medium text-gray-900">Ordonnance du ${formatDate(prescription.date_prescription)}</h5>
                                        <p class="text-sm text-gray-600">${prescription.doctor_name} - ${formatDate(prescription.consultation_date)}</p>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p class="font-medium">Médicaments:</p>
                                    ${prescription.medications.length > 0 ? prescription.medications.map(med => `
                                        <div class="ml-4 mt-1">
                                            <p><strong>${med.nom}</strong> ${med.dosage ? '(' + med.dosage + ')' : ''}</p>
                                            <p class="text-gray-600">${med.quantite} - ${med.instructions || 'Sans instruction particulière'}</p>
                                        </div>
                                    `).join('') : '<p class="ml-4 text-gray-500">Aucun médicament prescrit</p>'}
                                    ${prescription.details ? `<p class="font-medium mt-2">Notes:</p><p>${prescription.details}</p>` : ''}
                                </div>
                            </div>
                        `).join('') : '<p class="text-gray-500">Aucune ordonnance enregistrée</p>'}
                    </div>
                `;
            }
            
            function generateExamensContent(data) {
                return `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-microscope text-4xl mb-4"></i>
                        <p>Fonctionnalité des examens à implémenter</p>
                    </div>
                `;
            }
            
            function generateAllergiesContent(data) {
                const dossier = data.dossier;
                return `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-500 mb-2">ALLERGIES CONNUES</h5>
                        <p class="text-sm text-gray-700">${dossier.patient_info.allergies || 'Aucune allergie connue'}</p>
                    </div>
                `;
            }
            
            function loadDoctorsForConsultation() {
                fetch('get_admin_doctors.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const select = document.querySelector('select[name="doctor_id"]');
                            if (select) {
                                data.doctors.forEach(doctor => {
                                    const option = document.createElement('option');
                                    option.value = doctor.id;
                                    option.textContent = doctor.name + (doctor.specialite ? ' (' + doctor.specialite + ')' : '');
                                    select.appendChild(option);
                                });
                            }
                        }
                    });
            }
            
            // Gestion du formulaire de nouvelle consultation
            document.addEventListener('submit', function(e) {
                if (e.target.id === 'form-nouvelle-consultation') {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    
                    fetch('ajouter_consultation_admin.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Consultation ajoutée avec succès !');
                            e.target.reset();
                            // Recharger le dossier
                            const currentDossierId = document.querySelector('.patient-item.border-l-4').getAttribute('data-medical-record-id');
                            selectDossier(currentDossierId);
                        } else {
                            alert('Erreur : ' + data.message);
                        }
                    })
                    .catch(() => {
                        alert('Erreur lors de la communication avec le serveur.');
                    });
                }
            });
            
            function printDossier(medicalRecordId) {
                // Récupérer l'en-tête et le contenu du dossier
                const header = document.getElementById('dossier-header');
                const content = document.getElementById('dossier-content');
                if (!header || !content) {
                    alert('Erreur : contenu du dossier introuvable.');
                    return;
                }
                // Créer une nouvelle fenêtre pour l'impression
                const printWindow = window.open('', '', 'width=900,height=700');
                // Styles minimalistes pour l'impression
                const styles = `
                  <style>
                    body { font-family: Arial, sans-serif; color: #222; background: #fff; margin: 0; padding: 0; }
                    .header { padding: 24px 24px 12px 24px; border-bottom: 2px solid #6366f1; background: #f5f7ff; }
                    .content { padding: 24px; }
                    h3, h2, h4 { color: #3730a3; margin-bottom: 8px; }
                    .info { margin-bottom: 16px; }
                    .section { margin-bottom: 32px; }
                    .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; background: #e0e7ff; color: #3730a3; font-size: 12px; margin-left: 8px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
                    th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
                    th { background: #f1f5f9; }
                    @media print { .no-print { display: none !important; } }
                  </style>
                `;
                // Construire le HTML à imprimer
                printWindow.document.write(`
                  <html><head><title>Dossier médical</title>${styles}</head><body>
                    <div class="header">${header.innerHTML}</div>
                    <div class="content">${content.innerHTML}</div>
                  </body></html>
                `);
                printWindow.document.close();
                // Attendre que le contenu soit chargé avant d'imprimer
                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                    setTimeout(() => { printWindow.close(); }, 500);
                };
            }
            
            // Fonctions utilitaires
            function formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleDateString('fr-FR');
            }
            
            function getAge(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const diff = Date.now() - d.getTime();
                const age = new Date(diff).getUTCFullYear() - 1970;
                return age;
            }
            
            function loadMedications() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 animate-fadeInUp">Gestion des médicaments</h2>
                            <p class="text-gray-600 text-lg animate-fadeInUp">Inventaire et prescriptions des médicaments</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-xl flex items-center hover:bg-gray-50 shadow-sm transition-all duration-300">
                                <i class="fas fa-download mr-2"></i> Exporter
                            </button>
                            <button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" id="openMedicamentModal">
                                <i class="fas fa-plus mr-3"></i> Ajouter médicament
                            </button>
                        </div>
                    </div>
                    <!-- Filtres et statistiques -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                        <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 animate-fadeInUp">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Recherche</label>
                                    <input type="text" id="filtre-recherche" class="form-input" placeholder="Nom, DCI...">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Catégorie</label>
                                    <select id="filtre-categorie" class="form-select">
                                        <option>Toutes catégories</option>
                                        <option>Antibiotique</option>
                                        <option>Antihypertenseur</option>
                                        <option>Antidouleur</option>
                                        <option>Antidiabétique</option>
                                        <option>Psychotrope</option>
                                        <option>Bronchodilatateur</option>
                                        <option>Anti-inflammatoire</option>
                                        <option>Vitamines</option>
                                        <option>Autre</option>
                                    </select>
                                </div>
                                <div class="form-group flex items-end">
                                    <button id="btn-filtrer-medicaments" class="btn btn-primary w-full">
                                        <i class="fas fa-filter mr-2"></i>
                                        Filtrer
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-red-50 to-pink-100 rounded-2xl p-6 border border-red-200 shadow-dynamic animate-fadeInUp flex flex-col items-center justify-center">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-red-500 rounded-full p-3">
                                    <i class="fas fa-pills text-white text-2xl"></i>
                                </div>
                                <span class="text-2xl font-bold text-red-700" id="total-stock">...</span>
                            </div>
                            <p class="text-red-700 font-semibold text-xs uppercase tracking-wider">Médicaments en stock</p>
                            <span class="text-green-500 text-xs font-medium mt-2">+5% <span class="text-gray-500 ml-1">vs mois dernier</span></span>
                        </div>
                    </div>
                    <!-- Liste des médicaments -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fadeInUp">
                        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="text-lg font-bold text-gray-900">Inventaire des médicaments</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">148 médicaments trouvés</span>
                                <button class="btn btn-secondary p-2">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="responsive-table medicaments-table min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom commercial</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCI</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Forme</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="medicaments-tbody" class="bg-white divide-y divide-gray-200">
                                    <tr><td colspan="7" class="text-center py-6 text-gray-400">Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 gap-4">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="#" class="btn btn-secondary text-sm">Précédent</a>
                                <a href="#" class="btn btn-secondary text-sm">Suivant</a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700" id="pagination-info">
                                        Affichage de <span class="font-medium">1</span> à <span class="font-medium">3</span> sur <span class="font-medium">148</span> résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="pagination" aria-label="Pagination" id="pagination-nav">
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                // Ajout du fetch pour le total stock
                fetch('get_total_stock.php')
                  .then(r => r.json())
                  .then(data => {
                    if(data.success) {
                      document.getElementById('total-stock').textContent = data.total_stock;
                    } else {
                      document.getElementById('total-stock').textContent = 'Erreur';
                    }
                  })
                  .catch(() => {
                    document.getElementById('total-stock').textContent = 'Erreur';
                  });
                function chargerMedicaments(recherche = '', categorie = '') {
                  const params = new URLSearchParams();
                  if(recherche) params.append('recherche', recherche);
                  if(categorie) params.append('categorie', categorie);
                  fetch('lister_medicaments.php?' + params.toString())
                    .then(response => response.json())
                    .then(data => {
                      const tbody = document.getElementById('medicaments-tbody');
                      if(data.success && data.medicaments.length > 0) {
                        tbody.innerHTML = data.medicaments.map(m => `
                          <tr class="hover:bg-indigo-50 transition-all duration-300 animate-fadeInUp">
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full p-2">
                                  <i class="fas fa-pills text-indigo-600"></i>
                                </div>
                                <div class="text-sm font-bold text-gray-900 responsive-text">${m.nom}</div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900 responsive-text">${m.dci || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900 responsive-text">${m.forme || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900 responsive-text">${m.dosage || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center gap-2">
                                <div class="stock-progress bg-gray-200 rounded-full h-2.5">
                                  <div class="${m.stock > 60 ? 'bg-green-600' : m.stock > 20 ? 'bg-yellow-400' : 'bg-red-600'} h-2.5 rounded-full" style="width: ${Math.min(100, m.stock)}%"></div>
                                </div>
                                <span class="text-sm font-semibold ${m.stock > 60 ? 'text-green-700' : m.stock > 20 ? 'text-yellow-700' : 'text-red-700'} responsive-text">${m.stock}/100</span>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="category-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${m.categorie && m.categorie.toLowerCase().includes('antibiotique') ? 'bg-red-100 text-red-800' : m.categorie && m.categorie.toLowerCase().includes('antihypertenseur') ? 'bg-blue-100 text-blue-800' : m.categorie && m.categorie.toLowerCase().includes('bronchodilatateur') ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'}">${m.categorie || ''}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                              <div class="action-buttons">
                                <button class="btn btn-compact btn-secondary btn-edit-medicament" data-id="${m.id}" title="Modifier">
                                  <i class="fas fa-edit"></i>
                                  <span class="hidden sm:inline ml-1">Modifier</span>
                                </button>
                                <button class="btn btn-compact btn-danger btn-supprimer-medicament" data-id="${m.id}" title="Supprimer">
                                  <i class="fas fa-trash"></i>
                                  <span class="hidden sm:inline ml-1">Supprimer</span>
                                </button>
                              </div>
                            </td>
                          </tr>
                        `).join('');
                        
                        // Ajouter les event listeners pour les boutons
                        setTimeout(() => {
                          // Event listeners pour les boutons d'édition
                          document.querySelectorAll('.btn-edit-medicament').forEach(btn => {
                            btn.addEventListener('click', function() {
                              const id = this.getAttribute('data-id');
                              editMedicament(id);
                            });
                          });
                          
                          // Event listeners pour les boutons de suppression
                          document.querySelectorAll('.btn-supprimer-medicament').forEach(btn => {
                            btn.addEventListener('click', function() {
                              const id = this.getAttribute('data-id');
                              if(confirm('Voulez-vous vraiment supprimer ce médicament ?')) {
                                supprimerMedicament(id);
                              }
                            });
                          });
                        }, 100);
                      } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-6 text-gray-400">Aucun médicament trouvé</td></tr>';
                      }
                    })
                    .catch(() => {
                      const tbody = document.getElementById('medicaments-tbody');
                      tbody.innerHTML = '<tr><td colspan="7" class="text-center py-6 text-red-400">Erreur lors du chargement des médicaments</td></tr>';
                    });
                }
                
                // Fonction pour éditer un médicament
                function editMedicament(id) {
                  fetch('get_medicament.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                      if(data.success) {
                        const medicament = data.medicament;
                        showEditModal(medicament);
                      } else {
                        alert('Erreur : ' + data.message);
                      }
                    })
                    .catch(() => {
                      alert('Erreur lors de la communication avec le serveur.');
                    });
                }
                
                // Fonction pour supprimer un médicament
                function supprimerMedicament(id) {
                  fetch('supprimer_medicament.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(id)
                  })
                  .then(response => response.json())
                  .then(data => {
                    if(data.success) {
                      alert('Médicament supprimé avec succès !');
                      chargerMedicaments();
                    } else {
                      alert('Erreur : ' + data.message);
                    }
                  })
                  .catch(() => {
                    alert('Erreur lors de la communication avec le serveur.');
                  });
                }
                
                // Fonction pour afficher le modal d'édition
                function showEditModal(medicament) {
                  const modal = document.createElement('div');
                  modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                  modal.innerHTML = `
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                      <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                          <h3 class="text-lg font-semibold text-gray-900">Modifier le médicament</h3>
                          <button class="text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                            <i class="fas fa-times text-xl"></i>
                          </button>
                        </div>
                      </div>
                      <form id="editMedicamentForm" class="p-6">
                        <input type="hidden" name="id" value="${medicament.id}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du médicament *</label>
                            <input type="text" name="nom" value="${medicament.nom || ''}" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DCI</label>
                            <input type="text" name="dci" value="${medicament.dci || ''}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forme</label>
                            <input type="text" name="forme" value="${medicament.forme || ''}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                            <input type="text" name="dosage" value="${medicament.dosage || ''}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" value="${medicament.stock || 0}" min="0" max="100" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="categorie" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                              <option value="">Sélectionner une catégorie</option>
                              <option value="Antibiotique" ${medicament.categorie === 'Antibiotique' ? 'selected' : ''}>Antibiotique</option>
                              <option value="Antihypertenseur" ${medicament.categorie === 'Antihypertenseur' ? 'selected' : ''}>Antihypertenseur</option>
                              <option value="Bronchodilatateur" ${medicament.categorie === 'Bronchodilatateur' ? 'selected' : ''}>Bronchodilatateur</option>
                              <option value="Analgésique" ${medicament.categorie === 'Analgésique' ? 'selected' : ''}>Analgésique</option>
                              <option value="Anti-inflammatoire" ${medicament.categorie === 'Anti-inflammatoire' ? 'selected' : ''}>Anti-inflammatoire</option>
                              <option value="Autre" ${medicament.categorie === 'Autre' ? 'selected' : ''}>Autre</option>
                            </select>
                          </div>
                         
                        </div>
                        <div class="mt-4">
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                          <button type="button" onclick="this.closest('.fixed').remove()" 
                                  class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Annuler
                          </button>
                          <button type="submit" 
                                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Enregistrer
                          </button>
                        </div>
                      </form>
                    </div>
                  `;
                  
                  document.body.appendChild(modal);
                  
                  // Gestion de la soumission du formulaire
                  document.getElementById('editMedicamentForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData);
                    
                    fetch('update_medicament.php', {
                      method: 'POST',
                      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                      body: new URLSearchParams(data).toString()
                    })
                    .then(response => response.json())
                    .then(result => {
                      if(result.success) {
                        alert('Médicament mis à jour avec succès !');
                        modal.remove();
                        chargerMedicaments();
                      } else {
                        alert('Erreur : ' + result.message);
                      }
                    })
                    .catch(() => {
                      alert('Erreur lors de la communication avec le serveur.');
                    });
                  });
                }
                
                // Appel initial
                chargerMedicaments();
                // Gestion du clic sur le bouton Filtrer
                const btnFiltrer = document.getElementById('btn-filtrer-medicaments');
                if(btnFiltrer) {
                  btnFiltrer.addEventListener('click', function() {
                    const recherche = document.getElementById('filtre-recherche').value;
                    const categorie = document.getElementById('filtre-categorie').value;
                    chargerMedicaments(recherche, categorie);
                  });
                }
            }
            
            function loadDoctors() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 animate-fadeInUp">Gestion des médecins</h2>
                            <p class="text-gray-600 text-lg animate-fadeInUp">Liste des médecins de la clinique</p>
                        </div>
                        <button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" id="openDoctorModal">
                            <i class="fas fa-plus mr-3"></i> Ajouter médecin
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="doctors-list">
                        <div class="col-span-3 text-center text-gray-400 py-8">Chargement...</div>
                    </div>
                `;
                fetch('lister_docteurs.php')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('doctors-list');
                        if(data.success && data.docteurs.length > 0) {
                            window._docteursList = data.docteurs;
                            container.innerHTML = data.docteurs.map(d => `
                                <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-dynamic p-6 flex flex-col gap-4 hover:shadow-xl transition-all duration-300 animate-fadeInUp">
                                    <div class="flex items-center gap-4 mb-2">
                                        <img class="h-16 w-16 rounded-full border-2 border-indigo-200 shadow object-cover bg-white" src="https://randomuser.me/api/portraits/lego/2.jpg" alt="">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-lg font-bold text-gray-900 truncate">Dr. ${d.nom} ${d.prenom}</h4>
                                                ${d.specialite ? `<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700'><i class='fas fa-stethoscope mr-1'></i>${d.specialite}</span>` : ''}
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 mb-1">
                                                <span><i class="fas fa-envelope mr-1"></i>${d.email}</span>
                                                ${d.telephone ? `<span class='flex items-center'><i class='fas fa-phone mr-1'></i>${d.telephone}</span>` : ''}
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                                <i class="fas fa-user-injured mr-1"></i>${d.nb_patients ? d.nb_patients : 0} patients
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <span class="flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full"><i class="fas fa-calendar-alt mr-1"></i> ${d.disponibilite ? d.disponibilite : 'Non renseignée'}</span>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button class="flex-1 bg-indigo-50 text-indigo-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-indigo-100 btn-planning-docteur" data-id="${d.doctor_id}"><i class="fas fa-calendar-alt mr-2"></i>Planning</button>
                                        <button class="flex-1 bg-gray-50 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-100 btn-edit-docteur" data-id="${d.doctor_id}"><i class="fas fa-edit mr-2"></i>Editer</button>
                                        <button class="flex-1 bg-red-50 text-red-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-red-100 btn-supprimer-docteur" data-id="${d.doctor_id}"><i class="fas fa-trash mr-2"></i>Supprimer</button>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            container.innerHTML = '<div class="col-span-3 text-center py-8 text-gray-400">Aucun médecin trouvé</div>';
                        }
                    })
                    .catch(() => {
                        const container = document.getElementById('doctors-list');
                        container.innerHTML = '<div class="col-span-3 text-center text-red-400 py-8">Erreur lors du chargement des médecins</div>';
                    });
                setTimeout(() => {
                    document.querySelectorAll('.btn-supprimer-docteur').forEach(btn => {
                        btn.addEventListener('click', function() {
                            if(confirm('Voulez-vous vraiment supprimer ce médecin ?')) {
                                const id = this.getAttribute('data-id');
                                fetch('supprimer_docteur.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: 'id=' + encodeURIComponent(id)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if(data.success) {
                                        alert('Médecin supprimé avec succès !');
                                        loadDoctors();
                                    } else {
                                        alert('Erreur : ' + data.message);
                                    }
                                })
                                .catch(() => {
                                    alert('Erreur lors de la communication avec le serveur.');
                                });
                            }
                        });
                    });
                }, 300);
            }
            
            function loadStatistics() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8 flex items-center gap-4">
                      <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-chart-bar text-indigo-600 text-2xl"></i>
                    </div>
                      <div>
                        <h2 class="text-3xl font-bold text-gray-800">Statistiques de la clinique</h2>
                        <p class="text-gray-500 text-lg">Analyse visuelle de l'activité et des indicateurs clés</p>
                            </div>
                        </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                      <div class="bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center justify-between mb-4">
                          <h3 class="text-xl font-bold text-indigo-800 flex items-center gap-2">
                            <i class="fas fa-chart-line text-indigo-500"></i>
                            Consultations par mois
                          </h3>
                          <span class="text-sm text-gray-400">Année en cours</span>
                            </div>
                        <div class="h-72 flex items-center justify-center relative">
                          <canvas id="consultationsParMoisChart" width="600" height="250"></canvas>
                          <div id="chart-loader" class="absolute inset-0 flex items-center justify-center bg-white/60 z-10 hidden">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                        </div>
                    </div>
                      </div>
                      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-white">
                          <h3 class="text-lg font-bold text-indigo-700 flex items-center gap-2">
                            <i class="fas fa-bolt text-yellow-400"></i>
                            Indicateurs clés
                          </h3>
                        </div>
                        <div class="overflow-x-auto">
                          <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-indigo-50">
                                    <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-500 uppercase tracking-wider">Indicateur</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-500 uppercase tracking-wider">Valeur</th>
                                    </tr>
                                </thead>
                            <tbody class="bg-white divide-y divide-indigo-50" id="stats-tbody">
                                    <tr><td colspan="2" class="text-center py-6 text-gray-400">Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                `;
                // Appel AJAX pour charger les statistiques dynamiques (version simple)
                fetch('lister_rendezvous.php?stats=1')
                  .then(response => response.json())
                  .then(data => {
                    if(data.success) {
                      const stats = data.stats;
                      document.getElementById('stats-tbody').innerHTML = `
                        <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap font-medium">Rendez-vous aujourd'hui</td>
                          <td class="px-6 py-4 whitespace-nowrap">${stats.rdv_aujourdhui}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap font-medium">Nouveaux patients (7 jours)</td>
                          <td class="px-6 py-4 whitespace-nowrap">${stats.nouveaux_patients}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap font-medium">Consultations en attente</td>
                          <td class="px-6 py-4 whitespace-nowrap">${stats.consultations_attente}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap font-medium">Total patients</td>
                          <td class="px-6 py-4 whitespace-nowrap">${stats.total_patients}</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap font-medium">Total docteurs</td>
                          <td class="px-6 py-4 whitespace-nowrap">${stats.total_docteurs}</td>
                        </tr>
                      `;
                    } else {
                      document.getElementById('stats-tbody').innerHTML = `<tr><td colspan="2" class="text-center text-red-500 py-6">Erreur: ${data.message}</td></tr>`;
                    }
                  })
                  .catch(() => {
                    document.getElementById('stats-tbody').innerHTML = `<tr><td colspan="2" class="text-center text-red-500 py-6">Erreur de connexion au serveur</td></tr>`;
                  });
                // Modernisation du JS pour loader animé
                setTimeout(() => {
                  document.getElementById('chart-loader').classList.remove('hidden');
                  fetch('stats_consultations_par_mois.php')
                    .then(r => r.json())
                    .then(stats => {
                      if(stats.success) {
                        const ctx = document.getElementById('consultationsParMoisChart').getContext('2d');
                        new Chart(ctx, {
                          type: 'bar',
                          data: {
                            labels: stats.labels,
                            datasets: [{
                              label: 'Consultations',
                              data: stats.data,
                              backgroundColor: 'rgba(99, 102, 241, 0.7)',
                              borderColor: 'rgba(99, 102, 241, 1)',
                              borderWidth: 1,
                              borderRadius: 8
                            }]
                          },
                          options: {
                            responsive: true,
                            plugins: {
                              legend: { display: false },
                              title: { display: false }
                            },
                            scales: {
                              y: { beginAtZero: true, ticks: { stepSize: 1 } }
                            }
                          }
                        });
                      }
                    })
                    .finally(() => {
                      document.getElementById('chart-loader').classList.add('hidden');
                    });
                }, 500);
            }
            
            function loadSettings() {
                loadSettingsImproved();
            }
            
            // Fonction pour réinitialiser le formulaire de mot de passe
            function resetPasswordForm() {
                document.getElementById('form-admin-password').reset();
                document.getElementById('admin-password-feedback').textContent = '';
                document.getElementById('admin-password-feedback').className = 'mt-4 text-sm';
            }
        });

        // Gestion modale patient
        document.addEventListener('click', function(e) {
          if(e.target && e.target.id === 'openPatientModal') {
            document.getElementById('patientModal').classList.remove('hidden');
          }
          if(e.target && (e.target.id === 'closePatientModal' || e.target.id === 'closePatientModal2')) {
            document.getElementById('patientModal').classList.add('hidden');
          }
          // Gestion modale médecin
          if(e.target && e.target.id === 'openDoctorModal') {
            document.getElementById('doctorModal').classList.remove('hidden');
          }
          if(e.target && (e.target.id === 'closeDoctorModal' || e.target.id === 'closeDoctorModal2')) {
            document.getElementById('doctorModal').classList.add('hidden');
          }
          // Gestion modale médicament
          if(e.target && e.target.id === 'openMedicamentModal') {
            document.getElementById('medicamentModal').classList.remove('hidden');
          }
          if(e.target && (e.target.id === 'closeMedicamentModal' || e.target.id === 'closeMedicamentModal2')) {
            document.getElementById('medicamentModal').classList.add('hidden');
          }
        });

        // Gestion AJAX du formulaire d'ajout de patient
        const formAjoutPatient = document.getElementById('form-ajout-patient');
        if(formAjoutPatient) {
          formAjoutPatient.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formAjoutPatient);
            fetch('ajouter_patient.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if(data.success) {
                alert('Patient ajouté avec succès !');
                formAjoutPatient.reset();
                document.getElementById('patientModal').classList.add('hidden');
                // Ici, on pourrait rafraîchir la liste des patients dynamiquement
              } else {
                alert('Erreur : ' + data.message);
              }
            })
            .catch(() => {
              alert('Erreur lors de la communication avec le serveur.');
            });
          });
        }

        // Gestion AJAX du formulaire d'ajout de médecin
        const formAjoutDocteur = document.getElementById('form-ajout-docteur');
        if(formAjoutDocteur) {
          formAjoutDocteur.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formAjoutDocteur);
            fetch('ajouter_docteur.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if(data.success) {
                alert('Médecin ajouté avec succès !');
                formAjoutDocteur.reset();
                document.getElementById('doctorModal').classList.add('hidden');
                // Ici, on pourrait rafraîchir la liste des médecins dynamiquement
              } else {
                alert('Erreur : ' + data.message);
              }
            })
            .catch(() => {
              alert('Erreur lors de la communication avec le serveur.');
            });
          });
        }

        // Gestion AJAX pour l'ajout de médicament
        const formAjoutMedicament = document.getElementById('form-ajout-medicament');
        if(formAjoutMedicament) {
          formAjoutMedicament.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formAjoutMedicament);
            fetch('ajouter_medicament.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if(data.success) {
                alert('Médicament ajouté avec succès !');
                formAjoutMedicament.reset();
                document.getElementById('medicamentModal').classList.add('hidden');
                // Ici, on pourrait rafraîchir la liste des médicaments dynamiquement
              } else {
                alert('Erreur : ' + data.message);
              }
            })
            .catch(() => {
              alert('Erreur lors de la communication avec le serveur.');
            });
          });
        }

        // Après le rendu du tableau, ajouter le JS suivant :
        function chargerRendezvous(date = '', medecin = '', statut = '') {
          const params = new URLSearchParams();
          if(date) params.append('date', date);
          if(medecin) params.append('medecin', medecin);
          if(statut) params.append('statut', statut);
          fetch('lister_rendezvous.php?' + params.toString())
            .then(response => response.json())
            .then(data => {
              const tbody = document.getElementById('rendezvous-tbody');
              if(data.success && data.rendezvous.length > 0) {
                tbody.innerHTML = data.rendezvous.map(r => `
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/lego/3.jpg" alt="">
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">${r.patient_nom} ${r.patient_prenom}</div>
                          <div class="text-sm text-gray-500">${r.patient_sexe ? r.patient_sexe : ''}${r.patient_date_naissance ? ', ' + getAge(r.patient_date_naissance) + ' ans' : ''}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">${formatDate(r.date_heure)}</div>
                      <div class="text-sm text-gray-500">${formatHeure(r.date_heure)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">Dr. ${r.doctor_nom} ${r.doctor_prenom}</div>
                      <div class="text-sm text-gray-500">${r.doctor_specialite ? r.doctor_specialite : ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">${r.motif || ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${r.statut === 'confirmé' ? 'bg-green-100 text-green-800' : r.statut === 'en attente' ? 'bg-yellow-100 text-yellow-800' : r.statut === 'annulé' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}">${r.statut.charAt(0).toUpperCase() + r.statut.slice(1)}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</button>
                      <button class="text-red-600 hover:text-red-900">Annuler</button>
                    </td>
                  </tr>
                `).join('');
              } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-gray-400">Aucun rendez-vous trouvé</td></tr>';
              }
            })
            .catch(() => {
              const tbody = document.getElementById('rendezvous-tbody');
              tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-red-400">Erreur lors du chargement des rendez-vous</td></tr>';
            });
        }
        // Fonctions utilitaires pour l'affichage
        function formatDate(dateStr) {
          if(!dateStr) return '';
          const d = new Date(dateStr);
          return d.toLocaleDateString('fr-FR');
        }
        function formatHeure(dateStr) {
          if(!dateStr) return '';
          const d = new Date(dateStr);
          return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
        function getAge(dateStr) {
          if(!dateStr) return '';
          const d = new Date(dateStr);
          const diff = Date.now() - d.getTime();
          const age = new Date(diff).getUTCFullYear() - 1970;
          return age;
        }
        // Appel initial
        chargerRendezvous();
        // ... (ajouter la gestion des filtres plus tard)

        // Branche les filtres sur le rechargement dynamique
        setTimeout(() => {
          const dateInput = document.querySelector('input[type="date"]');
          const medecinSelect = document.querySelectorAll('select')[0];
          const statutSelect = document.querySelectorAll('select')[1];
          const filtrerBtn = document.querySelector('button.bg-indigo-600');

          function getFiltreValeurs() {
            const date = dateInput.value;
            let medecin = medecinSelect.value;
            let statut = statutSelect.value;
            // Harmonisation des valeurs envoyées
            if(medecin.toLowerCase().includes('tous')) medecin = '';
            if(statut.toLowerCase().includes('tous')) statut = '';
            return { date, medecin, statut };
          }

          filtrerBtn.addEventListener('click', () => {
            const { date, medecin, statut } = getFiltreValeurs();
            chargerRendezvous(date, medecin, statut);
          });
          // Optionnel : recharger à chaque changement de filtre
          dateInput.addEventListener('change', () => {
            const { date, medecin, statut } = getFiltreValeurs();
            chargerRendezvous(date, medecin, statut);
          });
          medecinSelect.addEventListener('change', () => {
            const { date, medecin, statut } = getFiltreValeurs();
            chargerRendezvous(date, medecin, statut);
          });
          statutSelect.addEventListener('change', () => {
            const { date, medecin, statut } = getFiltreValeurs();
            chargerRendezvous(date, medecin, statut);
          });
        }, 300);

        // Widgets dynamiques
        fetch('lister_rendezvous.php?stats=1')
            .then(r=>r.json())
            .then(data=>{
                if(data.success) {
                    document.getElementById('widget-rdv-jour').textContent = data.stats.rdv_aujourdhui;
                    document.getElementById('widget-nouveaux-patients').textContent = data.stats.nouveaux_patients;
                    document.getElementById('widget-consult-attente').textContent = data.stats.consultations_attente;
                    document.getElementById('widget-total-patients').textContent = data.stats.total_patients;
                    document.getElementById('widget-total-docteurs').textContent = data.stats.total_docteurs;
                    // Les évolutions sont à calculer côté back ou à masquer si non dispo
                    document.getElementById('widget-rdv-evol').textContent = '';
                    document.getElementById('widget-patients-evol').textContent = '';
                    document.getElementById('widget-consult-evol').textContent = '';
                }
            });
        // Prochains rendez-vous dynamiques (prochains 3)
        fetch('lister_rendezvous.php')
            .then(r=>r.json())
            .then(data=>{
                const cont = document.getElementById('dashboard-prochains-rdv');
                if(data.success && data.rendezvous.length > 0) {
                    cont.innerHTML = data.rendezvous.slice(0,3).map(r=>`
                        <div class="p-6 flex items-center hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/lego/2.jpg" alt="">
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">${r.patient_nom} ${r.patient_prenom}</h4>
                                    <span class="text-xs text-gray-500">${formatHeure(r.date_heure)}</span>
                                </div>
                                <p class="text-sm text-gray-500">${r.motif || ''}</p>
                            </div>
                            <div class="ml-4">
                                <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Voir</button>
                            </div>
                        </div>
                    `).join('');
                } else {
                    cont.innerHTML = '<div class="p-6 text-center text-gray-400">Aucun rendez-vous à venir</div>';
                }
            });

        // Dans loadDoctors(), pour le bouton Editer, ajouter la classe btn-edit-docteur et data-id :
        // <button class="flex-1 bg-gray-50 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-100 btn-edit-docteur" data-id="${d.doctor_id}">
        // ...
        // Après le code de loadDoctors(), ajouter :
        document.addEventListener('click', function(e) {
          if(e.target && e.target.classList.contains('btn-edit-docteur')) {
            const btn = e.target.closest('button');
            const doctorId = btn.getAttribute('data-id');
            // On récupère les infos du médecin dans le DOM (ou via AJAX si besoin)
            // Ici, on va utiliser le JSON déjà chargé (window._docteursList)
            const doctor = window._docteursList && window._docteursList.find(d => d.doctor_id == doctorId);
            if(doctor) {
              document.getElementById('edit-doctor-id').value = doctor.doctor_id;
              document.getElementById('edit-nom').value = doctor.nom;
              document.getElementById('edit-prenom').value = doctor.prenom;
              document.getElementById('edit-email').value = doctor.email;
              document.getElementById('edit-specialite').value = doctor.specialite || '';
              document.getElementById('edit-telephone').value = doctor.telephone || '';
              document.getElementById('edit-disponibilite').value = doctor.disponibilite || '';
              document.getElementById('edit-sexe').value = doctor.sexe || '';
              document.getElementById('edit-date_naissance').value = doctor.date_naissance || '';
              // On ne pré-remplit pas le mot de passe ni la photo
              document.getElementById('editDoctorModal').classList.remove('hidden');
            }
          }
          if(e.target && (e.target.id === 'closeEditDoctorModal' || e.target.id === 'closeEditDoctorModal2')) {
            document.getElementById('editDoctorModal').classList.add('hidden');
          }
        });

        // JS pour soumettre le formulaire d'édition
        const formEditDocteur = document.getElementById('form-edit-docteur');
        if(formEditDocteur) {
          formEditDocteur.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formEditDocteur);
            fetch('editer_docteur.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if(data.success) {
                alert('Médecin modifié avec succès !');
                formEditDocteur.reset();
                document.getElementById('editDoctorModal').classList.add('hidden');
                setTimeout(loadDoctors, 200);
              } else {
                alert('Erreur : ' + data.message);
              }
            })
            .catch(() => {
              alert('Erreur lors de la communication avec le serveur.');
            });
          });
        }

        // JS pour ouvrir la modale d'édition et pré-remplir les champs
        document.addEventListener('click', function(e) {
          if(e.target && e.target.classList.contains('btn-edit-patient')) {
            const btn = e.target.closest('button');
            const patientId = btn.getAttribute('data-id');
            const patient = window._patientsList && window._patientsList.find(p => p.patient_id == patientId);
            if(patient) {
              document.getElementById('edit-patient-id').value = patient.patient_id;
              document.getElementById('edit-patient-nom').value = patient.nom;
              document.getElementById('edit-patient-prenom').value = patient.prenom;
              document.getElementById('edit-patient-email').value = patient.email;
              document.getElementById('edit-patient-date_naissance').value = patient.date_naissance || '';
              document.getElementById('edit-patient-sexe').value = patient.sexe || '';
              document.getElementById('edit-patient-telephone').value = patient.telephone || '';
              document.getElementById('edit-patient-groupe_sanguin').value = patient.groupe_sanguin || '';
              document.getElementById('edit-patient-allergies').value = patient.allergies || '';
              document.getElementById('edit-patient-statut').value = patient.statut || 'actif';
              // On ne pré-remplit pas le mot de passe ni la photo
              document.getElementById('editPatientModal').classList.remove('hidden');
            }
          }
          if(e.target && (e.target.id === 'closeEditPatientModal' || e.target.id === 'closeEditPatientModal2')) {
            document.getElementById('editPatientModal').classList.add('hidden');
          }
        });

        // JS pour soumettre le formulaire d'édition
        const formEditPatient = document.getElementById('form-edit-patient');
        if(formEditPatient) {
          formEditPatient.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formEditPatient);
            fetch('editer_patient.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if(data.success) {
                alert('Patient modifié avec succès !');
                formEditPatient.reset();
                document.getElementById('editPatientModal').classList.add('hidden');
                setTimeout(loadPatients, 200);
              } else {
                alert('Erreur : ' + data.message);
              }
            })
            .catch(() => {
              alert('Erreur lors de la communication avec le serveur.');
            });
          });
        }

        // JS pour la suppression de patient
        document.addEventListener('click', function(e) {
          if(e.target && e.target.classList.contains('btn-supprimer-patient')) {
            if(confirm('Voulez-vous vraiment supprimer ce patient ?')) {
              const id = e.target.getAttribute('data-id');
              fetch('supprimer_patient.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
              })
              .then(response => response.json())
              .then(data => {
                if(data.success) {
                  alert('Patient supprimé avec succès !');
                  setTimeout(() => {
                    loadPatients();
                    // Actualiser le dashboard si affiché
                    if(document.querySelector('#page-content h2') && document.querySelector('#page-content h2').textContent.includes('Tableau de bord')) {
                      loadDashboard();
                    } else {
                      // Rafraîchir les widgets dynamiques si présents
                      fetch('lister_rendezvous.php?stats=1')
                        .then(r=>r.json())
                        .then(data=>{
                            if(data.success) {
                                document.getElementById('widget-rdv-jour').textContent = data.stats.rdv_aujourdhui;
                                document.getElementById('widget-nouveaux-patients').textContent = data.stats.nouveaux_patients;
                                document.getElementById('widget-consult-attente').textContent = data.stats.consultations_attente;
                                document.getElementById('widget-total-patients').textContent = data.stats.total_patients;
                                document.getElementById('widget-total-docteurs').textContent = data.stats.total_docteurs;
                                document.getElementById('widget-rdv-evol').textContent = '';
                                document.getElementById('widget-patients-evol').textContent = '';
                                document.getElementById('widget-consult-evol').textContent = '';
                            }
                        });
                    }
                  }, 200);
                } else {
                  alert('Erreur : ' + data.message);
                }
              })
              .catch(() => {
                alert('Erreur lors de la communication avec le serveur.');
              });
            }
          }
        });

        // Fonctions pour la gestion des rendez-vous
        function loadDoctorsForFilter() {
            fetch('get_admin_doctors.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('filter-doctor');
                        if (select) {
                            // Vider les options existantes sauf la première
                            select.innerHTML = '<option value="">Tous les médecins</option>';
                            
                            data.doctors.forEach(doctor => {
                                const option = document.createElement('option');
                                option.value = doctor.id;
                                option.textContent = doctor.name;
                                select.appendChild(option);
                            });
                        }
                    } else {
                        console.error('Erreur lors du chargement des médecins:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des médecins:', error);
                });
        }

        function loadAppointmentsData(page = 1) {
            const dateFilter = document.getElementById('filter-date').value;
            const doctorFilter = document.getElementById('filter-doctor').value;
            const statusFilter = document.getElementById('filter-status').value;

            const params = new URLSearchParams({
                page: page,
                date: dateFilter,
                doctor: doctorFilter,
                status: statusFilter
            });

            fetch(`get_admin_appointments.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAppointments(data.appointments);
                        updatePagination(data.pagination);
                    } else {
                        document.getElementById('rendezvous-tbody').innerHTML = 
                            '<tr><td colspan="6" class="text-center py-6 text-red-400">Erreur: ' + data.message + '</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des rendez-vous:', error);
                    document.getElementById('rendezvous-tbody').innerHTML = 
                        '<tr><td colspan="6" class="text-center py-6 text-red-400">Erreur de connexion au serveur</td></tr>';
                });
        }

        function displayAppointments(appointments) {
            const tbody = document.getElementById('rendezvous-tbody');
            
            if (appointments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-gray-400">Aucun rendez-vous trouvé</td></tr>';
                return;
            }

            tbody.innerHTML = appointments.map(appointment => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium">${appointment.patient_name.charAt(0)}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${appointment.patient_name}</div>
                                <div class="text-sm text-gray-500">${appointment.patient_phone || 'Téléphone non renseigné'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${appointment.date_heure}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${appointment.doctor_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${appointment.motif}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(appointment.statut)}">
                            ${getStatusLabel(appointment.statut)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3" onclick="viewAppointment(${appointment.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="text-green-600 hover:text-green-900 mr-3" onclick="editAppointment(${appointment.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-900" onclick="deleteAppointment(${appointment.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'confirmé':
                    return 'bg-green-100 text-green-800';
                case 'en attente':
                    return 'bg-yellow-100 text-yellow-800';
                case 'annulé':
                    return 'bg-red-100 text-red-800';
                case 'terminé':
                    return 'bg-blue-100 text-blue-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusLabel(status) {
            switch (status) {
                case 'confirmé':
                    return 'Confirmé';
                case 'en attente':
                    return 'En attente';
                case 'annulé':
                    return 'Annulé';
                case 'terminé':
                    return 'Terminé';
                default:
                    return status;
            }
        }

        function updatePagination(pagination) {
            const info = document.getElementById('pagination-info');
            const nav = document.getElementById('pagination-nav');
            
            const start = (pagination.current_page - 1) * pagination.per_page + 1;
            const end = Math.min(start + pagination.per_page - 1, pagination.total_count);
            
            info.innerHTML = `Affichage de <span class="font-medium">${start}</span> à <span class="font-medium">${end}</span> sur <span class="font-medium">${pagination.total_count}</span> résultats`;
            
            if (pagination.total_pages <= 1) {
                nav.innerHTML = '';
                return;
            }
            
            let navHtml = '';
            
            // Bouton précédent
            if (pagination.current_page > 1) {
                navHtml += `<a href="#" onclick="loadAppointmentsData(${pagination.current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Précédent</span>
                    <i class="fas fa-chevron-left"></i>
                </a>`;
            } else {
                navHtml += `<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                    <span class="sr-only">Précédent</span>
                    <i class="fas fa-chevron-left"></i>
                </span>`;
            }
            
            // Pages
            const startPage = Math.max(1, pagination.current_page - 2);
            const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    navHtml += `<span aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">${i}</span>`;
                } else {
                    navHtml += `<a href="#" onclick="loadAppointmentsData(${i})" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">${i}</a>`;
                }
            }
            
            // Bouton suivant
            if (pagination.current_page < pagination.total_pages) {
                navHtml += `<a href="#" onclick="loadAppointmentsData(${pagination.current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Suivant</span>
                    <i class="fas fa-chevron-right"></i>
                </a>`;
            } else {
                navHtml += `<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                    <span class="sr-only">Suivant</span>
                    <i class="fas fa-chevron-right"></i>
                </span>`;
            }
            
            nav.innerHTML = navHtml;
        }

        // Fonctions pour les actions sur les rendez-vous (à implémenter)
        function viewAppointment(id) {
            alert('Voir le rendez-vous ' + id + ' - Fonctionnalité à implémenter');
        }

        function editAppointment(id) {
            const newStatus = prompt('Changer le statut du rendez-vous:\n\n1. confirmé\n2. en attente\n3. annulé\n4. terminé\n\nEntrez le nouveau statut:');
            
            if (newStatus && ['confirmé', 'en attente', 'annulé', 'terminé'].includes(newStatus)) {
                const formData = new FormData();
                formData.append('appointment_id', id);
                formData.append('status', newStatus);
                
                fetch('update_admin_appointment_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Statut mis à jour avec succès !');
                        // Recharger les données
                        loadAppointmentsData();
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour du statut');
                });
            } else if (newStatus !== null) {
                alert('Statut invalide. Veuillez entrer: confirmé, en attente, annulé, ou terminé');
            }
        }

        function deleteAppointment(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
                alert('Supprimer le rendez-vous ' + id + ' - Fonctionnalité à implémenter');
            }
        }

        // Variables globales pour le calendrier
        let currentCalendarDate = new Date();
        let calendarAppointments = [];

        // Fonction pour initialiser le calendrier
        function initCalendar() {
            // Ajouter les événements de navigation
            document.getElementById('calendar-prev-month').addEventListener('click', () => {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                renderCalendar();
                loadCalendarAppointments();
            });

            document.getElementById('calendar-next-month').addEventListener('click', () => {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                renderCalendar();
                loadCalendarAppointments();
            });

            // Rendre le calendrier initial
            renderCalendar();
            loadCalendarAppointments();
        }

        // Fonction pour rendre le calendrier
        function renderCalendar() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            
            // Mettre à jour le titre
            const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                               'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            document.getElementById('calendar-month-year').textContent = `${monthNames[month]} ${year}`;
            
            // Obtenir le premier jour du mois et le nombre de jours
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            
            // Obtenir le jour de la semaine du premier jour (0 = dimanche, 1 = lundi, etc.)
            let firstDayOfWeek = firstDay.getDay();
            if (firstDayOfWeek === 0) firstDayOfWeek = 7; // Convertir dimanche (0) en 7 pour lundi = 1
            
            // Obtenir le dernier jour du mois précédent
            const lastDayPrevMonth = new Date(year, month, 0);
            const daysInPrevMonth = lastDayPrevMonth.getDate();
            
            // Générer la grille du calendrier
            let calendarHTML = '';
            
            // Jours du mois précédent
            for (let i = firstDayOfWeek - 1; i > 0; i--) {
                const day = daysInPrevMonth - i + 1;
                calendarHTML += `<div class="text-center py-2 text-sm text-gray-400">${day}</div>`;
            }
            
            // Jours du mois actuel
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const currentDate = new Date(year, month, day);
                const isToday = currentDate.toDateString() === today.toDateString();
                const dateString = currentDate.toISOString().split('T')[0];
                
                // Vérifier s'il y a des rendez-vous ce jour-là
                const dayAppointments = calendarAppointments.filter(apt => 
                    apt.date_heure.startsWith(dateString)
                );
                
                let dayClass = 'text-center py-2 text-sm cursor-pointer hover:bg-gray-100 rounded';
                let dayContent = day;
                
                if (isToday) {
                    dayClass += ' bg-indigo-100 text-indigo-600 font-medium';
                }
                
                // Ajouter des indicateurs pour les rendez-vous
                if (dayAppointments.length > 0) {
                    const confirmedCount = dayAppointments.filter(apt => apt.statut === 'confirmé').length;
                    const pendingCount = dayAppointments.filter(apt => apt.statut === 'en attente').length;
                    
                    if (confirmedCount > 0) {
                        dayClass += ' bg-green-100 text-green-800';
                        dayContent = `${day} <span class="text-xs">(${confirmedCount})</span>`;
                    } else if (pendingCount > 0) {
                        dayClass += ' bg-yellow-100 text-yellow-800';
                        dayContent = `${day} <span class="text-xs">(${pendingCount})</span>`;
                    }
                }
                
                calendarHTML += `<div class="${dayClass}" data-date="${dateString}" onclick="selectCalendarDate('${dateString}')">${dayContent}</div>`;
            }
            
            // Jours du mois suivant
            const remainingCells = 42 - (firstDayOfWeek - 1 + daysInMonth); // 42 = 6 semaines * 7 jours
            for (let day = 1; day <= remainingCells; day++) {
                calendarHTML += `<div class="text-center py-2 text-sm text-gray-400">${day}</div>`;
            }
            
            document.getElementById('calendar-grid').innerHTML = calendarHTML;
        }

        // Fonction pour charger les rendez-vous du calendrier
        function loadCalendarAppointments() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth() + 1;
            const monthStr = month.toString().padStart(2, '0');
            
            // Charger tous les rendez-vous du mois
            fetch(`lister_rendezvous.php?month=${year}-${monthStr}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calendarAppointments = data.rendezvous || [];
                        renderCalendar(); // Re-rendre pour afficher les indicateurs
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des rendez-vous du calendrier:', error);
                    calendarAppointments = [];
                });
        }

        // Fonction pour sélectionner une date dans le calendrier
        function selectCalendarDate(dateString) {
            // Mettre à jour le filtre de date
            const dateInput = document.getElementById('filter-date');
            if (dateInput) {
                dateInput.value = dateString;
                // Déclencher le filtre automatiquement
                loadAppointmentsData();
            }
        }

        // Rendre la fonction accessible globalement
        window.selectCalendarDate = selectCalendarDate;
        
        // Injection des variables PHP dans JavaScript
        window.adminUserInfo = {
            nom: '<?php echo addslashes($nom); ?>',
            prenom: '<?php echo addslashes($prenom); ?>',
            email: '<?php echo addslashes($email); ?>',
            telephone: '<?php echo addslashes($telephone); ?>',
            photo_url: '<?php echo addslashes($photo_url); ?>'
        };

        // Gestion du menu hamburger mobile - Version améliorée
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.getElementById('hamburger-menu');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const sidebarLinks = document.querySelectorAll('#sidebar a');

            console.log('Initialisation du menu hamburger:', {
                hamburgerMenu: !!hamburgerMenu,
                sidebar: !!sidebar,
                mobileOverlay: !!mobileOverlay
            });

            // Ouvrir/fermer le menu
            if (hamburgerMenu) {
                hamburgerMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Clic sur hamburger menu');
                    
                    const isHidden = sidebar.classList.contains('-translate-x-full');
                    console.log('Sidebar cachée:', isHidden);
                    
                    if (isHidden) {
                        // Ouvrir le menu
                        sidebar.classList.remove('-translate-x-full');
                        mobileOverlay.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                        console.log('Menu ouvert');
                    } else {
                        // Fermer le menu
                        sidebar.classList.add('-translate-x-full');
                        mobileOverlay.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                        console.log('Menu fermé');
                    }
                });
            }

            // Fermer le menu en cliquant sur l'overlay
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    console.log('Menu fermé via overlay');
                });
            }

            // Fermer le menu en cliquant sur un lien (mobile)
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('-translate-x-full');
                        mobileOverlay.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                        console.log('Menu fermé via lien');
                    }
                });
            });

            // Fermer le menu lors du redimensionnement de la fenêtre
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // Initialisation : s'assurer que le menu est fermé sur mobile au chargement
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                mobileOverlay.classList.add('hidden');
            }
        });

        // Fonction de test pour le menu hamburger
        function testHamburger() {
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const hamburgerMenu = document.getElementById('hamburger-menu');
            
            console.log('=== TEST HAMBURGER ===');
            console.log('Sidebar:', sidebar);
            console.log('Overlay:', mobileOverlay);
            console.log('Hamburger button:', hamburgerMenu);
            console.log('Sidebar classes:', sidebar?.className);
            console.log('Is hidden:', sidebar?.classList.contains('-translate-x-full'));
            console.log('Window width:', window.innerWidth);
            
            // Test d'ouverture manuelle
            if (sidebar && mobileOverlay) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                console.log('Menu ouvert manuellement');
                alert('Menu ouvert manuellement - Vérifiez s\'il est visible');
            }
        }
    </script>
</body>
</html>
