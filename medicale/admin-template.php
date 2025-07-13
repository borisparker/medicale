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
$photo_url = 'https://ui-avatars.com/api/?name=' . urlencode($prenom . '+' . $nom) . '&background=6366f1&color=fff&size=128&font-size=0.4&bold=true';
if (!empty($user_info['photo'])) {
    $photo_file = basename($user_info['photo']);
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/medicale/uploads/' . $photo_file;
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/uploads/' . $photo_file;
    }
}

// Variables pour personnaliser le template
$page_title = $page_title ?? 'Administration';
$current_page = $current_page ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gestion Clinique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dossiers_medicaux.css">
    <link rel="stylesheet" href="admin-responsive.css">
    <script src="admin_settings_improved.js"></script>
    <script src="admin-responsive.js"></script>
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
                        <a href="admin.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'dashboard' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'dashboard' ? 'bg-pink-400' : 'bg-green-400 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Tableau de bord</span>
                        </a>
                        <a href="lister_patients.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'patients' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'patients' ? 'bg-green-400' : 'bg-green-400 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-injured mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Patients</span>
                        </a>
                        <a href="lister_dossiers_medicaux.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'dossiers' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'dossiers' ? 'bg-purple-400' : 'bg-purple-400 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Dossiers médicaux</span>
                        </a>
                        <a href="lister_medicaments.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'medicaments' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'medicaments' ? 'bg-yellow-400' : 'bg-yellow-400 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-pills mr-3 text-xl group-hover:scale-125 group-hover:text-yellow-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médicaments</span>
                        </a>
                        <a href="lister_docteurs.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'docteurs' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'docteurs' ? 'bg-pink-300' : 'bg-pink-300 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-md mr-3 text-xl group-hover:scale-125 group-hover:text-pink-300 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médecins</span>
                        </a>
                        <a href="lister_rendezvous.php" class="group flex items-center px-4 py-3 rounded-xl <?php echo $current_page === 'rendezvous' ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600 hover:text-white'; ?> transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 <?php echo $current_page === 'rendezvous' ? 'bg-blue-400' : 'bg-blue-400 opacity-0 group-hover:opacity-100'; ?> rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-calendar-alt mr-3 text-xl group-hover:scale-125 group-hover:text-blue-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Rendez-vous</span>
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
                    <button id="hamburger-menu" class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
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
                <!-- Le contenu spécifique à chaque page sera injecté ici -->
                <div id="page-content">
                    <?php if (isset($page_content)): ?>
                        <?php echo $page_content; ?>
                    <?php else: ?>
                        <!-- Contenu par défaut -->
                        <div class="animate-fadeIn">
                            <h1 class="text-3xl font-bold text-gray-900 mb-6"><?php echo htmlspecialchars($page_title); ?></h1>
                            <div class="responsive-card">
                                <p class="text-gray-600">Contenu de la page à définir.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- Injection des variables PHP dans JavaScript -->
    <script>
        window.adminUserInfo = {
            nom: '<?php echo addslashes($nom); ?>',
            prenom: '<?php echo addslashes($prenom); ?>',
            email: '<?php echo addslashes($email); ?>',
            telephone: '<?php echo addslashes($telephone); ?>',
            photo_url: '<?php echo addslashes($photo_url); ?>'
        };
        
        window.currentPage = '<?php echo $current_page; ?>';
    </script>
</body>
</html> 