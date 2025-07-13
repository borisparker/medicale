<?php
require_once 'auth.php';
require_role('patient');
require_once 'db.php';

// Récupérer les infos du patient connecté
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT u.nom, u.prenom, u.photo, u.email FROM users u WHERE u.id = ?');
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
$photo_url = !empty($user_info['photo']) ? $user_info['photo'] : 'assets/images/default-user.png';
$nom = $user_info['nom'] ?? '';
$prenom = $user_info['prenom'] ?? '';
$email = $user_info['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Espace Patient | MediCare Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @keyframes zoomInOut {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.06); }
    }
    .animate-zoom-in-out {
      animation: zoomInOut 3s ease-in-out infinite;
    }
    
    /* Animation pour le menu mobile */
    @keyframes slideIn {
      from { transform: translateX(-100%); }
      to { transform: translateX(0); }
    }
    
    @keyframes slideOut {
      from { transform: translateX(0); }
      to { transform: translateX(-100%); }
    }
    
    .sidebar-mobile {
      animation: slideIn 0.3s ease-out;
    }
    
    .sidebar-mobile.hidden {
      animation: slideOut 0.3s ease-in;
    }
    
    /* Overlay pour mobile */
    .sidebar-overlay {
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
    }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    
    <!-- Sidebar Mobile -->
    <div id="sidebar-mobile" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-800 via-indigo-700 to-indigo-900 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden">
        <div class="flex items-center justify-center h-20 px-4 bg-indigo-900 shadow-lg">
            <div class="flex items-center gap-2">
                <img src="assets/images/logo.jpg" alt="Logo" class="h-10 w-10 rounded-full shadow-lg border-2 border-pink-400 bg-white object-cover">
                <span class="text-xl font-extrabold tracking-wide text-white">Vaidya Mitra</span>
            </div>
        </div>
        <div class="flex flex-col flex-grow px-4 py-6 overflow-y-auto">
            <nav class="flex-1 space-y-2">
                <a href="#" class="group flex items-center px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg transition-all duration-300 ease-in-out relative sidebar-link-mobile">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-400 rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                    <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                    <span class="font-semibold text-base">Tableau de bord</span>
                </a>
                <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link-mobile">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-blue-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                    <i class="fas fa-calendar-alt mr-3 text-xl group-hover:scale-125 group-hover:text-blue-400 transition-transform duration-300"></i>
                    <span class="font-semibold text-base">Mes rendez-vous</span>
                </a>
                <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link-mobile">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                    <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                    <span class="font-semibold text-base">Mon dossier médical</span>
                </a>
                <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link-mobile">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-purple-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                    <i class="fas fa-prescription-bottle-alt mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                    <span class="font-semibold text-base">Mes ordonnances</span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-indigo-700 bg-indigo-900 shadow-inner">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-pink-400 to-indigo-600 text-white text-xl font-extrabold shadow-lg uppercase">
                    <?php echo strtoupper(mb_substr($prenom,0,1) . mb_substr($nom,0,1)); ?>
                </div>
                <div>
                    <p class="text-base font-bold text-white leading-tight"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                    <p class="text-xs text-indigo-200">Patient</p>
                    <a href="logout.php" class="block mt-2 text-xs text-pink-300 hover:text-pink-500 font-semibold transition"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 min-h-screen bg-gradient-to-b from-indigo-800 via-indigo-700 to-indigo-900 shadow-2xl transition-all duration-500 ease-in-out">
                <div class="flex items-center justify-center h-20 px-4 bg-indigo-900 shadow-lg">
                    <div class="flex items-center gap-2">
                        <img src="assets/images/logo.jpg" alt="Logo" class="h-10 w-10 rounded-full shadow-lg border-2 border-pink-400 bg-white object-cover">
                        <span class="text-2xl font-extrabold tracking-wide text-white">Vaidya Mitra</span>
                    </div>
                </div>
                <div class="flex flex-col flex-grow px-4 py-6 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-400 rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Tableau de bord</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-blue-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-calendar-alt mr-3 text-xl group-hover:scale-125 group-hover:text-blue-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Mes rendez-vous</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Mon dossier médical</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-purple-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-prescription-bottle-alt mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Mes ordonnances</span>
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-indigo-700 bg-indigo-900 shadow-inner animate-fadeIn">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-pink-400 to-indigo-600 text-white text-xl font-extrabold shadow-lg uppercase">
                            <?php echo strtoupper(mb_substr($prenom,0,1) . mb_substr($nom,0,1)); ?>
                        </div>
                        <div>
                            <p class="text-base font-bold text-white leading-tight"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                            <p class="text-xs text-indigo-200">Patient</p>
                            <a href="logout.php" class="block mt-2 text-xs text-pink-300 hover:text-pink-500 font-semibold transition"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16 px-4 md:px-6 bg-white border-b border-gray-200">
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 max-w-md ml-4 md:ml-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="w-full py-2 pl-10 pr-4 text-sm bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-bell text-lg"></i>
                    </button>
                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-envelope text-lg"></i>
                    </button>
                    <div class="relative">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-indigo-600 text-white text-base font-bold uppercase shadow">
                                <?php echo strtoupper(mb_substr($prenom,0,1) . mb_substr($nom,0,1)); ?>
                            </div>
                            <span class="text-sm md:text-base font-bold text-gray-800 hidden sm:block"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-4 md:p-6">
                <div id="page-content"></div>
            </div>
        </div>
    </div>

    <script>
        // Navigation dynamique patient
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du menu mobile
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebarMobile = document.getElementById('sidebar-mobile');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            // Ouvrir le menu mobile
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    sidebarMobile.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            // Fermer le menu mobile
            function closeMobileMenu() {
                sidebarMobile.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            // Fermer en cliquant sur l'overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeMobileMenu);
            }
            
            // Fermer avec la touche Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
            
            // Fermer le menu mobile lors du redimensionnement vers desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                }
            });
            
            // Gestion des liens du menu mobile
            document.querySelectorAll('.sidebar-link-mobile').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.querySelector('i').className.match(/fa-([a-z-]+)/)[1];
                    
                    // Mettre à jour l'état actif
                    document.querySelectorAll('.sidebar-link-mobile').forEach(el => {
                        el.classList.remove('bg-indigo-600', 'text-white');
                        el.classList.add('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                    });
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
                        case 'file-medical':
                            loadMedicalRecord();
                            break;
                        case 'prescription-bottle-alt':
                            loadPrescriptions();
                            break;
                        case 'comments':
                            loadMessages();
                            break;
                    }
                    
                    // Fermer le menu mobile
                    closeMobileMenu();
                });
            });
            const params = new URLSearchParams(window.location.search);
            const page = params.get('page');
            switch(page) {
                case 'rdv':
                    loadAppointments();
                    break;
                case 'messages':
                    loadMessages();
                    break;
                case 'dossier':
                    loadMedicalRecord();
                    break;
                default:
                    loadDashboard();
            }
            document.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.querySelector('i').className.match(/fa-([a-z-]+)/)[1];
                    document.querySelectorAll('nav a').forEach(el => {
                        el.classList.remove('bg-indigo-600', 'text-white');
                        el.classList.add('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                    });
                    this.classList.add('bg-indigo-600', 'text-white');
                    this.classList.remove('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                    switch(page) {
                        case 'tachometer-alt':
                            loadDashboard();
                            break;
                        case 'calendar-alt':
                            loadAppointments();
                            break;
                        case 'file-medical':
                            loadMedicalRecord();
                            break;
                        case 'prescription-bottle-alt':
                            loadPrescriptions();
                            break;
                        case 'comments':
                            loadMessages();
                            break;
                    }
                });
            });
            function loadDashboard() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-indigo-900 tracking-tight mb-1 flex items-center gap-2">
                            <i class="fas fa-tachometer-alt text-pink-500 animate-pulse"></i> Tableau de bord
                        </h2>
                        
                        <div class="flex justify-center mb-6 md:mb-8 relative">
                            <img src="assets/images/bbb.jpg" alt="Bienvenue" class="w-full h-auto rounded-2xl md:rounded-3xl shadow-xl animate-zoom-in-out" style="object-fit:cover;">
                            <div class="absolute inset-0 flex flex-col justify-center items-center">
                                <div class="bg-black bg-opacity-40 rounded-xl md:rounded-2xl px-4 md:px-8 py-3 md:py-4">
                                    <p class="text-white text-lg md:text-2xl lg:text-3xl font-bold text-center drop-shadow">
                                        Bienvenue sur votre espace patient.<br class="hidden sm:block">
                                        Retrouvez ici un aperçu de vos activités médicales.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Notifications -->
                    <div id="dashboard-notifications" class="mb-6 md:mb-8"></div>
                    <!-- Statistiques -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:scale-[1.02] md:hover:scale-[1.03] hover:shadow-2xl transition-transform duration-300 group border-t-4 border-blue-400 relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-blue-700 text-xs md:text-sm font-semibold uppercase tracking-wide truncate">Rendez-vous à venir</p>
                                    <h3 class="text-2xl md:text-3xl font-extrabold mt-1 md:mt-2 text-blue-900 drop-shadow" id="upcoming-appointments">-</h3>
                                    <p class="text-xs text-blue-500 mt-1 truncate" id="next-appointment-date">-</p>
                                </div>
                                <div class="p-2 md:p-3 rounded-full bg-blue-200 text-blue-700 shadow group-hover:bg-blue-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-lg md:text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:scale-[1.02] md:hover:scale-[1.03] hover:shadow-2xl transition-transform duration-300 group border-t-4 border-green-400 relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-green-700 text-xs md:text-sm font-semibold uppercase tracking-wide truncate">Dernière consultation</p>
                                    <h3 class="text-2xl md:text-3xl font-extrabold mt-1 md:mt-2 text-green-900 drop-shadow" id="last-consultation">-</h3>
                                    <p class="text-xs text-green-500 mt-1 truncate" id="last-doctor">-</p>
                                </div>
                                <div class="p-2 md:p-3 rounded-full bg-green-200 text-green-700 shadow group-hover:bg-green-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-file-medical text-lg md:text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:scale-[1.02] md:hover:scale-[1.03] hover:shadow-2xl transition-transform duration-300 group border-t-4 border-purple-400 relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-purple-700 text-xs md:text-sm font-semibold uppercase tracking-wide truncate">Ordonnances actives</p>
                                    <h3 class="text-2xl md:text-3xl font-extrabold mt-1 md:mt-2 text-purple-900 drop-shadow" id="active-prescriptions">-</h3>
                                    <p class="text-xs text-purple-500 mt-1 truncate" id="prescriptions-expiring">-</p>
                                </div>
                                <div class="p-2 md:p-3 rounded-full bg-purple-200 text-purple-700 shadow group-hover:bg-purple-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-prescription-bottle-alt text-lg md:text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:scale-[1.02] md:hover:scale-[1.03] hover:shadow-2xl transition-transform duration-300 group border-t-4 border-yellow-400 relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-yellow-700 text-xs md:text-sm font-semibold uppercase tracking-wide truncate">Messages non lus</p>
                                    <h3 class="text-2xl md:text-3xl font-extrabold mt-1 md:mt-2 text-yellow-900 drop-shadow" id="unread-messages">-</h3>
                                    <p class="text-xs text-yellow-500 mt-1 truncate" id="last-message">-</p>
                                </div>
                                <div class="p-2 md:p-3 rounded-full bg-yellow-200 text-yellow-700 shadow group-hover:bg-yellow-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-comments text-lg md:text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actions rapides -->
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 mb-6 md:mb-8 border border-indigo-100">
                        <h3 class="text-lg md:text-xl font-bold text-indigo-800 mb-4 md:mb-6 flex items-center gap-2"><i class="fas fa-bolt text-pink-400 animate-bounce"></i> Actions rapides</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                            <a href="patient.php?page=rdv" class="flex items-center p-4 md:p-5 border border-indigo-200 rounded-xl bg-gradient-to-br from-indigo-50 to-white hover:from-indigo-100 hover:to-indigo-200 hover:shadow-lg transition-all group">
                                <div class="p-2 md:p-3 bg-indigo-200 rounded-xl mr-3 md:mr-4 group-hover:bg-indigo-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-calendar-plus text-indigo-700 text-lg md:text-xl"></i>
                                </div>
                                <div class="text-left min-w-0 flex-1">
                                    <p class="font-bold text-indigo-900 text-sm md:text-base truncate">Prendre rendez-vous</p>
                                    <p class="text-xs md:text-sm text-indigo-500 truncate">Réserver une consultation</p>
                                </div>
                            </a>
                            <a href="patient.php?page=dossier" class="flex items-center p-4 md:p-5 border border-purple-200 rounded-xl bg-gradient-to-br from-purple-50 to-white hover:from-purple-100 hover:to-purple-200 hover:shadow-lg transition-all group">
                                <div class="p-2 md:p-3 bg-purple-200 rounded-xl mr-3 md:mr-4 group-hover:bg-purple-400 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="fas fa-file-medical text-purple-700 text-lg md:text-xl"></i>
                                </div>
                                <div class="text-left min-w-0 flex-1">
                                    <p class="font-bold text-purple-900 text-sm md:text-base truncate">Voir mon dossier</p>
                                    <p class="text-xs md:text-sm text-purple-500 truncate">Consulter l'historique</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- Prochains rendez-vous -->
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 mb-6 md:mb-8 border border-blue-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base md:text-lg font-bold text-blue-900 flex items-center gap-2"><i class="fas fa-calendar text-blue-400"></i> Prochains rendez-vous</h3>
                            <button onclick="loadAppointments()" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-semibold transition">Voir tout</button>
                        </div>
                        <div class="border-t border-blue-100 mb-4"></div>
                        <div id="upcoming-appointments-list" class="space-y-3"></div>
                    </div>
                    <!-- Activité récente -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 border border-green-100">
                            <h3 class="text-base md:text-lg font-bold text-green-900 mb-4 flex items-center gap-2"><i class="fas fa-stethoscope text-green-400"></i> Dernières consultations</h3>
                            <div class="border-t border-green-100 mb-4"></div>
                            <div id="recent-consultations" class="space-y-3"></div>
                        </div>
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 border border-purple-100">
                            <h3 class="text-base md:text-lg font-bold text-purple-900 mb-4 flex items-center gap-2"><i class="fas fa-prescription text-purple-400"></i> Ordonnances récentes</h3>
                            <div class="border-t border-purple-100 mb-4"></div>
                            <div id="recent-prescriptions" class="space-y-3"></div>
                        </div>
                    </div>
                `;
                // Charger les données du tableau de bord
                loadDashboardData();
            }
            
            function loadDashboardData() {
                // Charger les statistiques générales
                fetch('get_dashboard_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateDashboardStats(data.stats);
                        } else {
                            console.error('Erreur stats:', data.message);
                            updateDashboardStats({
                                upcoming_appointments: 0,
                                last_consultation: 'Aucune',
                                last_doctor: '-',
                                active_prescriptions: 0,
                                prescriptions_expiring: '-',
                                unread_messages: 0,
                                last_message: '-'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Erreur chargement stats:', err);
                        // Utiliser des données par défaut en cas d'erreur
                        updateDashboardStats({
                            upcoming_appointments: 0,
                            last_consultation: 'Aucune',
                            last_doctor: '-',
                            active_prescriptions: 0,
                            prescriptions_expiring: '-',
                            unread_messages: 0,
                            last_message: '-'
                        });
                    });
                
                // Charger les prochains rendez-vous
                loadUpcomingAppointments();
                
                // Charger l'activité récente
                loadRecentActivity();
                
                // Charger les notifications
                loadNotifications();
                
                // Programmer le rafraîchissement automatique (toutes les 5 minutes)
                setTimeout(refreshDashboard, 300000);
            }
            
            function refreshDashboard() {
                console.log('🔄 Rafraîchissement automatique du tableau de bord...');
                loadDashboardData();
            }
            
            // Fonction pour rafraîchir manuellement
            function manualRefresh() {
                const refreshBtn = document.getElementById('refresh-dashboard');
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
                
                loadDashboardData();
                
                setTimeout(() => {
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                    }
                }, 2000);
            }
            
            function updateDashboardStats(stats) {
                document.getElementById('upcoming-appointments').textContent = stats.upcoming_appointments || 0;
                document.getElementById('next-appointment-date').textContent = stats.next_appointment_date || '-';
                document.getElementById('last-consultation').textContent = stats.last_consultation || 'Aucune';
                document.getElementById('last-doctor').textContent = stats.last_doctor || '-';
                document.getElementById('active-prescriptions').textContent = stats.active_prescriptions || 0;
                document.getElementById('prescriptions-expiring').textContent = stats.prescriptions_expiring || '-';
                document.getElementById('unread-messages').textContent = stats.unread_messages || 0;
                document.getElementById('last-message').textContent = stats.last_message || '-';
            }
            
            function loadUpcomingAppointments() {
                fetch('get_appointments.php?limit=3')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('upcoming-appointments-list');
                        
                        if (data.success && data.appointments && data.appointments.length > 0) {
                            container.innerHTML = '';
                            data.appointments.forEach(appointment => {
                                const appointmentHtml = `
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                                                <i class="fas fa-calendar text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">${appointment.date} à ${appointment.heure}</p>
                                                <p class="text-sm text-gray-500">Dr. ${appointment.medecin} - ${appointment.motif}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ${appointment.statut_class}">${appointment.statut}</span>
                                    </div>
                                `;
                                container.insertAdjacentHTML('beforeend', appointmentHtml);
                            });
                        } else {
                            container.innerHTML = `
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                    <p>Aucun rendez-vous à venir</p>
                                    <button onclick="loadAppointments()" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Prendre rendez-vous</button>
                                </div>
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Erreur chargement rendez-vous:', err);
                        document.getElementById('upcoming-appointments-list').innerHTML = `
                            <div class="text-center text-red-500 py-8">
                                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                <p>Erreur lors du chargement</p>
                            </div>
                        `;
                    });
            }
            
            function loadRecentActivity() {
                // Charger les consultations récentes
                fetch('get_patient_medical_record.php?limit=3')
                    .then(response => response.json())
                    .then(data => {
                        const consultationsContainer = document.getElementById('recent-consultations');
                        
                        if (data.success && data.consultations && data.consultations.length > 0) {
                            consultationsContainer.innerHTML = '';
                            data.consultations.slice(0, 3).forEach(consultation => {
                                const consultationHtml = `
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                                <i class="fas fa-stethoscope text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">${consultation.type}</p>
                                                <p class="text-sm text-gray-500">${consultation.doctor_name} - ${consultation.date_consultation}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Terminé</span>
                                    </div>
                                `;
                                consultationsContainer.insertAdjacentHTML('beforeend', consultationHtml);
                            });
                        } else {
                            consultationsContainer.innerHTML = `
                                <div class="text-center text-gray-500 py-4">
                                    <p>Aucune consultation récente</p>
                                </div>
                            `;
                        }
                        
                        // Charger les ordonnances récentes
                        if (data.success && data.prescriptions && data.prescriptions.length > 0) {
                            const prescriptionsContainer = document.getElementById('recent-prescriptions');
                            prescriptionsContainer.innerHTML = '';
                            data.prescriptions.slice(0, 3).forEach(prescription => {
                                const prescriptionHtml = `
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                                <i class="fas fa-prescription text-purple-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Ordonnance du ${prescription.date_creation}</p>
                                                <p class="text-sm text-gray-500">${prescription.doctor_name}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Active</span>
                                    </div>
                                `;
                                prescriptionsContainer.insertAdjacentHTML('beforeend', prescriptionHtml);
                            });
                        } else {
                            document.getElementById('recent-prescriptions').innerHTML = `
                                <div class="text-center text-gray-500 py-4">
                                    <p>Aucune ordonnance récente</p>
                                </div>
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Erreur chargement activité récente:', err);
                        document.getElementById('recent-consultations').innerHTML = `
                            <div class="text-center text-red-500 py-4">
                                <p>Erreur lors du chargement</p>
                            </div>
                        `;
                        document.getElementById('recent-prescriptions').innerHTML = `
                            <div class="text-center text-red-500 py-4">
                                <p>Erreur lors du chargement</p>
                            </div>
                        `;
                    });
            }
            
            function loadNotifications() {
                fetch('get_notifications.php')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('dashboard-notifications');
                        
                        if (data.success && data.notifications && data.notifications.length > 0) {
                            container.innerHTML = '';
                            data.notifications.forEach(notification => {
                                const colorClass = notification.type === 'warning' ? 'yellow' : 
                                                 notification.type === 'success' ? 'green' : 'blue';
                                
                                const notificationHtml = `
                                    <div class="bg-${colorClass}-50 border border-${colorClass}-200 rounded-lg p-4 mb-3 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas ${notification.icon} text-${colorClass}-600 text-lg"></i>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm text-${colorClass}-800 font-medium">${notification.message}</p>
                                                    ${notification.details ? `<p class="text-xs text-${colorClass}-600 mt-1">${notification.details}</p>` : ''}
                                                    ${notification.date ? `<p class="text-xs text-${colorClass}-500 mt-1">${notification.date}</p>` : ''}
                                                </div>
                                            </div>
                                            ${notification.action ? `
                                                <button onclick="${notification.action}" class="ml-3 text-${colorClass}-600 hover:text-${colorClass}-800 text-sm font-medium">
                                                    Voir <i class="fas fa-arrow-right ml-1"></i>
                                                </button>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
                                container.insertAdjacentHTML('beforeend', notificationHtml);
                            });
                        } else {
                            container.innerHTML = `
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-800">Tout va bien ! Aucune notification importante.</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Erreur chargement notifications:', err);
                        const container = document.getElementById('dashboard-notifications');
                        container.innerHTML = '';
                    });
            }
            function loadAppointments() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-indigo-900 tracking-tight mb-1 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-500 animate-pulse"></i> Mes rendez-vous
                            </h2>
                            <p class="text-gray-500 text-sm md:text-base">Consultez et gérez vos rendez-vous médicaux.</p>
                        </div>
                        <button id="btn-open-form" class="bg-gradient-to-r from-blue-500 via-indigo-500 to-pink-500 hover:from-indigo-600 hover:to-blue-400 text-white px-4 md:px-6 py-2 rounded-xl shadow-lg flex items-center gap-2 font-semibold transition-all duration-300 focus:ring-2 focus:ring-blue-300 text-sm md:text-base">
                            <i class="fas fa-plus"></i> Prendre rendez-vous
                        </button>
                    </div>
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 mb-6 md:mb-8 border border-blue-100">
                        <div id="rdv-loading" class="text-center text-blue-400 py-8 flex flex-col items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                            <span class="text-lg font-semibold">Chargement des rendez-vous...</span>
                        </div>
                        <div id="rdv-error" class="text-center text-red-500 py-4 hidden"></div>
                        <div id="rdv-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-calendar-times text-5xl mb-4"></i>
                            <p class="text-lg font-semibold">Aucun rendez-vous trouvé.</p>
                            <p class="text-sm">Prenez votre premier rendez-vous en cliquant sur le bouton ci-dessus.</p>
                        </div>
                        <div id="rdv-table-container" class="overflow-x-auto">
                            <table id="rdv-table" class="min-w-full divide-y divide-blue-100 hidden">
                                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                    <tr>
                                        <th class="px-3 md:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date</th>
                                        <th class="px-3 md:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider hidden sm:table-cell">Heure</th>
                                        <th class="px-3 md:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Médecin</th>
                                        <th class="px-3 md:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider hidden md:table-cell">Motif</th>
                                        <th class="px-3 md:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Statut</th>
                                        <th class="px-3 md:px-6 py-3 text-right text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rdv-tbody" class="bg-white divide-y divide-blue-50"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Modal Formulaire de rendez-vous -->
                    <div id="modal-form-rdv" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden p-4">
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg relative border-2 border-blue-100 animate-fadeIn">
                            <button id="btn-close-form" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
                            <h3 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2"><i class="fas fa-calendar-plus text-blue-500"></i> Prendre un rendez-vous</h3>
                            <form id="form-rdv" class="space-y-5">
                                <div>
                                    <label for="doctor_id" class="block text-sm font-semibold text-gray-700 mb-1">Médecin <span class="text-red-500">*</span></label>
                                    <select id="doctor_id" name="doctor_id" class="w-full border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                                        <option value="">Sélectionner un médecin</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_heure" class="block text-sm font-semibold text-gray-700 mb-1">Date et heure <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="date_heure" name="date_heure" class="w-full border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                                </div>
                                <div>
                                    <label for="motif" class="block text-sm font-semibold text-gray-700 mb-1">Motif <span class="text-red-500">*</span></label>
                                    <input type="text" id="motif" name="motif" placeholder="Ex : Consultation, renouvellement..." class="w-full border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                                </div>
                                <div>
                                    <label for="commentaire" class="block text-sm font-semibold text-gray-700 mb-1">Commentaire (optionnel)</label>
                                    <textarea id="commentaire" name="commentaire" rows="2" placeholder="Ajouter un commentaire..." class="w-full border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"></textarea>
                                </div>
                                <div id="rdv-message" class="text-center text-sm font-semibold"></div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" id="btn-cancel-form" class="bg-white border border-blue-200 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-50 transition">Annuler</button>
                                    <button type="submit" class="bg-gradient-to-r from-blue-500 via-indigo-500 to-pink-500 hover:from-indigo-600 hover:to-blue-400 text-white font-bold px-5 py-2 rounded-lg shadow-lg transition flex items-center gap-2 focus:ring-2 focus:ring-blue-300">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Modal Détails du rendez-vous -->
                    <div id="modal-details-rdv" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden p-4">
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-2xl relative border-2 border-indigo-100 animate-fadeIn">
                            <button id="btn-close-details" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
                            <h3 class="text-2xl font-extrabold mb-6 text-indigo-700 flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> Détails du rendez-vous</h3>
                            <div id="rdv-details-content" class="space-y-6">
                                <!-- Le contenu sera chargé dynamiquement -->
                            </div>
                            <div class="flex justify-end mt-6">
                                <button id="btn-close-details-footer" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                                    <i class="fas fa-times"></i> Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                // Fonction pour charger les rendez-vous
                function loadAppointmentsData() {
                    const loading = document.getElementById('rdv-loading');
                    const table = document.getElementById('rdv-table');
                    const tbody = document.getElementById('rdv-tbody');
                    const error = document.getElementById('rdv-error');
                    const empty = document.getElementById('rdv-empty');
                    // Réinitialiser l'affichage
                    loading.classList.remove('hidden');
                    table.classList.add('hidden');
                    error.classList.add('hidden');
                    empty.classList.add('hidden');
                    tbody.innerHTML = '';
                    fetch('get_appointments.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            loading.classList.add('hidden');
                            if (data.success) {
                                if (data.appointments && data.appointments.length > 0) {
                                    table.classList.remove('hidden');
                                    data.appointments.forEach((rdv, index) => {
                                        const statusColors = {
                                            'À venir': 'bg-blue-100 text-blue-800',
                                            'Terminé': 'bg-green-100 text-green-800',
                                            'Annulé': 'bg-red-100 text-red-800',
                                            'En attente': 'bg-yellow-100 text-yellow-800',
                                        };
                                        const icon = rdv.statut === 'Terminé' ? 'fa-check-circle' : rdv.statut === 'Annulé' ? 'fa-times-circle' : rdv.statut === 'En attente' ? 'fa-hourglass-half' : 'fa-calendar-alt';
                                        const tr = document.createElement('tr');
                                        tr.className = 'hover:bg-blue-50 transition';
                                        tr.innerHTML = `
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap font-semibold text-blue-900 text-sm md:text-base">${rdv.date}</td>
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-blue-700 text-sm hidden sm:table-cell">${rdv.heure}</td>
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-indigo-700 font-medium text-sm md:text-base">${rdv.medecin}</td>
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-gray-700 text-sm hidden md:table-cell">${rdv.motif || '-'}</td>
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full ${statusColors[rdv.statut] || 'bg-gray-100 text-gray-700'}">
                                                    <i class="fas ${icon}"></i> <span class="hidden sm:inline">${rdv.statut}</span>
                                                </span>
                                            </td>
                                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-right text-xs md:text-sm font-medium">
                                                <button class="text-blue-600 hover:text-blue-900 mr-2 md:mr-3 font-bold transition btn-details" data-id="${rdv.id}"><i class="fas fa-eye"></i> <span class="hidden sm:inline">Détails</span></button>
                                                ${rdv.statut !== 'Annulé' && rdv.statut !== 'Terminé' ? 
                                                    `<button class="text-red-600 hover:text-red-900 font-bold transition btn-annuler" data-id="${rdv.id}"><i class="fas fa-times"></i> <span class="hidden sm:inline">Annuler</span></button>` : 
                                                    '<span class="text-gray-400 text-xs md:text-sm">Non annulable</span>'
                                                }
                                            </td>
                                        `;
                                        tbody.appendChild(tr);
                                    });
                                    
                                    // Ajouter les gestionnaires d'événements pour les boutons Annuler
                                    tbody.querySelectorAll('.btn-annuler').forEach(btn => {
                                        btn.onclick = function() {
                                            if(confirm("Voulez-vous vraiment annuler ce rendez-vous ?")) {
                                                const rdvId = this.getAttribute('data-id');
                                                const originalText = this.innerHTML;
                                                this.disabled = true;
                                                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Annulation...';
                                                
                                                fetch('annuler_rdv.php', {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json' },
                                                    body: JSON.stringify({ id: rdvId })
                                                })
                                                .then(res => res.json())
                                                .then(res => {
                                                    if(res.success) {
                                                        alert("Rendez-vous annulé avec succès !");
                                                        loadAppointmentsData();
                                                    } else {
                                                        alert(res.message || "Erreur lors de l'annulation.");
                                                        this.disabled = false;
                                                        this.innerHTML = originalText;
                                                    }
                                                })
                                                .catch(err => {
                                                    alert("Erreur lors de l'annulation.");
                                                    this.disabled = false;
                                                    this.innerHTML = originalText;
                                                });
                                            }
                                        };
                                    });
                                    
                                    // Ajouter les gestionnaires d'événements pour les boutons Détails
                                    tbody.querySelectorAll('.btn-details').forEach(btn => {
                                        btn.onclick = function() {
                                            const rdvId = this.getAttribute('data-id');
                                            showAppointmentDetails(rdvId);
                                        };
                                    });
                                } else {
                                    empty.classList.remove('hidden');
                                }
                            } else {
                                error.textContent = data.message || 'Erreur lors du chargement des rendez-vous.';
                                error.classList.remove('hidden');
                            }
                        })
                        .catch(err => {
                            loading.classList.add('hidden');
                            error.textContent = 'Erreur lors du chargement des rendez-vous: ' + err.message;
                            error.classList.remove('hidden');
                        });
                }
                // Charger les rendez-vous au démarrage
                loadAppointmentsData();
                
                // Fonction pour afficher les détails d'un rendez-vous
                function showAppointmentDetails(rdvId) {
                    const modal = document.getElementById('modal-details-rdv');
                    const content = document.getElementById('rdv-details-content');
                    
                    // Afficher le modal avec un indicateur de chargement
                    modal.classList.remove('hidden');
                    content.innerHTML = `
                        <div class="text-center text-indigo-400 py-8 flex flex-col items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                            <span class="text-lg font-semibold">Chargement des détails...</span>
                        </div>
                    `;
                    
                    // Charger les détails du rendez-vous
                    fetch(`get_appointment_details.php?id=${rdvId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const rdv = data.appointment;
                                const statusColors = {
                                    'À venir': 'bg-blue-100 text-blue-800',
                                    'Terminé': 'bg-green-100 text-green-800',
                                    'Annulé': 'bg-red-100 text-red-800',
                                    'En attente': 'bg-yellow-100 text-yellow-800',
                                };
                                const icon = rdv.statut === 'Terminé' ? 'fa-check-circle' : rdv.statut === 'Annulé' ? 'fa-times-circle' : rdv.statut === 'En attente' ? 'fa-hourglass-half' : 'fa-calendar-alt';
                                
                                content.innerHTML = `
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                        <div>
                                            <h4 class="text-lg font-bold text-indigo-800 mb-3 flex items-center gap-2">
                                                <i class="fas fa-calendar-day text-indigo-500"></i> Informations générales
                                            </h4>
                                            <div class="space-y-3">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-calendar-alt text-indigo-400"></i>
                                                    <span class="font-semibold text-gray-700">Date :</span>
                                                    <span class="text-indigo-900">${rdv.date}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-clock text-indigo-400"></i>
                                                    <span class="font-semibold text-gray-700">Heure :</span>
                                                    <span class="text-indigo-900">${rdv.heure}</span>
                                                </div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <i class="fas fa-info-circle text-indigo-400"></i>
                                                    <span class="font-semibold text-gray-700">Statut :</span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full ${statusColors[rdv.statut] || 'bg-gray-100 text-gray-700'}">
                                                        <i class="fas ${icon}"></i> ${rdv.statut}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-hashtag text-indigo-400"></i>
                                                    <span class="font-semibold text-gray-700">ID :</span>
                                                    <span class="text-purple-900 font-mono">#${rdv.id}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-calendar-plus text-indigo-400"></i>
                                                    <span class="font-semibold text-gray-700">Créé le :</span>
                                                    <span class="text-purple-900">${rdv.date_creation || 'Non disponible'}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-8 md:mt-0">
                                            <h4 class="text-lg font-bold text-blue-800 mb-3 flex items-center gap-2">
                                                <i class="fas fa-user-md text-blue-500"></i> Médecin
                                            </h4>
                                            <div class="space-y-3">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-user text-blue-400"></i>
                                                    <span class="font-semibold text-gray-700">Nom :</span>
                                                    <span class="text-blue-900">${rdv.medecin}</span>
                                                </div>
                                                ${rdv.doctor_specialite ? `
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-stethoscope text-blue-400"></i>
                                                    <span class="font-semibold text-gray-700">Spécialité :</span>
                                                    <span class="text-blue-900">${rdv.doctor_specialite}</span>
                                                </div>
                                                ` : ''}
                                                ${rdv.doctor_telephone ? `
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-phone text-blue-400"></i>
                                                    <span class="font-semibold text-gray-700">Téléphone :</span>
                                                    <span class="text-blue-900">${rdv.doctor_telephone}</span>
                                                </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-6 border-t border-gray-200"></div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                        <div class="mb-6 md:mb-0">
                                            <h4 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
                                                <i class="fas fa-file-alt text-green-500"></i> Motif
                                            </h4>
                                            <div class="bg-green-50 border-l-4 md:border-l-4 border-green-400 rounded p-4 text-green-900">
                                                ${rdv.motif || 'Non spécifié'}
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-yellow-800 mb-3 flex items-center gap-2">
                                                <i class="fas fa-comment text-yellow-500"></i> Commentaire
                                            </h4>
                                            <div class="bg-yellow-50 border-l-4 md:border-l-4 border-yellow-400 rounded p-4 text-yellow-900">
                                                ${rdv.commentaire || '<span class=\"italic text-gray-400\">Aucun commentaire</span>'}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            } else {
                                content.innerHTML = `
                                    <div class="text-center text-red-500 py-8">
                                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                                        <p class="text-lg font-semibold">Erreur lors du chargement</p>
                                        <p class="text-sm">${data.message || 'Impossible de charger les détails du rendez-vous.'}</p>
                                    </div>
                                `;
                            }
                        })
                        .catch(err => {
                            console.error('Erreur chargement détails:', err);
                            content.innerHTML = `
                                <div class="text-center text-red-500 py-8">
                                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                                    <p class="text-lg font-semibold">Erreur de connexion</p>
                                    <p class="text-sm">Impossible de charger les détails du rendez-vous.</p>
                                </div>
                            `;
                        });
                }
                
                // Affichage/fermeture du modal
                setTimeout(() => {
                    const btnOpen = document.getElementById('btn-open-form');
                    const btnClose = document.getElementById('btn-close-form');
                    const btnCancel = document.getElementById('btn-cancel-form');
                    const modal = document.getElementById('modal-form-rdv');
                    if(btnOpen && modal) {
                        btnOpen.onclick = () => { modal.classList.remove('hidden'); };
                    }
                    if(btnClose && modal) {
                        btnClose.onclick = () => { modal.classList.add('hidden'); };
                    }
                    if(btnCancel && modal) {
                        btnCancel.onclick = () => { modal.classList.add('hidden'); };
                    }
                    
                    // Gestionnaires pour le modal de détails
                    const btnCloseDetails = document.getElementById('btn-close-details');
                    const btnCloseDetailsFooter = document.getElementById('btn-close-details-footer');
                    const modalDetails = document.getElementById('modal-details-rdv');
                    
                    if(btnCloseDetails && modalDetails) {
                        btnCloseDetails.onclick = () => { modalDetails.classList.add('hidden'); };
                    }
                    if(btnCloseDetailsFooter && modalDetails) {
                        btnCloseDetailsFooter.onclick = () => { modalDetails.classList.add('hidden'); };
                    }
                    
                    // Fermer le modal en cliquant à l'extérieur
                    if(modalDetails) {
                        modalDetails.onclick = function(e) {
                            if(e.target === modalDetails) {
                                modalDetails.classList.add('hidden');
                            }
                        };
                    }
                    
                    // Remplir dynamiquement la liste des docteurs
                    fetch('get_doctors.php')
                        .then(response => response.json())
                        .then(data => {
                            const select = document.getElementById('doctor_id');
                            if (select) {
                                select.innerHTML = '<option value="">Sélectionner un médecin</option>';
                                data.forEach(doctor => {
                                    const option = document.createElement('option');
                                    option.value = doctor.id;
                                    option.textContent = doctor.nom;
                                    select.appendChild(option);
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Erreur chargement docteurs:', err);
                        });
                    // Soumission AJAX du formulaire
                    const form = document.getElementById('form-rdv');
                    if(form) {
                        form.onsubmit = function(e) {
                            e.preventDefault();
                            const message = document.getElementById('rdv-message');
                            message.textContent = '';
                            message.className = 'text-center text-sm font-semibold';
                            const btn = form.querySelector('button[type="submit"]');
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
                            const data = {
                                doctor_id: form.doctor_id.value,
                                date_heure: form.date_heure.value,
                                motif: form.motif.value,
                                commentaire: form.commentaire.value
                            };
                            fetch('ajouter_rdv.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data)
                            })
                            .then(res => res.json())
                            .then(res => {
                                if(res.success) {
                                    message.textContent = res.message;
                                    message.className = 'text-green-600 text-center text-sm font-semibold my-2';
                                    setTimeout(() => {
                                        loadAppointmentsData();
                                        modal.classList.add('hidden');
                                        message.textContent = '';
                                        form.reset();
                                    }, 1000);
                                } else {
                                    message.textContent = res.message || 'Erreur lors de l\'enregistrement.';
                                    message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                                    console.error('Erreur ajouter_rdv:', res);
                                }
                            })
                            .catch(err => {
                                message.textContent = 'Erreur lors de l\'envoi du formulaire.';
                                message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                                console.error('Erreur fetch ajouter_rdv:', err);
                            })
                            .finally(() => {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-check"></i> Valider';
                            });
                        };
                    }
                }, 100);
            }
            function loadMedicalRecord() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-indigo-900 tracking-tight mb-1 flex items-center gap-2">
                            <i class="fas fa-file-medical text-green-500 animate-pulse"></i> Mon dossier médical
                        </h2>
                        <p class="text-gray-500 text-sm md:text-base">Retrouvez l'historique de vos consultations et traitements.</p>
                    </div>
                    <div id="medical-record-loading" class="text-center text-indigo-400 py-8 flex flex-col items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <span class="text-lg font-semibold">Chargement du dossier médical...</span>
                    </div>
                    <div id="medical-record-content" class="hidden">
                        <!-- Résumé du dossier -->
                        <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 mb-6 md:mb-8 border-l-4 border-indigo-400">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4">
                                <h3 class="text-lg md:text-xl font-bold text-indigo-800 flex items-center gap-2"><i class="fas fa-clipboard-list text-indigo-400"></i> Résumé du dossier</h3>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs md:text-sm font-bold flex items-center gap-2"><i class="fas fa-check-circle"></i> Actif</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                                <div class="text-center p-3 md:p-4 bg-white rounded-xl shadow group hover:shadow-lg transition-all">
                                    <div class="text-2xl md:text-3xl font-extrabold text-indigo-600" id="consultations-count">0</div>
                                    <div class="text-xs md:text-sm text-gray-600">Consultations</div>
                                </div>
                                <div class="text-center p-3 md:p-4 bg-white rounded-xl shadow group hover:shadow-lg transition-all">
                                    <div class="text-2xl md:text-3xl font-extrabold text-green-600" id="prescriptions-count">0</div>
                                    <div class="text-xs md:text-sm text-gray-600">Ordonnances</div>
                                </div>
                                <div class="text-center p-3 md:p-4 bg-white rounded-xl shadow group hover:shadow-lg transition-all">
                                    <div class="text-2xl md:text-3xl font-extrabold text-purple-600" id="last-consultation-date">-</div>
                                    <div class="text-xs md:text-sm text-gray-600">Dernière consultation</div>
                                </div>
                            </div>
                        </div>
                        <!-- Consultations -->
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 mb-6 md:mb-8 border-l-4 border-green-400">
                            <h3 class="text-base md:text-lg font-bold text-green-900 mb-4 md:mb-6 flex items-center gap-2"><i class="fas fa-stethoscope text-green-400"></i> Historique des consultations</h3>
                            <div id="consultations-container" class="space-y-4 md:space-y-6"></div>
                            <div id="consultations-empty" class="text-center text-gray-500 py-6 md:py-8 hidden">
                                <i class="fas fa-file-medical text-4xl md:text-5xl mb-4"></i>
                                <p class="text-base md:text-lg font-semibold">Aucune consultation trouvée.</p>
                                <p class="text-xs md:text-sm">Vos consultations apparaîtront ici après vos rendez-vous.</p>
                            </div>
                        </div>
                        <!-- Ordonnances -->
                        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-6 md:p-8 border-l-4 border-purple-400">
                            <h3 class="text-base md:text-lg font-bold text-purple-900 mb-4 md:mb-6 flex items-center gap-2"><i class="fas fa-prescription-bottle-alt text-purple-400"></i> Mes ordonnances</h3>
                            <div id="prescriptions-container" class="space-y-4 md:space-y-6"></div>
                            <div id="prescriptions-empty" class="text-center text-gray-500 py-6 md:py-8 hidden">
                                <i class="fas fa-prescription-bottle-alt text-4xl md:text-5xl mb-4"></i>
                                <p class="text-base md:text-lg font-semibold">Aucune ordonnance trouvée.</p>
                                <p class="text-xs md:text-sm">Vos ordonnances apparaîtront ici après vos consultations.</p>
                            </div>
                        </div>
                    </div>
                    <div id="medical-record-error" class="text-center text-red-500 py-4 hidden"></div>
                `;
                loadMedicalRecordData();
            }
            
            function loadMedicalRecordData() {
                const loading = document.getElementById('medical-record-loading');
                const content = document.getElementById('medical-record-content');
                const error = document.getElementById('medical-record-error');
                loading.classList.remove('hidden');
                content.classList.add('hidden');
                error.classList.add('hidden');
                fetch('get_patient_medical_record.php')
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');
                        if (data.success) {
                            content.classList.remove('hidden');
                            document.getElementById('consultations-count').textContent = data.consultations_count;
                            document.getElementById('prescriptions-count').textContent = data.prescriptions_count;
                            if (data.consultations && data.consultations.length > 0) {
                                const lastConsultation = data.consultations[0];
                                document.getElementById('last-consultation-date').textContent = lastConsultation.date_consultation;
                            }
                            displayConsultations(data.consultations);
                            displayPrescriptions(data.prescriptions);
                        } else {
                            error.textContent = data.message || 'Erreur lors du chargement du dossier médical.';
                            error.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        loading.classList.add('hidden');
                        error.textContent = 'Erreur lors du chargement du dossier médical: ' + err.message;
                        error.classList.remove('hidden');
                    });
            }
            
            function displayConsultations(consultations) {
                const container = document.getElementById('consultations-container');
                const empty = document.getElementById('consultations-empty');
                if (consultations && consultations.length > 0) {
                    empty.classList.add('hidden');
                    container.innerHTML = '';
                    consultations.forEach(consultation => {
                        const consultationHtml = `
                            <div class="bg-gradient-to-br from-green-50 to-white border-l-4 border-green-400 rounded-xl md:rounded-2xl shadow p-4 md:p-6 hover:shadow-2xl transition-all duration-300 group">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-green-900 text-base md:text-lg flex items-center gap-2"><i class="fas fa-stethoscope text-green-400"></i> ${consultation.type}</h5>
                                        <p class="text-xs md:text-sm text-gray-600 truncate">${consultation.doctor_name} <span class='text-xs text-gray-400'>${consultation.date_consultation}</span></p>
                                        ${consultation.doctor_specialite ? `<p class="text-xs text-gray-500">${consultation.doctor_specialite}</p>` : ''}
                                    </div>
                                    <div class="flex-shrink-0 ml-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle"></i> <span class="hidden sm:inline">Terminé</span></span>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs md:text-sm text-gray-700">
                                    <p class="font-medium text-green-800 mb-1">Motif :</p>
                                    <p class="break-words">${consultation.motif}</p>
                                    <p class="font-medium mt-2 text-green-800">Observations :</p>
                                    <p class="break-words">${consultation.observations}</p>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', consultationHtml);
                    });
                } else {
                    container.innerHTML = '';
                    empty.classList.remove('hidden');
                }
            }
            
            function displayPrescriptions(prescriptions) {
                const container = document.getElementById('prescriptions-container');
                const empty = document.getElementById('prescriptions-empty');
                if (prescriptions && prescriptions.length > 0) {
                    empty.classList.add('hidden');
                    container.innerHTML = '';
                    prescriptions.forEach((prescription, idx) => {
                        const medicationsList = prescription.medications.map(med =>
                            `<li class='flex items-center gap-2'><i class='fas fa-capsules text-indigo-400'></i> <span class='font-semibold text-indigo-900'>${med.nom}</span> <span class='text-xs text-gray-500'>${med.dosage}</span> <span class='text-xs text-gray-400'>x${med.quantite}</span></li>`
                        ).join('');
                        const statusBadge = `<span class='inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800'><i class='fas fa-check-circle'></i> Active</span>`;
                        const prescriptionHtml = `
                            <div class="bg-gradient-to-br from-purple-50 to-white border-l-4 border-purple-400 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 group relative">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="font-bold text-purple-900 text-lg flex items-center gap-2"><i class="fas fa-prescription-bottle-alt text-purple-400"></i> Ordonnance du ${prescription.date_creation}</h5>
                                        <p class="text-sm text-gray-600">${prescription.doctor_name} <span class='text-xs text-gray-400'>(Consultation: ${prescription.consultation_date})</span></p>
                                        <p class="text-xs text-gray-500">${prescription.consultation_type}</p>
                                    </div>
                                    ${statusBadge}
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p class="font-medium text-indigo-800 mb-1">Médicaments :</p>
                                    <ul class="list-none pl-0 space-y-1">${medicationsList}</ul>
                                    ${prescription.details ? `
                                        <p class="font-medium mt-3 text-indigo-800">Instructions :</p>
                                        <p class="text-gray-700">${prescription.details}</p>
                                    ` : ''}
                                </div>
                                <div class="mt-4 flex space-x-3">
                                    <button class="btn-prescription-details text-purple-700 hover:text-white hover:bg-purple-500 border border-purple-200 rounded-lg px-4 py-2 text-sm font-semibold flex items-center gap-2 transition-all duration-200 shadow group-hover:shadow-lg" data-idx="${idx}"><i class="fas fa-eye"></i> Voir détails</button>
                                    <button class="text-indigo-700 hover:text-white hover:bg-indigo-500 border border-indigo-200 rounded-lg px-4 py-2 text-sm font-semibold flex items-center gap-2 transition-all duration-200 shadow group-hover:shadow-lg"><i class="fas fa-download"></i> Télécharger</button>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', prescriptionHtml);
                    });
                    // Ajout gestionnaire d'événement pour chaque bouton "Voir détails"
                    const btns = container.querySelectorAll('.btn-prescription-details');
                    btns.forEach(btn => {
                        btn.onclick = function() {
                            const idx = this.getAttribute('data-idx');
                            showPrescriptionDetails(prescriptions[idx]);
                        };
                    });
                } else {
                    container.innerHTML = '';
                    empty.classList.remove('hidden');
                }
            }
            // Fonction pour afficher les détails d'une ordonnance dans un modal
            function showPrescriptionDetails(prescription) {
                const modal = document.getElementById('modal-prescription-details');
                const content = document.getElementById('prescription-details-content');
                modal.classList.remove('hidden');
                let medicationsList = prescription.medications.map(med =>
                    `<li class='flex items-center gap-2'><i class='fas fa-capsules text-indigo-400'></i> <span class='font-semibold text-indigo-900'>${med.nom}</span> <span class='text-xs text-gray-500'>${med.dosage}</span> <span class='text-xs text-gray-400'>x${med.quantite}</span></li>`
                ).join('');
                content.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-purple-400"></i>
                            <span class="font-semibold text-gray-700">Date :</span>
                            <span class="text-purple-900">${prescription.date_creation}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-md text-purple-400"></i>
                            <span class="font-semibold text-gray-700">Médecin :</span>
                            <span class="text-purple-900">${prescription.doctor_name}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-stethoscope text-purple-400"></i>
                            <span class="font-semibold text-gray-700">Consultation :</span>
                            <span class="text-purple-900">${prescription.consultation_type} (${prescription.consultation_date})</span>
                        </div>
                    </div>
                    <div class="my-4">
                        <p class="font-medium text-indigo-800 mb-1">Médicaments :</p>
                        <ul class="list-none pl-0 space-y-1">${medicationsList}</ul>
                    </div>
                    ${prescription.details ? `
                        <div class="my-2">
                            <p class="font-medium text-indigo-800 mb-1">Instructions :</p>
                            <p class="text-gray-700">${prescription.details}</p>
                        </div>
                    ` : ''}
                `;
            }
            // Gestion fermeture du modal
            setTimeout(() => {
                const modal = document.getElementById('modal-prescription-details');
                const btnClose = document.getElementById('btn-close-prescription-details');
                const btnCloseFooter = document.getElementById('btn-close-prescription-details-footer');
                if(btnClose && modal) btnClose.onclick = () => modal.classList.add('hidden');
                if(btnCloseFooter && modal) btnCloseFooter.onclick = () => modal.classList.add('hidden');
                if(modal) {
                    modal.onclick = function(e) {
                        if(e.target === modal) modal.classList.add('hidden');
                    };
                }
                
                // Gestion fermeture des autres modals
                const modalForm = document.getElementById('modal-form-rdv');
                const modalDetails = document.getElementById('modal-details-rdv');
                
                if(modalForm) {
                    modalForm.onclick = function(e) {
                        if(e.target === modalForm) modalForm.classList.add('hidden');
                    };
                }
                
                if(modalDetails) {
                    modalDetails.onclick = function(e) {
                        if(e.target === modalDetails) modalDetails.classList.add('hidden');
                    };
                }
            }, 100);
            function loadPrescriptions() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-purple-900 tracking-tight mb-1 flex items-center gap-2">
                            <i class="fas fa-prescription-bottle-alt text-purple-500 animate-pulse"></i> Mes ordonnances
                        </h2>
                        <p class="text-gray-500 text-sm md:text-base">Liste de vos ordonnances actives et passées.</p>
                    </div>
                    <div id="prescriptions-loading" class="text-center text-purple-400 py-8 flex flex-col items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <span class="text-lg font-semibold">Chargement des ordonnances...</span>
                    </div>
                    <div id="prescriptions-content" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                            <div id="prescriptions-container" class="col-span-full space-y-4 md:space-y-6"></div>
                        </div>
                        <div id="prescriptions-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-prescription-bottle-alt text-5xl mb-4"></i>
                            <p class="text-lg font-semibold">Aucune ordonnance trouvée.</p>
                            <p class="text-sm">Vos ordonnances apparaîtront ici après vos consultations.</p>
                        </div>
                    </div>
                    <div id="prescriptions-error" class="text-center text-red-500 py-4 hidden"></div>
                `;
                loadPrescriptionsData();
            }
            
            function loadPrescriptionsData() {
                const loading = document.getElementById('prescriptions-loading');
                const content = document.getElementById('prescriptions-content');
                const error = document.getElementById('prescriptions-error');
                loading.classList.remove('hidden');
                content.classList.add('hidden');
                error.classList.add('hidden');
                fetch('get_patient_medical_record.php')
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');
                        if (data.success) {
                            content.classList.remove('hidden');
                            displayPrescriptionsList(data.prescriptions);
                        } else {
                            error.textContent = data.message || 'Erreur lors du chargement des ordonnances.';
                            error.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        loading.classList.add('hidden');
                        error.textContent = 'Erreur lors du chargement des ordonnances: ' + err.message;
                        error.classList.remove('hidden');
                    });
            }
            
            function displayPrescriptionsList(prescriptions) {
                const container = document.getElementById('prescriptions-container');
                const empty = document.getElementById('prescriptions-empty');
                if (prescriptions && prescriptions.length > 0) {
                    empty.classList.add('hidden');
                    container.innerHTML = '';
                    prescriptions.forEach((prescription, idx) => {
                        const medicationsList = prescription.medications.map(med =>
                            `<li class='flex items-center gap-2'><i class='fas fa-capsules text-indigo-400'></i> <span class='font-semibold text-indigo-900'>${med.nom}</span> <span class='text-xs text-gray-500'>${med.dosage}</span> <span class='text-xs text-gray-400'>x${med.quantite}</span></li>`
                        ).join('');
                        const statusBadge = `<span class='inline-flex items-center gap-1 px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800'><i class='fas fa-check-circle'></i> Active</span>`;
                        const prescriptionHtml = `
                            <div class="bg-gradient-to-br from-purple-50 to-white border-l-4 border-purple-400 rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:shadow-2xl transition-all duration-300 group relative">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-purple-900 text-base md:text-lg flex items-center gap-2"><i class="fas fa-prescription-bottle-alt text-purple-400"></i> Ordonnance du ${prescription.date_creation}</h5>
                                        <p class="text-xs md:text-sm text-gray-600 truncate">${prescription.doctor_name} <span class='text-xs text-gray-400'>(Consultation: ${prescription.consultation_date})</span></p>
                                        <p class="text-xs text-gray-500">${prescription.consultation_type}</p>
                                    </div>
                                    <div class="flex-shrink-0 ml-2">
                                        ${statusBadge}
                                    </div>
                                </div>
                                <div class="mt-2 text-xs md:text-sm text-gray-700">
                                    <p class="font-medium text-indigo-800 mb-1">Médicaments :</p>
                                    <ul class="list-none pl-0 space-y-1">${medicationsList}</ul>
                                    ${prescription.details ? `
                                        <p class="font-medium mt-3 text-indigo-800">Instructions :</p>
                                        <p class="text-gray-700">${prescription.details}</p>
                                    ` : ''}
                                </div>
                                <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                                    <button class="btn-prescription-details text-purple-700 hover:text-white hover:bg-purple-500 border border-purple-200 rounded-lg px-3 md:px-4 py-2 text-xs md:text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-200 shadow group-hover:shadow-lg" data-idx="${idx}"><i class="fas fa-eye"></i> <span class="hidden sm:inline">Voir détails</span></button>
                                    <a href="download_prescription.php?id=${prescription.id}" class="text-indigo-700 hover:text-white hover:bg-indigo-500 border border-indigo-200 rounded-lg px-3 md:px-4 py-2 text-xs md:text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-200 shadow group-hover:shadow-lg" target="_blank"><i class="fas fa-download"></i> <span class="hidden sm:inline">Télécharger</span></a>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', prescriptionHtml);
                    });
                    // Ajout gestionnaire d'événement pour chaque bouton "Voir détails"
                    const btns = container.querySelectorAll('.btn-prescription-details');
                    btns.forEach(btn => {
                        btn.onclick = function() {
                            const idx = this.getAttribute('data-idx');
                            showPrescriptionDetails(prescriptions[idx]);
                        };
                    });
                } else {
                    container.innerHTML = '';
                    empty.classList.remove('hidden');
                }
            }
            function loadMessages() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Messagerie</h2>
                        <p class="text-gray-600 text-sm md:text-base">Discutez avec votre médecin ou le secrétariat.</p>
                    </div>
                    <div class="bg-white rounded-xl md:rounded-lg shadow p-4 md:p-6">
                        <div class="mb-4">
                            <input type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm md:text-base" placeholder="Nouveau message...">
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <img class="w-8 h-8 rounded-full mr-3 flex-shrink-0" src="https://randomuser.me/api/portraits/men/45.jpg" alt="">
                                <div class="min-w-0 flex-1">
                                    <div class="bg-indigo-100 text-indigo-800 rounded-lg px-3 md:px-4 py-2 mb-1 text-sm md:text-base break-words">Bonjour Docteur, j'ai une question sur mon traitement.</div>
                                    <div class="text-xs text-gray-400">Aujourd'hui, 09:15</div>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <img class="w-8 h-8 rounded-full mr-3 flex-shrink-0" src="https://randomuser.me/api/portraits/women/44.jpg" alt="">
                                <div class="min-w-0 flex-1">
                                    <div class="bg-gray-100 text-gray-800 rounded-lg px-3 md:px-4 py-2 mb-1 text-sm md:text-base break-words">Bonjour, je vous écoute !</div>
                                    <div class="text-xs text-gray-400">Aujourd'hui, 09:17</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            function loadProfile() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Mon profil</h2>
                        <p class="text-gray-600">Gérez vos informations personnelles.</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <form>
                            <div class="space-y-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        <img class="h-16 w-16 rounded-full" src="https://randomuser.me/api/portraits/men/45.jpg" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                                            Changer la photo
                                        </button>
                                        <p class="text-xs text-gray-500 mt-2">JPG, GIF ou PNG. Taille max: 2MB</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                        <input type="text" value="Jean" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                        <input type="text" value="Patient" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" value="jean.patient@email.com" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                                    <input type="tel" value="+33 6 12 34 56 78" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-3 hover:bg-gray-50">
                                        Annuler
                                    </button>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                                        Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                `;
            }
        });
    </script>
    <script>
        // Fonction pour naviguer vers une section et mettre à jour la sidebar
        function navigateToSection(section) {
            // Met à jour la sidebar
            document.querySelectorAll('nav a').forEach(el => {
                el.classList.remove('bg-indigo-600', 'text-white');
                el.classList.add('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                el.querySelector('.sidebar-indicator').classList.add('opacity-0');
            });

            // Trouver le lien correspondant dans la sidebar
            let targetLink = null;
            document.querySelectorAll('nav a').forEach(link => {
                const icon = link.querySelector('i');
                if (icon && icon.className.includes('fa-' + section)) {
                    targetLink = link;
                }
            });
            if (targetLink) {
                targetLink.classList.add('bg-indigo-600', 'text-white');
                targetLink.classList.remove('text-indigo-200', 'hover:bg-indigo-600', 'hover:text-white');
                targetLink.querySelector('.sidebar-indicator').classList.remove('opacity-0');
            }

            // Charger la page correspondante
            switch(section) {
                case 'calendar-alt':
                    loadAppointments();
                    break;
                case 'comments':
                    loadMessages();
                    break;
                case 'file-medical':
                    loadMedicalRecord();
                    break;
                case 'prescription-bottle-alt':
                    loadPrescriptions();
                    break;
            }
        }
        // Animation JS pour l'indicateur actif
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                sidebarLinks.forEach(l => {
                    l.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg');
                    l.querySelector('.sidebar-indicator').classList.add('opacity-0');
                });
                this.classList.add('bg-indigo-600', 'text-white', 'shadow-lg');
                this.querySelector('.sidebar-indicator').classList.remove('opacity-0');
            });
        });
    </script>
    <div id="modal-prescription-details" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden p-4">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg relative border-2 border-purple-100 animate-fadeIn">
            <button id="btn-close-prescription-details" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
            <h3 class="text-xl md:text-2xl font-extrabold mb-4 md:mb-6 text-purple-700 flex items-center gap-2"><i class="fas fa-prescription-bottle-alt text-purple-500"></i> Détails de l'ordonnance</h3>
            <div id="prescription-details-content" class="space-y-6"></div>
            <div class="flex justify-end mt-6">
                <button id="btn-close-prescription-details-footer" class="bg-purple-600 hover:bg-purple-700 text-white px-4 md:px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2 text-sm md:text-base">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</body>
</html> 