<?php
require_once 'auth.php';
require_role('docteur');
require_once 'db.php';

// Récupérer les infos du docteur connecté
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT nom, prenom, photo, email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
$nom = $user_info['nom'] ?? '';
$prenom = $user_info['prenom'] ?? '';
$photo_url = !empty($user_info['photo']) ? '/medicale/uploads/' . basename($user_info['photo']) : '/medicale/assets/images/default-user.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Espace Docteur | MediCare Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        /* Masquer la barre de défilement */
        .overflow-y-auto::-webkit-scrollbar {
            display: none;
        }
        .overflow-y-auto {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Animation fadeIn */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Animation pulse pour l'icône */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 md:static md:inset-0 transition duration-300 ease-in-out">
            <div class="flex flex-col w-full min-h-screen bg-gradient-to-b from-indigo-800 via-indigo-700 to-indigo-900 shadow-2xl">
                <div class="flex items-center justify-between h-20 px-4 bg-indigo-900 shadow-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-heartbeat text-3xl animate-pulse text-pink-400 drop-shadow-lg"></i>
                        <span class="text-2xl font-extrabold tracking-wide text-white">Vaidya Mitra</span>
                    </div>
                    <button id="sidebar-close" class="md:hidden text-white hover:text-pink-300 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-400 rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Tableau de bord</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-blue-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-injured mr-3 text-xl group-hover:scale-125 group-hover:text-blue-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Mes patients</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-calendar-alt mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Mes rendez-vous</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-purple-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Consultations</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-yellow-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-prescription-bottle-alt mr-3 text-xl group-hover:scale-125 group-hover:text-yellow-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Ordonnances</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-red-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-chart-bar mr-3 text-xl group-hover:scale-125 group-hover:text-red-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Statistiques</span>
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user mr-3 text-xl group-hover:scale-125 group-hover:text-pink-300 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Profil</span>
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-indigo-700 bg-indigo-900 shadow-inner animate-fadeIn">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full border-2 border-pink-400 shadow-lg transition-transform duration-300 hover:scale-110 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white font-bold text-lg sidebar-profile-initials">
                            <?php echo strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)); ?>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-bold text-white"><?php echo htmlspecialchars('Dr. ' . $prenom . ' ' . $nom); ?></p>
                            <p class="text-xs text-indigo-200">Cardiologue</p>
                            <a href="logout.php" class="block mt-2 text-xs text-pink-300 hover:text-pink-500 font-semibold transition"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
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
                    <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 focus:outline-none transition">
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
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-sm border-2 border-indigo-300 shadow header-profile-initials">
                                <?php echo strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)); ?>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6">
                <div id="page-content"></div>
            </div>
        </div>
    </div>

    <!-- Overlay pour mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

    <script>
        // Gestion du menu hamburger mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            // Fonction pour ouvrir la sidebar
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebarOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Empêcher le scroll
            }
            
            // Fonction pour fermer la sidebar
            function closeSidebar() {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restaurer le scroll
            }
            
            // Gestionnaire pour le bouton hamburger
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    openSidebar();
                });
            }
            
            // Gestionnaire pour l'overlay (fermer en cliquant dessus)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Gestionnaire pour le bouton de fermeture de la sidebar
            const sidebarClose = document.getElementById('sidebar-close');
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Fermer la sidebar en cliquant sur un lien (mobile)
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768) { // Seulement sur mobile
                    if (e.target.closest('.sidebar-link')) {
                        closeSidebar();
                    }
                }
            });
            
            // Fermer la sidebar lors du redimensionnement de la fenêtre
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });
        });

        // Fonctions globales pour les consultations et ordonnances
        function viewConsultation(id) {
            alert('Voir consultation ' + id + ' - Fonctionnalité à implémenter');
        }
        
        function editConsultation(id) {
            // Charger les infos de la consultation
            fetch('get_consultation.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Erreur lors du chargement de la consultation.');
                        return;
                    }
                    const c = data.consultation;
                    // Créer le modal
                    const modalHtml = `
                        <div id="modal-edit-consultation" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-xl relative border border-indigo-100">
                                <button id="btn-close-edit-consultation" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
                                <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center gap-2"><i class="fas fa-edit text-indigo-500"></i> Editer la consultation</h3>
                                <form id="edit-consultation-form" class="space-y-5">
                                    <input type="hidden" name="id" value="${c.id}">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Type de consultation</label>
                                        <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                            <option value="">Sélectionner le type</option>
                                            <option value="Consultation générale" ${c.type==='Consultation générale'?'selected':''}>Consultation générale</option>
                                            <option value="Consultation spécialisée" ${c.type==='Consultation spécialisée'?'selected':''}>Consultation spécialisée</option>
                                            <option value="Suivi traitement" ${c.type==='Suivi traitement'?'selected':''}>Suivi traitement</option>
                                            <option value="Bilan de santé" ${c.type==='Bilan de santé'?'selected':''}>Bilan de santé</option>
                                            <option value="Urgence" ${c.type==='Urgence'?'selected':''}>Urgence</option>
                                            <option value="Contrôle" ${c.type==='Contrôle'?'selected':''}>Contrôle</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Motif</label>
                                        <input type="text" name="motif" value="${c.motif || ''}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Observations</label>
                                        <textarea name="observations" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>${c.observations || ''}</textarea>
                                    </div>
                                    <div id="edit-consultation-message" class="text-center text-sm font-semibold"></div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" id="btn-cancel-edit-consultation" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Annuler</button>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2 rounded-lg shadow-lg transition flex items-center gap-2 focus:ring-2 focus:ring-indigo-300">
                                            <i class="fas fa-check mr-2"></i> Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    // Gestion fermeture
                    document.getElementById('btn-close-edit-consultation').onclick = closeEditConsultationModal;
                    document.getElementById('btn-cancel-edit-consultation').onclick = closeEditConsultationModal;
                    function closeEditConsultationModal() {
                        document.getElementById('modal-edit-consultation').remove();
                    }
                    // Gestion soumission
                    document.getElementById('edit-consultation-form').onsubmit = function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const message = document.getElementById('edit-consultation-message');
                        message.textContent = '';
                        message.className = 'text-center text-sm font-semibold';
                        const btn = form.querySelector('button[type="submit"]');
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
                        const data = {
                            id: c.id,
                            type: form.type.value,
                            motif: form.motif.value,
                            observations: form.observations.value
                        };
                        fetch('update_consultation.php', {
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
                                    closeEditConsultationModal();
                                    loadConsultationsData();
                                }, 1200);
                            } else {
                                message.textContent = res.message || 'Erreur lors de la modification.';
                                message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                            }
                        })
                        .catch(() => {
                            message.textContent = 'Erreur lors de l\'envoi du formulaire.';
                            message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-check mr-2"></i> Enregistrer';
                        });
                    };
                });
        }
        
        function createPrescription(consultationId) {
            console.log('💊 Ouverture du modal d\'ordonnance pour consultation:', consultationId);
            
            // Créer le modal d'ordonnance
            const modalHtml = `
                <div id="modal-prescription-form" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 p-4">
                    <div class="bg-white rounded-2xl shadow-2xl p-4 md:p-8 w-full max-w-4xl relative border border-indigo-100 max-h-[90vh] overflow-y-auto">
                        <button id="btn-close-prescription-form" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
                        <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center gap-2">
                            <i class="fas fa-prescription text-indigo-500"></i> Nouvelle ordonnance
                        </h3>
                        
                        <form id="prescription-form" class="space-y-5">
                            <input type="hidden" id="consultation_id" value="${consultationId}">
                            
                            <div>
                                <label for="prescription_details" class="block text-sm font-semibold text-gray-700 mb-1">Détails de l'ordonnance</label>
                                <textarea id="prescription_details" name="prescription_details" rows="3" 
                                    placeholder="Instructions générales, posologie, durée du traitement..." 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Médicaments <span class="text-red-500">*</span></label>
                                <div id="medications-container" class="space-y-3">
                                    <div class="medication-row grid grid-cols-1 gap-3 p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Médicament</label>
                                            <select class="medication-select w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" required>
                                                <option value="">Sélectionner</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantité</label>
                                            <input type="text" class="medication-quantity w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" 
                                                placeholder="Ex: 30 comprimés" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Instructions</label>
                                            <input type="text" class="medication-instructions w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" 
                                                placeholder="Ex: 1 comprimé matin et soir">
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" class="remove-medication bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-medication" class="mt-3 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    <i class="fas fa-plus mr-2"></i> Ajouter un médicament
                                </button>
                            </div>
                            
                            <div id="prescription-message" class="text-center text-sm font-semibold"></div>
                            
                            <div class="flex flex-col sm:flex-row justify-end gap-2">
                                <button type="button" id="btn-cancel-prescription-form" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Annuler</button>
                                <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white font-bold px-5 py-2 rounded-lg shadow-lg transition flex items-center gap-2 focus:ring-2 focus:ring-indigo-300">
                                    <i class="fas fa-check mr-2"></i> Créer l'ordonnance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            // Ajouter le modal au DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            console.log('✅ Modal ajouté au DOM');
            
            // Charger les médicaments
            loadMedications();
            
            // Gestion des événements
            const modal = document.getElementById('modal-prescription-form');
            const btnClose = document.getElementById('btn-close-prescription-form');
            const btnCancel = document.getElementById('btn-cancel-prescription-form');
            const btnAddMedication = document.getElementById('add-medication');
            const form = document.getElementById('prescription-form');
            
            btnClose.onclick = () => { 
                console.log('❌ Fermeture du modal');
                modal.remove(); 
            };
            btnCancel.onclick = () => { 
                console.log('❌ Annulation du modal');
                modal.remove(); 
            };
            
            btnAddMedication.onclick = function() {
                console.log('➕ Ajout d\'une ligne de médicament');
                addMedicationRow();
            };
            
            form.onsubmit = function(e) {
                e.preventDefault();
                console.log('📝 Soumission du formulaire d\'ordonnance');
                submitPrescription();
            };
            
            // Gestion de la suppression des lignes de médicaments
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-medication')) {
                    const container = document.getElementById('medications-container');
                    if (container.children.length > 1) {
                        console.log('🗑️ Suppression d\'une ligne de médicament');
                        e.target.closest('.medication-row').remove();
                    }
                }
            });
            
            console.log('✅ Modal d\'ordonnance configuré avec succès');
        }
        
        function loadMedications() {
            console.log('🔄 Début du chargement des médicaments...');
            
            // Vérifier que les éléments existent
            const selects = document.querySelectorAll('.medication-select');
            console.log('🔍 Nombre de selects trouvés:', selects.length);
            
            if (selects.length === 0) {
                console.warn('⚠️ Aucun select de médicament trouvé dans le DOM');
                return;
            }
            
            fetch('get_medications.php')
                .then(response => {
                    console.log('📡 Réponse fetch reçue, status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📋 Données JSON reçues:', data);
                    
                    if (data.success) {
                        console.log('✅ Succès - Nombre de médicaments:', data.count);
                        console.log('💊 Médicaments:', data.medications);
                        
                        selects.forEach((select, index) => {
                            console.log(`🔄 Mise à jour du select ${index + 1}`);
                            select.innerHTML = '<option value="">Sélectionner un médicament</option>';
                            
                            if (data.medications && Array.isArray(data.medications)) {
                                data.medications.forEach(medication => {
                                    const option = document.createElement('option');
                                    option.value = medication.id;
                                    option.textContent = medication.display_name;
                                    option.title = medication.description || '';
                                    select.appendChild(option);
                                });
                                console.log(`✅ Select ${index + 1} mis à jour avec ${data.medications.length} médicaments`);
                            } else {
                                console.error('❌ Données de médicaments invalides:', data.medications);
                            }
                        });
                        
                        console.log('✅ Tous les selects mis à jour avec succès');
                    } else {
                        console.error('❌ Erreur dans la réponse:', data.message);
                        // Afficher l'erreur dans les selects
                        selects.forEach(select => {
                            select.innerHTML = '<option value="">Erreur de chargement</option>';
                        });
                    }
                })
                .catch(err => {
                    console.error('❌ Erreur lors du chargement des médicaments:', err);
                    console.error('❌ Détails de l\'erreur:', err.message);
                    
                    // Afficher l'erreur dans les selects
                    selects.forEach(select => {
                        select.innerHTML = '<option value="">Erreur de chargement</option>';
                    });
                });
        }
        
        function addMedicationRow() {
            const container = document.getElementById('medications-container');
            const newRow = document.createElement('div');
            newRow.className = 'medication-row grid grid-cols-1 gap-3 p-4 border border-gray-200 rounded-lg';
            newRow.innerHTML = `
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Médicament</label>
                    <select class="medication-select w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" required>
                        <option value="">Sélectionner un médicament</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="text" class="medication-quantity w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" 
                        placeholder="Ex: 30 comprimés" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Instructions</label>
                    <input type="text" class="medication-instructions w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm" 
                        placeholder="Ex: 1 comprimé matin et soir">
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-medication bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Charger les médicaments dans la nouvelle ligne
            const newSelect = newRow.querySelector('.medication-select');
            console.log('🔄 Chargement des médicaments pour la nouvelle ligne...');
            
            fetch('get_medications.php')
                .then(response => {
                    console.log('📡 Réponse fetch pour nouvelle ligne, status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📋 Données JSON pour nouvelle ligne:', data);
                    
                    if (data.success && data.medications && Array.isArray(data.medications)) {
                        newSelect.innerHTML = '<option value="">Sélectionner un médicament</option>';
                        data.medications.forEach(medication => {
                            const option = document.createElement('option');
                            option.value = medication.id;
                            option.textContent = medication.display_name;
                            option.title = medication.description || '';
                            newSelect.appendChild(option);
                        });
                        console.log(`✅ Nouvelle ligne mise à jour avec ${data.medications.length} médicaments`);
                    } else {
                        console.error('❌ Erreur dans la réponse pour nouvelle ligne:', data.message);
                        newSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    }
                })
                .catch(err => {
                    console.error('❌ Erreur lors du chargement des médicaments pour nouvelle ligne:', err);
                    newSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }
        
        function submitPrescription() {
            const form = document.getElementById('prescription-form');
            const message = document.getElementById('prescription-message');
            message.textContent = '';
            message.className = 'text-center text-sm font-semibold';
            
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
            
            // Collecter les médicaments
            const medications = [];
            const medicationRows = document.querySelectorAll('.medication-row');
            medicationRows.forEach(row => {
                const medicationId = row.querySelector('.medication-select').value;
                const quantity = row.querySelector('.medication-quantity').value;
                const instructions = row.querySelector('.medication-instructions').value;
                
                if (medicationId && quantity) {
                    medications.push({
                        medication_id: medicationId,
                        quantite: quantity,
                        instructions: instructions
                    });
                }
            });
            
            if (medications.length === 0) {
                message.textContent = 'Veuillez ajouter au moins un médicament.';
                message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Créer l\'ordonnance';
                return;
            }
            
            const data = {
                consultation_id: document.getElementById('consultation_id').value,
                details: document.getElementById('prescription_details').value,
                medications: medications
            };
            
            console.log('📤 Envoi des données d\'ordonnance:', data);
            
            fetch('create_prescription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                console.log('📥 Réponse reçue:', res);
                if(res.success) {
                    message.textContent = res.message;
                    message.className = 'text-green-600 text-center text-sm font-semibold my-2';
                    
                    setTimeout(() => {
                        document.getElementById('modal-prescription-form').remove();
                        loadConsultationsData(); // Recharger les consultations
                    }, 1500);
                } else {
                    message.textContent = res.message || 'Erreur lors de la création de l\'ordonnance.';
                    message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                }
            })
            .catch(err => {
                console.error('❌ Erreur lors de l\'envoi:', err);
                message.textContent = 'Erreur lors de l\'envoi du formulaire.';
                message.className = 'text-red-600 text-center text-sm font-semibold my-2';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Enregistrer';
            });
        }
        
        function viewPrescription(id) {
            alert('Voir ordonnance ' + id + ' - Fonctionnalité à implémenter');
        }
        
        function printPrescription(id) {
            alert('Imprimer ordonnance ' + id + ' - Fonctionnalité à implémenter');
        }

        // Navigation dynamique docteur
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
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
                        case 'user-injured':
                            loadPatients();
                            break;
                        case 'calendar-alt':
                            loadAppointments();
                            break;
                        case 'file-medical':
                            loadConsultations();
                            break;
                        case 'prescription-bottle-alt':
                            loadPrescriptions();
                            break;
                        case 'chart-bar':
                            loadStatistics();
                            break;
                        case 'user':
                            loadProfile();
                            break;
                    }
                });
            });
            function loadDashboard() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold text-indigo-800 font-poppins mb-1 animate-fadeIn">Tableau de bord</h2>
                        <p class="text-gray-500 text-lg">Aperçu de votre activité médicale.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                        <div class="bg-gradient-to-br from-blue-100 via-white to-blue-50 rounded-2xl shadow-xl p-7 transform transition hover:scale-105 hover:shadow-2xl animate-fadeIn">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 font-semibold">Patients suivis</p>
                                    <h3 id="stat-patients" class="text-4xl font-extrabold text-blue-700 mt-2">...</h3>
                                </div>
                                <div class="p-4 rounded-full bg-blue-200 text-blue-700 shadow-lg">
                                    <i class="fas fa-user-injured text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-100 via-white to-green-50 rounded-2xl shadow-xl p-7 transform transition hover:scale-105 hover:shadow-2xl animate-fadeIn">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 font-semibold">Rendez-vous aujourd'hui</p>
                                    <h3 id="stat-rdv-today" class="text-4xl font-extrabold text-green-700 mt-2">...</h3>
                                </div>
                                <div class="p-4 rounded-full bg-green-200 text-green-700 shadow-lg">
                                    <i class="fas fa-calendar-alt text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-100 via-white to-purple-50 rounded-2xl shadow-xl p-7 transform transition hover:scale-105 hover:shadow-2xl animate-fadeIn">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 font-semibold">Consultations ce mois</p>
                                    <h3 id="stat-consultations" class="text-4xl font-extrabold text-purple-700 mt-2">...</h3>
                                </div>
                                <div class="p-4 rounded-full bg-purple-200 text-purple-700 shadow-lg">
                                    <i class="fas fa-file-medical text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Graphique d'activité (dynamique) -->
                    <div class="bg-white rounded-2xl shadow p-6 animate-fadeIn mb-8">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-chart-bar text-indigo-500 text-2xl mr-3"></i>
                            <span class="text-lg font-bold text-indigo-700">Activité (7 derniers jours)</span>
                        </div>
                        <div id="activity-chart-container" class="flex items-end space-x-2 h-32 mt-4">
                            <div class="flex items-center justify-center">
                                <span class="animate-spin text-indigo-500 text-2xl"><i class="fas fa-spinner"></i></span>
                                <span class="ml-2 text-gray-500">Chargement...</span>
                            </div>
                        </div>
                        <div id="activity-chart-labels" class="flex justify-between text-xs text-gray-500 mt-2">
                            <!-- Les labels seront générés dynamiquement -->
                        </div>
                    </div>
                    <!-- Prochains rendez-vous -->
                    <div class="bg-white rounded-2xl shadow-xl p-8 animate-fadeIn mb-8">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-calendar-day text-green-500 text-2xl mr-3"></i>
                            <span class="text-lg font-bold text-green-700">Prochains rendez-vous du jour</span>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-green-700 uppercase">Heure</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-green-700 uppercase">Patient</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-green-700 uppercase">Motif</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-green-700 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody id="dashboard-rdv-tbody" class="bg-white divide-y divide-gray-100">
                                <tr><td colspan="4" class="text-center text-gray-400 py-4">Chargement...</td></tr>
                            </tbody>
                        </table>
                    </div>
                `;

                // Charger les stats dynamiques
                fetch('get_doctor_dashboard_stats.php')
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.getElementById('stat-patients').textContent = data.patients_count;
                            document.getElementById('stat-rdv-today').textContent = data.rdv_today;
                            document.getElementById('stat-consultations').textContent = data.consultations_month;
                        } else {
                            document.getElementById('stat-patients').textContent = '-';
                            document.getElementById('stat-rdv-today').textContent = '-';
                            document.getElementById('stat-consultations').textContent = '-';
                        }
                    })
                    .catch(() => {
                        document.getElementById('stat-patients').textContent = '-';
                        document.getElementById('stat-rdv-today').textContent = '-';
                        document.getElementById('stat-consultations').textContent = '-';
                    });

                // Charger les prochains rendez-vous du jour dynamiquement
                fetch('get_doctor_appointments.php?statut=confirmé')
                    .then(res => res.json())
                    .then(data => {
                        const tbody = document.getElementById('dashboard-rdv-tbody');
                        tbody.innerHTML = '';
                        if(data.success && data.appointments && data.appointments.length > 0) {
                            // Filtrer pour aujourd'hui côté JS en utilisant date_heure_original
                            const today = new Date();
                            const todayStr = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');
                            const rdvsToday = data.appointments.filter(rdv => {
                                if (!rdv.date_heure_original) return false;
                                return rdv.date_heure_original.startsWith(todayStr);
                            });
                            if(rdvsToday.length > 0) {
                                rdvsToday.forEach(rdv => {
                                    tbody.innerHTML += `
                                        <tr class="hover:bg-green-50 transition">
                                            <td class="px-4 py-2 font-semibold">${rdv.heure || '-'}</td>
                                            <td class="px-4 py-2">${rdv.patient || '-'}</td>
                                            <td class="px-4 py-2">${rdv.motif || '-'}</td>
                                            <td class="px-4 py-2"><span class="inline-block ${rdv.statut_class} text-xs px-2 py-1 rounded-full">${rdv.statut || '-'}</span></td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-400 py-4">Aucun rendez-vous aujourd\'hui.</td></tr>';
                            }
                        } else {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-400 py-4">Aucun rendez-vous trouvé.</td></tr>';
                        }
                    })
                    .catch(() => {
                        const tbody = document.getElementById('dashboard-rdv-tbody');
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-red-400 py-4">Erreur lors du chargement.</td></tr>';
                    });

                // Charger le graphique d'activité des 7 derniers jours
                loadActivityChart();
            }
            
            function loadActivityChart() {
                fetch('get_doctor_activity_7_days.php')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('activity-chart-container');
                        const labelsContainer = document.getElementById('activity-chart-labels');
                        
                        if (data.success) {
                            // Générer les barres du graphique
                            let chartHtml = '';
                            data.heights.forEach((height, index) => {
                                const activity = data.activities[index];
                                const colorClass = height > 80 ? 'bg-indigo-600' : 
                                                 height > 60 ? 'bg-indigo-500' : 
                                                 height > 40 ? 'bg-indigo-400' : 
                                                 height > 20 ? 'bg-indigo-300' : 'bg-indigo-200';
                                
                                chartHtml += `
                                    <div class="w-6 ${colorClass} rounded-t-lg transition-all duration-300 hover:bg-indigo-700 cursor-pointer relative group" 
                                         style="height: ${height}%" 
                                         title="${activity} activité(s) le ${data.days[index]}">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                            ${activity} activité(s)
                                        </div>
                                    </div>
                                `;
                            });
                            
                            container.innerHTML = chartHtml;
                            
                            // Générer les labels des jours
                            let labelsHtml = '';
                            data.days.forEach(day => {
                                labelsHtml += `<span>${day}</span>`;
                            });
                            labelsContainer.innerHTML = labelsHtml;
                            
                        } else {
                            container.innerHTML = `
                                <div class="flex items-center justify-center w-full">
                                    <span class="text-red-400 text-sm">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Erreur de chargement des données d'activité
                                    </span>
                                </div>
                            `;
                            labelsContainer.innerHTML = '';
                        }
                    })
                    .catch(error => {
                        const container = document.getElementById('activity-chart-container');
                        container.innerHTML = `
                            <div class="flex items-center justify-center w-full">
                                <span class="text-red-400 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Erreur de connexion
                                </span>
                            </div>
                        `;
                        document.getElementById('activity-chart-labels').innerHTML = '';
                    });
            }
            function loadPatients() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Mes patients</h2>
                            <p class="text-gray-600">Liste de vos patients suivis (rendez-vous acceptés).</p>
                        </div>
                        <div class="flex gap-2 items-center mt-2 md:mt-0">
                            <input id="patients-search" type="text" placeholder="Rechercher un patient..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition w-56" />
                            <select id="patients-status-filter" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                                <option value="">Tous les statuts</option>
                                <option value="Actif">Actif</option>
                                <option value="Inactif">Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 mb-6 overflow-x-auto">
                        <div id="patients-loading" class="flex flex-col items-center justify-center py-8">
                            <span class="animate-spin text-indigo-500 text-3xl mb-2"><i class="fas fa-spinner"></i></span>
                            <span class="text-gray-400">Chargement des patients...</span>
                        </div>
                        <table id="patients-table" class="min-w-full divide-y divide-gray-200 hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Âge</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Téléphone</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="patients-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                        </table>
                        <div id="patients-error" class="text-center text-red-500 py-4 hidden"></div>
                        <div id="patients-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-user-injured text-4xl mb-4"></i>
                            <p>Aucun patient avec rendez-vous accepté trouvé.</p>
                        </div>
                    </div>
                `;

                // Charger dynamiquement les patients
                function loadDoctorPatients() {
                    const loading = document.getElementById('patients-loading');
                    const table = document.getElementById('patients-table');
                    const tbody = document.getElementById('patients-tbody');
                    const error = document.getElementById('patients-error');
                    const empty = document.getElementById('patients-empty');

                    loading.classList.remove('hidden');
                    table.classList.add('hidden');
                    error.classList.add('hidden');
                    empty.classList.add('hidden');
                    tbody.innerHTML = '';

                    fetch('get_doctor_patients.php')
                        .then(response => response.json())
                        .then(data => {
                            loading.classList.add('hidden');
                            if (data.success) {
                                if (data.patients && data.patients.length > 0) {
                                    table.classList.remove('hidden');
                                    data.patients.forEach(patient => {
                                        // Calcul de l'âge
                                        let age = '';
                                        if (patient.date_naissance) {
                                            const birth = new Date(patient.date_naissance);
                                            const today = new Date();
                                            age = today.getFullYear() - birth.getFullYear();
                                            const m = today.getMonth() - birth.getMonth();
                                            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                                                age--;
                                            }
                                        }
                                        // Avatar (initiales)
                                        const initials = (patient.prenom?.charAt(0) || '') + (patient.nom?.charAt(0) || '');
                                        tbody.innerHTML += `
                                            <tr class="hover:bg-indigo-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap flex items-center gap-3">
                                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold text-lg border border-indigo-200 shadow">${initials}</span>
                                                    <span class="font-semibold">${patient.nom} ${patient.prenom}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">${age || '-'}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${patient.telephone || '-'}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${patient.statut === 'Inactif' ? 'bg-gray-200 text-gray-600' : 'bg-green-100 text-green-800'}">
                                                        ${patient.statut || 'Actif'}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-4 py-2 rounded-lg font-semibold flex items-center gap-2 shadow transition" title="Voir le dossier" onclick="window.location.href='dossier_medical.php?patient_id=${patient.patient_id}'">
                                                        <i class="fas fa-folder-open"></i> Dossier
                                                    </button>
                                                </td>
                                            </tr>
                                        `;
                                    });
                                } else {
                                    empty.classList.remove('hidden');
                                }
                            } else {
                                error.textContent = data.message || 'Erreur lors du chargement des patients.';
                                error.classList.remove('hidden');
                            }
                        })
                        .catch(err => {
                            loading.classList.add('hidden');
                            error.textContent = 'Erreur lors du chargement des patients.';
                            error.classList.remove('hidden');
                        });
                }
                loadDoctorPatients();

                // Recherche et filtre
                setTimeout(() => {
                    const searchInput = document.getElementById('patients-search');
                    const statusFilter = document.getElementById('patients-status-filter');
                    searchInput?.addEventListener('input', filterPatients);
                    statusFilter?.addEventListener('change', filterPatients);
                    function filterPatients() {
                        const search = searchInput.value.toLowerCase();
                        const status = statusFilter.value;
                        document.querySelectorAll('#patients-tbody tr').forEach(tr => {
                            const nom = tr.querySelector('td:nth-child(1) .font-semibold')?.textContent.toLowerCase() || '';
                            const statut = tr.querySelector('td:nth-child(4) span')?.textContent || '';
                            const show = (!search || nom.includes(search)) && (!status || statut === status);
                            tr.style.display = show ? '' : 'none';
                        });
                    }
                }, 500);
            }
            function loadAppointments() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Mes rendez-vous</h2>
                            <p class="text-gray-600">Gérez vos rendez-vous à venir.</p>
                        </div>
                        <div class="flex gap-2 items-center mt-2 md:mt-0">
                            <input id="rdv-search" type="text" placeholder="Rechercher un patient ou motif..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition w-56" />
                            <select id="rdv-status-filter" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                                <option value="">Tous les statuts</option>
                                <option value="En attente">En attente</option>
                                <option value="Confirmé">Confirmé</option>
                                <option value="Terminé">Terminé</option>
                                <option value="Annulé">Annulé</option>
                            </select>
                        </div>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center mt-4 md:mt-0">
                            <i class="fas fa-calendar-plus mr-2"></i> Nouveau rendez-vous
                        </button>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 mb-6 overflow-x-auto">
                        <div id="rdv-loading" class="flex flex-col items-center justify-center py-8">
                            <span class="animate-spin text-indigo-500 text-3xl mb-2"><i class="fas fa-spinner"></i></span>
                            <span class="text-gray-400">Chargement des rendez-vous...</span>
                        </div>
                        <table id="rdv-table" class="min-w-full divide-y divide-gray-200 hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Téléphone</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rdv-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                        </table>
                        <div id="rdv-error" class="text-center text-red-500 py-4 hidden"></div>
                        <div id="rdv-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-calendar-times text-4xl mb-4"></i>
                            <p>Aucun rendez-vous trouvé.</p>
                        </div>
                    </div>
                `;
                // Fonction pour charger les rendez-vous
                function loadDoctorAppointmentsData() {
                    const loading = document.getElementById('rdv-loading');
                    const table = document.getElementById('rdv-table');
                    const tbody = document.getElementById('rdv-tbody');
                    const error = document.getElementById('rdv-error');
                    const empty = document.getElementById('rdv-empty');
                    loading.classList.remove('hidden');
                    table.classList.add('hidden');
                    error.classList.add('hidden');
                    empty.classList.add('hidden');
                    tbody.innerHTML = '';
                    fetch('get_doctor_appointments.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            loading.classList.add('hidden');
                            if (data.success) {
                                if (data.appointments && data.appointments.length > 0) {
                                    table.classList.remove('hidden');
                                    data.appointments.forEach((rdv, index) => {
                                        // Déterminer les boutons d'action selon le statut
                                        let actionButtons = '';
                                        if (rdv.statut === 'En attente') {
                                            actionButtons = `
                                                <button onclick="updateAppointmentStatus(${rdv.id}, 'confirmé')" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg font-semibold flex items-center gap-2 shadow transition mr-2" title="Valider">
                                                    <i class="fas fa-check"></i> Valider
                                                </button>
                                                <button onclick="updateAppointmentStatus(${rdv.id}, 'annulé')" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg font-semibold flex items-center gap-2 shadow transition" title="Refuser">
                                                    <i class="fas fa-times"></i> Refuser
                                                </button>
                                            `;
                                        } else if (rdv.statut === 'Confirmé') {
                                            actionButtons = `
                                                <button onclick="updateAppointmentStatus(${rdv.id}, 'terminé')" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg font-semibold flex items-center gap-2 shadow transition mr-2" title="Terminer">
                                                    <i class="fas fa-check-double"></i> Terminer
                                                </button>
                                                <button onclick="updateAppointmentStatus(${rdv.id}, 'annulé')" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg font-semibold flex items-center gap-2 shadow transition" title="Annuler">
                                                    <i class="fas fa-times"></i> Annuler
                                                </button>
                                            `;
                                        } else {
                                            actionButtons = `
                                                <span class="text-gray-400">Aucune action</span>
                                            `;
                                        }
                                        // Badge statut
                                        let badgeClass = '';
                                        let badgeIcon = '';
                                        switch(rdv.statut) {
                                            case 'En attente':
                                                badgeClass = 'bg-yellow-100 text-yellow-800';
                                                badgeIcon = '<i class="fas fa-hourglass-half mr-1"></i>';
                                                break;
                                            case 'Confirmé':
                                                badgeClass = 'bg-green-100 text-green-800';
                                                badgeIcon = '<i class="fas fa-check-circle mr-1"></i>';
                                                break;
                                            case 'Terminé':
                                                badgeClass = 'bg-blue-100 text-blue-800';
                                                badgeIcon = '<i class="fas fa-check-double mr-1"></i>';
                                                break;
                                            case 'Annulé':
                                                badgeClass = 'bg-red-100 text-red-800';
                                                badgeIcon = '<i class="fas fa-times-circle mr-1"></i>';
                                                break;
                                            default:
                                                badgeClass = 'bg-gray-100 text-gray-800';
                                                badgeIcon = '';
                                        }
                                        tbody.innerHTML += `
                                            <tr class="hover:bg-indigo-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap font-semibold">${rdv.patient}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${rdv.patient_telephone || '-'}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${rdv.date}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${rdv.heure}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${rdv.motif || '-'}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full ${badgeClass}">
                                                        ${badgeIcon}${rdv.statut}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex flex-wrap gap-2 justify-end">
                                                    ${actionButtons}
                                                </td>
                                            </tr>
                                        `;
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
                            error.textContent = 'Erreur lors du chargement des rendez-vous.';
                            error.classList.remove('hidden');
                        });
                }
                // Charger les rendez-vous au démarrage
                loadDoctorAppointmentsData();
                // Recherche et filtre
                setTimeout(() => {
                    const searchInput = document.getElementById('rdv-search');
                    const statusFilter = document.getElementById('rdv-status-filter');
                    searchInput?.addEventListener('input', filterRdv);
                    statusFilter?.addEventListener('change', filterRdv);
                    function filterRdv() {
                        const search = searchInput.value.toLowerCase();
                        const status = statusFilter.value;
                        document.querySelectorAll('#rdv-tbody tr').forEach(tr => {
                            const nom = tr.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                            const motif = tr.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                            const statut = tr.querySelector('td:nth-child(6) span')?.textContent.trim() || '';
                            const show = (!search || nom.includes(search) || motif.includes(search)) && (!status || statut === status);
                            tr.style.display = show ? '' : 'none';
                        });
                    }
                }, 500);
                // Fonction globale pour mettre à jour le statut
                window.updateAppointmentStatus = function(appointmentId, newStatus) {
                    fetch('update_appointment_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            appointment_id: appointmentId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadDoctorAppointmentsData();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(err => {
                        alert('Erreur lors de la mise à jour du statut');
                    });
                };
            }
            function loadConsultations() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Consultations</h2>
                            <p class="text-gray-600">Historique de vos consultations et création de nouvelles consultations.</p>
                        </div>
                        <div class="flex gap-2 items-center mt-2 md:mt-0">
                            <input id="consultation-search" type="text" placeholder="Rechercher un patient, motif..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition w-56" />
                            <select id="consultation-type-filter" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                                <option value="">Tous les types</option>
                                <option value="Consultation générale">Consultation générale</option>
                                <option value="Consultation spécialisée">Consultation spécialisée</option>
                                <option value="Suivi traitement">Suivi traitement</option>
                                <option value="Bilan de santé">Bilan de santé</option>
                                <option value="Urgence">Urgence</option>
                                <option value="Contrôle">Contrôle</option>
                            </select>
                        </div>
                        <button id="btn-open-consultation-form" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center mt-4 md:mt-0">
                            <i class="fas fa-plus mr-2"></i> Nouvelle consultation
                        </button>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6">
                        <div id="consultation-loading" class="flex flex-col items-center justify-center py-8">
                            <span class="animate-spin text-indigo-500 text-3xl mb-2"><i class="fas fa-spinner"></i></span>
                            <span class="text-gray-400">Chargement des consultations...</span>
                        </div>
                        <div id="consultation-error" class="text-center text-red-500 py-4 hidden"></div>
                        <div id="consultation-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-file-medical text-4xl mb-4"></i>
                            <p>Aucune consultation trouvée.</p>
                            <p class="text-sm">Créez votre première consultation en cliquant sur le bouton ci-dessus.</p>
                        </div>
                        
                        <!-- Version desktop - Tableau -->
                        <div class="hidden md:block overflow-x-auto">
                            <table id="consultation-table" class="min-w-full divide-y divide-gray-200 hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motif</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Observations</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="consultation-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                        
                        <!-- Version mobile - Cards -->
                        <div id="consultation-cards" class="md:hidden space-y-4 hidden">
                            <!-- Les cards seront générées dynamiquement -->
                        </div>
                    </div>
                    <!-- Modal Formulaire de consultation -->
                    <div id="modal-consultation-form" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden p-4">
                        <div class="bg-white rounded-2xl shadow-2xl p-4 md:p-8 w-full max-w-2xl relative border border-indigo-100 max-h-[90vh] overflow-y-auto">
                            <button id="btn-close-consultation-form" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-3xl font-bold transition">&times;</button>
                            <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center gap-2"><i class="fas fa-stethoscope text-indigo-500"></i> Nouvelle consultation</h3>
                            <form id="consultation-form" class="space-y-5">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="appointment_id" class="block text-sm font-semibold text-gray-700 mb-1">Rendez-vous <span class="text-red-500">*</span></label>
                                        <select id="appointment_id" name="appointment_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                            <option value="">Sélectionner un rendez-vous confirmé</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="patient_id" class="block text-sm font-semibold text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                                        <input type="text" id="patient_id" name="patient_id" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="consultation_type" class="block text-sm font-semibold text-gray-700 mb-1">Type de consultation <span class="text-red-500">*</span></label>
                                        <select id="consultation_type" name="consultation_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                            <option value="">Sélectionner le type</option>
                                            <option value="Consultation générale">Consultation générale</option>
                                            <option value="Consultation spécialisée">Consultation spécialisée</option>
                                            <option value="Suivi traitement">Suivi traitement</option>
                                            <option value="Bilan de santé">Bilan de santé</option>
                                            <option value="Urgence">Urgence</option>
                                            <option value="Contrôle">Contrôle</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="consultation_motif" class="block text-sm font-semibold text-gray-700 mb-1">Motif <span class="text-red-500">*</span></label>
                                        <input type="text" id="consultation_motif" name="consultation_motif" placeholder="Ex : Douleur thoracique, suivi..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="consultation_observations" class="block text-sm font-semibold text-gray-700 mb-1">Observations <span class="text-red-500">*</span></label>
                                    <textarea id="consultation_observations" name="consultation_observations" rows="4" placeholder="Détails de la consultation, examens, diagnostic..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required></textarea>
                                </div>
                                <div id="consultation-message" class="text-center text-sm font-semibold"></div>
                                <div class="flex flex-col sm:flex-row justify-end gap-2">
                                    <button type="button" id="btn-cancel-consultation-form" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Annuler</button>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2 rounded-lg shadow-lg transition flex items-center gap-2 focus:ring-2 focus:ring-indigo-300">
                                        <i class="fas fa-check mr-2"></i> Créer la consultation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                // Fonction de filtrage accessible dans tout le scope
                function filterConsultations() {
                    const searchInput = document.getElementById('consultation-search');
                    const typeFilter = document.getElementById('consultation-type-filter');
                    const search = searchInput?.value.toLowerCase() || '';
                    const type = typeFilter?.value || '';
                    document.querySelectorAll('#consultation-tbody tr').forEach(tr => {
                        const nom = tr.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                        const motif = tr.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                        const typeVal = tr.querySelector('td:nth-child(3)')?.textContent || '';
                        const show = (!search || nom.includes(search) || motif.includes(search)) && (!type || typeVal === type);
                        tr.style.display = show ? '' : 'none';
                    });
                }
                // Charger les consultations
                function loadConsultationsDataWithFilter() {
                    loadConsultationsData();
                    setTimeout(filterConsultations, 100); // Appel du filtre après le remplissage du tableau
                }
                loadConsultationsDataWithFilter();
                // Charger les rendez-vous confirmés pour le formulaire
                loadConfirmedAppointments();
                // Gestion du modal
                setTimeout(() => {
                    const btnOpen = document.getElementById('btn-open-consultation-form');
                    const btnClose = document.getElementById('btn-close-consultation-form');
                    const btnCancel = document.getElementById('btn-cancel-consultation-form');
                    const modal = document.getElementById('modal-consultation-form');
                    if(btnOpen && modal) {
                        btnOpen.onclick = () => { modal.classList.remove('hidden'); };
                    }
                    if(btnClose && modal) {
                        btnClose.onclick = () => { modal.classList.add('hidden'); };
                    }
                    if(btnCancel && modal) {
                        btnCancel.onclick = () => { modal.classList.add('hidden'); };
                    }
                    // Gestion du formulaire
                    const form = document.getElementById('consultation-form');
                    if(form) {
                        form.onsubmit = function(e) {
                            e.preventDefault();
                            createConsultation();
                        };
                    }
                    // Gestion du changement de rendez-vous
                    const appointmentSelect = document.getElementById('appointment_id');
                    if(appointmentSelect) {
                        appointmentSelect.onchange = function() {
                            updatePatientInfo();
                        };
                    }
                }, 100);
                // Recherche et filtre : écouteurs
                setTimeout(() => {
                    const searchInput = document.getElementById('consultation-search');
                    const typeFilter = document.getElementById('consultation-type-filter');
                    searchInput?.addEventListener('input', filterConsultations);
                    typeFilter?.addEventListener('change', filterConsultations);
                }, 500);
            }
            
            function loadConsultationsData() {
                const loading = document.getElementById('consultation-loading');
                const table = document.getElementById('consultation-table');
                const tbody = document.getElementById('consultation-tbody');
                const cards = document.getElementById('consultation-cards');
                const error = document.getElementById('consultation-error');
                const empty = document.getElementById('consultation-empty');
                
                console.log('🔄 Chargement des consultations...');
                
                loading.classList.remove('hidden');
                table.classList.add('hidden');
                cards.classList.add('hidden');
                error.classList.add('hidden');
                empty.classList.add('hidden');
                tbody.innerHTML = '';
                cards.innerHTML = '';
                
                fetch('get_consultations.php')
                    .then(response => {
                        console.log('📡 Réponse reçue:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('📋 Données reçues:', data);
                        loading.classList.add('hidden');
                        
                        if (data.success) {
                            console.log('✅ Succès - Nombre de consultations:', data.count);
                            
                            if (data.consultations && data.consultations.length > 0) {
                                console.log('✅ ' + data.consultations.length + ' consultations trouvées');
                                table.classList.remove('hidden');
                                cards.classList.remove('hidden');
                                
                                data.consultations.forEach((consultation, index) => {
                                    console.log('📋 Consultation ' + (index + 1) + ':', consultation);
                                    
                                    // Version desktop - Tableau
                                    const tr = document.createElement('tr');
                                    tr.className = 'hover:bg-gray-50';
                                    tr.innerHTML = `
                                        <td class="px-6 py-4 whitespace-nowrap">${consultation.patient_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${consultation.date_consultation}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${consultation.type}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${consultation.motif}</td>
                                        <td class="px-6 py-4">${consultation.observations ? consultation.observations.substring(0, 50) + (consultation.observations.length > 50 ? '...' : '') : ''}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="viewConsultation(${consultation.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</button>
                                            <button onclick="createPrescription(${consultation.id})" class="text-green-600 hover:text-green-900 mr-3">Ordonnance</button>
                                            <button onclick="editConsultation(${consultation.id})" class="text-gray-600 hover:text-gray-900">Editer</button>
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                    
                                    // Version mobile - Card
                                    const card = document.createElement('div');
                                    card.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
                                    card.innerHTML = `
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">${consultation.patient_name}</h4>
                                                <p class="text-sm text-gray-600">${consultation.date_consultation}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">${consultation.type}</span>
                                        </div>
                                        <div class="space-y-2 mb-4">
                                            <div>
                                                <span class="text-xs font-medium text-gray-500">Motif:</span>
                                                <p class="text-sm text-gray-900">${consultation.motif}</p>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-gray-500">Observations:</span>
                                                <p class="text-sm text-gray-900">${consultation.observations ? consultation.observations.substring(0, 80) + (consultation.observations.length > 80 ? '...' : '') : 'Aucune'}</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button onclick="viewConsultation(${consultation.id})" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full hover:bg-indigo-200 transition">Voir</button>
                                            <button onclick="createPrescription(${consultation.id})" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full hover:bg-green-200 transition">Ordonnance</button>
                                            <button onclick="editConsultation(${consultation.id})" class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full hover:bg-gray-200 transition">Editer</button>
                                        </div>
                                    `;
                                    cards.appendChild(card);
                                });
                            } else {
                                console.log('📭 Aucune consultation trouvée');
                                empty.classList.remove('hidden');
                            }
                        } else {
                            console.error('❌ Erreur dans la réponse:', data);
                            error.textContent = data.message || 'Erreur lors du chargement des consultations.';
                            if (data.debug) {
                                error.textContent += ' (Debug: ' + JSON.stringify(data.debug) + ')';
                            }
                            error.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        console.error('❌ Erreur fetch:', err);
                        loading.classList.add('hidden');
                        error.textContent = 'Erreur lors du chargement des consultations: ' + err.message;
                        error.classList.remove('hidden');
                    });
            }
            
            function loadConfirmedAppointments() {
                fetch('get_doctor_appointments.php?statut=confirmé')
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('appointment_id');
                        if (select && data.success) {
                            select.innerHTML = '<option value="">Sélectionner un rendez-vous confirmé</option>';
                            data.appointments.forEach(appointment => {
                                const option = document.createElement('option');
                                option.value = appointment.id;
                                option.textContent = `${appointment.patient_name} - ${appointment.date_heure} - ${appointment.motif}`;
                                option.setAttribute('data-patient-id', appointment.patient_id);
                                option.setAttribute('data-patient-name', appointment.patient_name);
                                select.appendChild(option);
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Erreur chargement rendez-vous:', err);
                    });
            }
            
            function updatePatientInfo() {
                const appointmentSelect = document.getElementById('appointment_id');
                const patientIdInput = document.getElementById('patient_id');
                
                if (appointmentSelect.value) {
                    const selectedOption = appointmentSelect.options[appointmentSelect.selectedIndex];
                    const patientId = selectedOption.getAttribute('data-patient-id');
                    const patientName = selectedOption.getAttribute('data-patient-name');
                    patientIdInput.value = `${patientName} (ID: ${patientId})`;
                } else {
                    patientIdInput.value = '';
                }
            }
            
            function createConsultation() {
                const form = document.getElementById('consultation-form');
                const message = document.getElementById('consultation-message');
                message.textContent = '';
                message.className = 'text-center text-sm font-semibold';
                
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
                
                const appointmentSelect = document.getElementById('appointment_id');
                const selectedOption = appointmentSelect.options[appointmentSelect.selectedIndex];
                const patientId = selectedOption.getAttribute('data-patient-id');
                
                const data = {
                    appointment_id: form.appointment_id.value,
                    patient_id: patientId,
                    type: form.consultation_type.value,
                    motif: form.consultation_motif.value,
                    observations: form.consultation_observations.value
                };
                
                fetch('create_consultation.php', {
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
                            document.getElementById('modal-consultation-form').classList.add('hidden');
                            message.textContent = '';
                            form.reset();
                            document.getElementById('patient_id').value = '';
                            loadConsultationsData();
                        }, 1500);
                    } else {
                        message.textContent = res.message || 'Erreur lors de la création de la consultation.';
                        message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                    }
                })
                .catch(err => {
                    message.textContent = 'Erreur lors de l\'envoi du formulaire.';
                    message.className = 'text-red-600 text-center text-sm font-semibold my-2';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Créer la consultation';
                });
            }
            
            function loadPrescriptions() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Ordonnances</h2>
                            <p class="text-gray-600">Liste de vos ordonnances récentes.</p>
                        </div>
                        <div class="flex gap-2 items-center mt-2 md:mt-0">
                            <input id="prescription-search" type="text" placeholder="Rechercher un patient ou médicament..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition w-56" />
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
                        <div id="prescription-loading" class="flex flex-col items-center justify-center py-8">
                            <span class="animate-spin text-indigo-500 text-3xl mb-2"><i class="fas fa-spinner"></i></span>
                            <span class="text-gray-400">Chargement des ordonnances...</span>
                        </div>
                        
                        <!-- Version desktop - Tableau -->
                        <div class="hidden md:block overflow-x-auto">
                            <table id="prescription-table" class="min-w-full divide-y divide-gray-200 hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Consultation</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Médicaments</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="prescription-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                        
                        <!-- Version mobile - Cards -->
                        <div id="prescription-cards" class="md:hidden space-y-4 hidden">
                            <!-- Les cards seront générées dynamiquement -->
                        </div>
                        
                        <div id="prescription-error" class="text-center text-red-500 py-4 hidden"></div>
                        <div id="prescription-empty" class="text-center text-gray-500 py-8 hidden">
                            <i class="fas fa-prescription-bottle-alt text-4xl mb-4"></i>
                            <p>Aucune ordonnance trouvée.</p>
                            <p class="text-sm">Créez des ordonnances depuis les consultations.</p>
                        </div>
                    </div>
                `;
                loadPrescriptionsData();
                // Recherche
                setTimeout(() => {
                    const searchInput = document.getElementById('prescription-search');
                    searchInput?.addEventListener('input', filterPrescriptions);
                    function filterPrescriptions() {
                        const search = searchInput.value.toLowerCase();
                        document.querySelectorAll('#prescription-tbody tr').forEach(tr => {
                            const patient = tr.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                            const meds = tr.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                            const show = (!search || patient.includes(search) || meds.includes(search));
                            tr.style.display = show ? '' : 'none';
                        });
                    }
                }, 500);
            }
            
            function loadPrescriptionsData() {
                const loading = document.getElementById('prescription-loading');
                const table = document.getElementById('prescription-table');
                const tbody = document.getElementById('prescription-tbody');
                const cards = document.getElementById('prescription-cards');
                const error = document.getElementById('prescription-error');
                const empty = document.getElementById('prescription-empty');
                
                console.log('🔄 Chargement des ordonnances...');
                
                loading.classList.remove('hidden');
                table.classList.add('hidden');
                cards.classList.add('hidden');
                error.classList.add('hidden');
                empty.classList.add('hidden');
                tbody.innerHTML = '';
                cards.innerHTML = '';
                
                fetch('get_doctor_prescriptions.php')
                    .then(response => {
                        console.log('📡 Réponse reçue:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('📋 Données reçues:', data);
                        loading.classList.add('hidden');
                        
                        if (data.success) {
                            console.log('✅ Succès - Nombre d\'ordonnances:', data.count);
                            
                            if (data.prescriptions && data.prescriptions.length > 0) {
                                console.log('✅ ' + data.prescriptions.length + ' ordonnances trouvées');
                                table.classList.remove('hidden');
                                cards.classList.remove('hidden');
                                
                                data.prescriptions.forEach((prescription, index) => {
                                    console.log('💊 Ordonnance ' + (index + 1) + ':', prescription);
                                    
                                    // Version desktop - Tableau
                                    const tr = document.createElement('tr');
                                    tr.className = 'hover:bg-gray-50';
                                    tr.innerHTML = `
                                        <td class="px-6 py-4 whitespace-nowrap">${prescription.date_prescription}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${prescription.patient_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="font-medium">${prescription.consultation_type}</div>
                                                <div class="text-gray-500">${prescription.consultation_motif}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                ${prescription.medications.split('; ').map(med => 
                                                    `<div class="mb-1">• ${med}</div>`
                                                ).join('')}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="window.open('download_prescription_pdf.php?id=${prescription.id}', '_blank')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                <i class="fas fa-file-pdf"></i> Télécharger PDF
                                            </button>
                                            <button onclick="window.open('download_prescription_pdf.php?id=${prescription.id}', '_blank')" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-print"></i> Imprimer
                                            </button>
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                    
                                    // Version mobile - Card
                                    const card = document.createElement('div');
                                    card.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
                                    card.innerHTML = `
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">${prescription.patient_name}</h4>
                                                <p class="text-sm text-gray-600">${prescription.date_prescription}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Ordonnance</span>
                                        </div>
                                        <div class="space-y-2 mb-4">
                                            <div>
                                                <span class="text-xs font-medium text-gray-500">Consultation:</span>
                                                <p class="text-sm text-gray-900">${prescription.consultation_type}</p>
                                                <p class="text-xs text-gray-600">${prescription.consultation_motif}</p>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-gray-500">Médicaments:</span>
                                                <div class="text-sm text-gray-900 mt-1">
                                                    ${prescription.medications.split('; ').map(med => 
                                                        `<div class="mb-1">• ${med}</div>`
                                                    ).join('')}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button onclick="window.open('download_prescription_pdf.php?id=${prescription.id}', '_blank')" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full hover:bg-indigo-200 transition">
                                                <i class="fas fa-file-pdf mr-1"></i> PDF
                                            </button>
                                            <button onclick="window.open('download_prescription_pdf.php?id=${prescription.id}', '_blank')" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full hover:bg-green-200 transition">
                                                <i class="fas fa-print mr-1"></i> Imprimer
                                            </button>
                                        </div>
                                    `;
                                    cards.appendChild(card);
                                });
                            } else {
                                console.log('📭 Aucune ordonnance trouvée');
                                empty.classList.remove('hidden');
                            }
                        } else {
                            console.error('❌ Erreur dans la réponse:', data);
                            error.textContent = data.message || 'Erreur lors du chargement des ordonnances.';
                            error.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        console.error('❌ Erreur fetch:', err);
                        loading.classList.add('hidden');
                        error.textContent = 'Erreur lors du chargement des ordonnances: ' + err.message;
                        error.classList.remove('hidden');
                    });
            }

            function loadStatistics() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chart-bar text-indigo-600 text-3xl"></i>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Statistiques</h2>
                                <p class="text-gray-600">Analyse de votre activité médicale</p>
                            </div>
                        </div>
                        <div class="flex gap-2 items-center mt-2 md:mt-0">
                            <select id="stats-period" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                                <option value="7">7 derniers jours</option>
                                <option value="30">30 derniers jours</option>
                                <option value="90">3 derniers mois</option>
                                <option value="180">6 derniers mois</option>
                                <option value="365">Cette année</option>
                            </select>
                            <button id="btn-apply-stats" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg font-semibold flex items-center gap-2 shadow transition">
                                <i class="fas fa-filter"></i> Appliquer
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 rounded-2xl shadow p-6 flex flex-col items-center justify-center">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-file-medical text-blue-500 text-2xl"></i>
                                <span class="text-lg font-bold text-blue-700">Consultations</span>
                            </div>
                            <div id="stat-consultations-card" class="text-4xl font-extrabold text-blue-700">...</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 via-white to-green-100 rounded-2xl shadow p-6 flex flex-col items-center justify-center">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-user-plus text-green-500 text-2xl"></i>
                                <span class="text-lg font-bold text-green-700">Nouveaux patients</span>
                            </div>
                            <div id="stat-new-patients-card" class="text-4xl font-extrabold text-green-700">...</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-50 via-white to-red-100 rounded-2xl shadow p-6 flex flex-col items-center justify-center">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-ban text-red-500 text-2xl"></i>
                                <span class="text-lg font-bold text-red-700">Taux d'annulation</span>
                            </div>
                            <div id="stat-cancel-rate-card" class="text-4xl font-extrabold text-red-700">...</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-chart-column text-indigo-400"></i> Consultations par mois</h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg" id="chart-consultations">
                                <span class="text-gray-400">Graphique à venir</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-chart-pie text-pink-400"></i> Répartition des motifs</h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg" id="chart-motifs">
                                <span class="text-gray-400">Graphique à venir</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex items-center gap-2">
                            <i class="fas fa-table text-indigo-400"></i>
                            <h3 class="text-lg font-medium text-gray-900">Indicateurs clés</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr id="stats-table-header-row">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicateur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">7 derniers jours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">30 derniers jours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cette année</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evolution</th>
                                    </tr>
                                </thead>
                                <tbody id="stats-tbody" class="bg-white divide-y divide-gray-200">
                                    <tr><td colspan="5" class="text-center text-gray-400 py-4"><span class="animate-spin text-indigo-500 text-2xl"><i class="fas fa-spinner"></i></span> Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                // Charger les stats dynamiquement
                function fetchStats(period = 30) {
                    fetch('get_doctor_statistics.php?period='+period)
                        .then(res => res.json())
                        .then(data => {
                            // Cards dynamiques
                            document.getElementById('stat-consultations-card').textContent = data.stats?.consultations?.period ?? '-';
                            document.getElementById('stat-new-patients-card').textContent = data.stats?.new_patients?.period ?? '-';
                            document.getElementById('stat-cancel-rate-card').textContent = (data.stats?.cancel_rate?.period ?? '-') + ' %';
                            // Tableau dynamique
                            const tbody = document.getElementById('stats-tbody');
                            if(data.success) {
                                tbody.innerHTML = '';
                                const indicateurs = [
                                    { label: 'Nombre de consultations', key: 'consultations', isPercent: false },
                                    { label: 'Nouveaux patients', key: 'new_patients', isPercent: false },
                                    { label: 'Taux d\'annulation', key: 'cancel_rate', isPercent: true }
                                ];
                                indicateurs.forEach(ind => {
                                    const v7 = data.stats[ind.key]?.d7;
                                    const v30 = data.stats[ind.key]?.d30;
                                    const v365 = data.stats[ind.key]?.d365;
                                    const evol = data.stats[ind.key]?.evolution;
                                    tbody.innerHTML += `
                                        <tr class="hover:bg-indigo-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">${ind.label}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${formatStat(v7, ind)}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${formatStat(v30, ind)}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${formatStat(v365, ind)}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <i class="fas ${evol >= 0 ? 'fa-arrow-up text-green-500' : 'fa-arrow-down text-red-500'} mr-1"></i>
                                                    <span class="text-sm ${evol >= 0 ? 'text-green-500' : 'text-red-500'}">${Math.abs(evol || 0)}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-400 py-4">Erreur lors du chargement des statistiques.</td></tr>`;
                            }
                        })
                        .catch(() => {
                            const tbody = document.getElementById('stats-tbody');
                            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-400 py-4">Erreur lors du chargement des statistiques.</td></tr>`;
                        });
                }
                function formatStat(val, ind) {
                    if(val === undefined || val === null) return '-';
                    if(ind.isPercent) return val + ' %';
                    if(ind.isEuro) return val + ' €';
                    return val;
                }
                // Initial fetch
                fetchStats();
                // Gestion du filtre période
                setTimeout(() => {
                    const btn = document.getElementById('btn-apply-stats');
                    if(btn) {
                        btn.onclick = () => {
                            const period = document.getElementById('stats-period').value;
                            fetchStats(period);
                        };
                    }
                }, 100);
                // Charger et afficher le graphique des consultations par mois
                fetch('get_doctor_consultations_per_month.php')
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            const ctxId = 'chart-consultations-canvas';
                            let chartDiv = document.getElementById('chart-consultations');
                            chartDiv.innerHTML = `<canvas id="${ctxId}" height="200"></canvas>`;
                            const ctx = document.getElementById(ctxId).getContext('2d');
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.data.map(d => d.month),
                                    datasets: [{
                                        label: 'Consultations',
                                        data: data.data.map(d => d.count),
                                        backgroundColor: 'rgba(99, 102, 241, 0.7)'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { display: false }
                                    }
                                }
                            });
                        } else {
                            document.getElementById('chart-consultations').innerHTML = '<span class="text-red-400">Erreur de chargement</span>';
                        }
                    })
                    .catch(() => {
                        document.getElementById('chart-consultations').innerHTML = '<span class="text-red-400">Erreur de chargement</span>';
                    });
                // Charger et afficher le graphique des motifs
                fetch('get_doctor_motifs_distribution.php')
                    .then(res => res.json())
                    .then(data => {
                        if(data.success && data.data.length > 0) {
                            const ctxId = 'chart-motifs-canvas';
                            let chartDiv = document.getElementById('chart-motifs');
                            chartDiv.innerHTML = `<canvas id="${ctxId}" height="200"></canvas>`;
                            const ctx = document.getElementById(ctxId).getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: data.data.map(d => d.motif),
                                    datasets: [{
                                        label: 'Motifs',
                                        data: data.data.map(d => d.count),
                                        backgroundColor: [
                                            '#6366f1','#818cf8','#a5b4fc','#fbbf24','#f87171','#34d399','#60a5fa','#f472b6','#facc15','#38bdf8','#f472b6','#a3e635'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { position: 'bottom' }
                                    }
                                }
                            });
                        } else {
                            document.getElementById('chart-motifs').innerHTML = '<span class="text-gray-400">Aucune donnée</span>';
                        }
                    })
                    .catch(() => {
                        document.getElementById('chart-motifs').innerHTML = '<span class="text-red-400">Erreur de chargement</span>';
                    });
            }
        });

            function loadProfile() {
                document.getElementById('page-content').innerHTML = `
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Mon profil</h2>
                        <p class="text-gray-600">Gérez vos informations personnelles et professionnelles.</p>
                    </div>
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white rounded-xl shadow-lg p-8 animate-fadeIn">
                            <div class="flex items-center mb-6">
                                <i class="fas fa-user text-indigo-500 text-2xl mr-3"></i>
                                <h3 class="text-xl font-bold text-indigo-800">Informations personnelles</h3>
                            </div>
                            
                            <form id="profile-form" class="space-y-6">
                                <!-- Section Photo -->
                                <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-lg">
                                    <div class="relative">
                                        <div id="profile-photo-preview" class="w-24 h-24 rounded-full border-4 border-indigo-400 shadow-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl">
                                            <!-- Les initiales seront affichées ici -->
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-1">
                                            <i class="fas fa-camera text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                                        <input type="file" id="profile-photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG ou GIF. Taille max: 2MB</p>
                                    </div>
                                </div>
                                
                                <!-- Informations de base -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                                        <input type="text" id="profile-nom" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                                        <input type="text" id="profile-prenom" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" id="profile-email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                                </div>
                                

                                
                                <!-- Section Mot de passe -->
                                <div class="border-t border-gray-200 pt-6">
                                    <div class="flex items-center mb-4">
                                        <i class="fas fa-lock text-indigo-500 text-xl mr-3"></i>
                                        <h4 class="text-lg font-semibold text-gray-800">Sécurité</h4>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel <span class="text-red-500">*</span></label>
                                            <input type="password" id="profile-current-password" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" placeholder="Entrez votre mot de passe actuel">
                                            <p class="text-xs text-gray-500 mt-1">Requis pour changer le mot de passe</p>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                                                <input type="password" id="profile-password" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" placeholder="Minimum 6 caractères">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                                                <input type="password" id="profile-password-confirm" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" placeholder="Confirmer le nouveau mot de passe">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Messages -->
                                <div id="profile-message" class="text-center text-sm font-semibold"></div>
                                
                                <!-- Boutons -->
                                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                                    <button type="button" id="btn-cancel-profile" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition">
                                        Annuler
                                    </button>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-lg shadow-lg transition flex items-center gap-2 focus:ring-2 focus:ring-indigo-300">
                                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
                
                // Charger les infos du profil
                loadProfileData();
                
                // Gestion des événements
                setupProfileEvents();
            }
            
            function loadProfileData() {
                console.log('🔄 Chargement des données du profil...');
                
                fetch('get_doctor_profile.php')
                    .then(response => {
                        console.log('📡 Réponse reçue:', response.status, response.statusText);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('📋 Données reçues:', data);
                        
                        if(data.success) {
                            console.log('✅ Succès - Chargement du profil');
                            
                            document.getElementById('profile-nom').value = data.nom || '';
                            document.getElementById('profile-prenom').value = data.prenom || '';
                            document.getElementById('profile-email').value = data.email || '';
                            
                            // Afficher les initiales ou la photo
                            const photoPreview = document.getElementById('profile-photo-preview');
                            if (data.photo_url && data.photo_url !== '/medicale/assets/images/default-user.png') {
                                photoPreview.innerHTML = `<img src="${data.photo_url}" class="w-full h-full rounded-full object-cover" alt="Photo de profil">`;
                            } else {
                                const initials = (data.prenom?.charAt(0) || '') + (data.nom?.charAt(0) || '');
                                photoPreview.textContent = initials.toUpperCase();
                            }
                            
                            window.userPhotoUrl = data.photo_url || '/medicale/assets/images/default-user.png';
                        } else {
                            console.error('❌ Erreur dans la réponse:', data.message);
                            showProfileError('Erreur lors du chargement du profil: ' + (data.message || 'Erreur inconnue'));
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur fetch:', error);
                        console.error('❌ Détails de l\'erreur:', error.message);
                        
                        // Afficher un message d'erreur plus détaillé
                        let errorMessage = 'Erreur de connexion lors du chargement du profil';
                        
                        if (error.message.includes('HTTP 404')) {
                            errorMessage = 'Fichier get_doctor_profile.php introuvable. Vérifiez que le fichier existe.';
                        } else if (error.message.includes('HTTP 500')) {
                            errorMessage = 'Erreur serveur. Vérifiez les logs du serveur.';
                        } else if (error.message.includes('Failed to fetch')) {
                            errorMessage = 'Impossible de se connecter au serveur. Vérifiez votre connexion internet.';
                        }
                        
                        showProfileError(errorMessage);
                    });
            }
            
            function setupProfileEvents() {
                // Preview photo
                const photoInput = document.getElementById('profile-photo');
                if (photoInput) {
                    photoInput.onchange = function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Vérifier la taille du fichier (2MB max)
                            if (file.size > 2 * 1024 * 1024) {
                                showProfileError('Le fichier est trop volumineux. Taille maximum: 2MB');
                                return;
                            }
                            
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                const photoPreview = document.getElementById('profile-photo-preview');
                                photoPreview.innerHTML = `<img src="${ev.target.result}" class="w-full h-full rounded-full object-cover" alt="Photo de profil">`;
                            };
                            reader.readAsDataURL(file);
                        }
                    };
                }
                
                // Soumission du formulaire
                const form = document.getElementById('profile-form');
                if (form) {
                    form.onsubmit = function(e) {
                        e.preventDefault();
                        submitProfileForm();
                    };
                }
                
                // Bouton annuler
                const btnCancel = document.getElementById('btn-cancel-profile');
                if (btnCancel) {
                    btnCancel.onclick = function() {
                        loadProfileData(); // Recharger les données originales
                        clearProfileMessage();
                        clearPasswordFields(); // Effacer les champs de mot de passe
                    };
                }
                

            }
            
            function submitProfileForm() {
                const form = document.getElementById('profile-form');
                const message = document.getElementById('profile-message');
                const btn = form.querySelector('button[type="submit"]');
                
                // Validation des mots de passe
                const currentPassword = document.getElementById('profile-current-password').value;
                const password = document.getElementById('profile-password').value;
                const passwordConfirm = document.getElementById('profile-password-confirm').value;
                
                // Si un nouveau mot de passe est saisi, vérifier l'ancien
                if (password || passwordConfirm) {
                    if (!currentPassword) {
                        showProfileError('Veuillez entrer votre mot de passe actuel pour le changer');
                        return;
                    }
                    
                    if (!password) {
                        showProfileError('Veuillez entrer le nouveau mot de passe');
                        return;
                    }
                    
                    if (password !== passwordConfirm) {
                        showProfileError('Les nouveaux mots de passe ne correspondent pas');
                        return;
                    }
                    
                    if (password.length < 6) {
                        showProfileError('Le nouveau mot de passe doit contenir au moins 6 caractères');
                        return;
                    }
                }
                
                clearProfileMessage();
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
                
                const formData = new FormData();
                formData.append('nom', document.getElementById('profile-nom').value);
                formData.append('prenom', document.getElementById('profile-prenom').value);
                formData.append('email', document.getElementById('profile-email').value);
                
                if (password) {
                    formData.append('current_password', currentPassword);
                    formData.append('password', password);
                }
                
                if (document.getElementById('profile-photo').files[0]) {
                    formData.append('photo', document.getElementById('profile-photo').files[0]);
                }
                
                fetch('update_doctor_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showProfileSuccess(res.message);
                        if (res.photo_url) {
                            window.userPhotoUrl = res.photo_url;
                        }
                        // Mettre à jour les initiales dans la sidebar
                        updateSidebarInitials();
                        // Effacer les champs de mot de passe après succès
                        clearPasswordFields();
                    } else {
                        showProfileError(res.message || 'Erreur lors de la modification du profil');
                    }
                })
                .catch(error => {
                    console.error('Erreur soumission profil:', error);
                    showProfileError('Erreur lors de l\'envoi du formulaire');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-2"></i> Enregistrer les modifications';
                });
            }
            
            function showProfileSuccess(message) {
                const messageEl = document.getElementById('profile-message');
                messageEl.textContent = message;
                messageEl.className = 'text-green-600 text-center text-sm font-semibold my-2 bg-green-50 p-3 rounded-lg';
            }
            
            function showProfileError(message) {
                const messageEl = document.getElementById('profile-message');
                messageEl.textContent = message;
                messageEl.className = 'text-red-600 text-center text-sm font-semibold my-2 bg-red-50 p-3 rounded-lg';
            }
            
            function clearProfileMessage() {
                const messageEl = document.getElementById('profile-message');
                messageEl.textContent = '';
                messageEl.className = 'text-center text-sm font-semibold';
            }
            
            function updateSidebarInitials() {
                const nom = document.getElementById('profile-nom').value;
                const prenom = document.getElementById('profile-prenom').value;
                const initials = (prenom?.charAt(0) || '') + (nom?.charAt(0) || '');
                
                // Mettre à jour les initiales dans la sidebar et le header
                const sidebarInitials = document.querySelector('.sidebar-profile-initials');
                const headerInitials = document.querySelector('.header-profile-initials');
                
                if (sidebarInitials) {
                    sidebarInitials.textContent = initials.toUpperCase();
                }
                if (headerInitials) {
                    headerInitials.textContent = initials.toUpperCase();
                }
            }
            
            function clearPasswordFields() {
                document.getElementById('profile-current-password').value = '';
                document.getElementById('profile-password').value = '';
                document.getElementById('profile-password-confirm').value = '';
            }
            

    </script>
</body>
</html> 