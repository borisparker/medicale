<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinique Médicale - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-image {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 600px;
        }
        .video-background {
            position: relative;
            height: 500px;
            overflow: hidden;
        }
        .video-background video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: 0;
        }
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .video-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        /* Nouvelles couleurs */
        .bg-primary {
            background-color: #1a365d;
        }
        .bg-secondary {
            background-color: #2c5282;
        }
        .text-primary {
            color: #1a365d;
        }
        .text-secondary {
            color: #2c5282;
        }
        .border-primary {
            border-color: #1a365d;
        }
        .hover\:bg-primary:hover {
            background-color: #1a365d;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php
    session_start();
    require_once 'config/database.php';
    require_once 'includes/header.php';

    if (!function_exists('menuItem')) {
        function menuItem($href, $text, $icon) {
            global $current_page;
            $active = $current_page === $href ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
            return <<<HTML
            <a href="{$href}" class="{$active} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {$icon}
                </svg>
                {$text}
            </a>
            HTML;
        }
    }
    ?>

    <main class="container mx-auto px-4 py-12">
        <!-- Hero Section avec Image de Fond -->
        <div class="hero-image relative text-white">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-transparent opacity-75"></div>
            <div class="relative container mx-auto px-4 py-24">
                <div class="max-w-3xl mx-auto text-center" data-aos="fade-up">
                    <h1 class="text-5xl font-bold mb-6">Votre Santé, Notre Priorité</h1>
                    <p class="text-xl mb-8">Une équipe médicale qualifiée à votre service pour des soins de qualité</p>
                    <div class="flex justify-center gap-4">
                        <a href="rendez-vous.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all">Prendre RDV</a>
                        <a href="contact.php" class="border-2 border-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all">Nous contacter</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgences Section avec Animation -->
        <div class="bg-red-600 text-white py-4 animate-pulse">
            <div class="container mx-auto px-4 text-center">
                <p class="text-xl font-semibold">Urgences 24/7 : <span class="text-2xl">01 23 45 67 89</span></p>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="text-4xl font-bold text-blue-600 mb-2">20+</div>
                <p class="text-gray-600">Années d'expérience</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="text-4xl font-bold text-blue-600 mb-2">50+</div>
                <p class="text-gray-600">Spécialistes</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="text-4xl font-bold text-blue-600 mb-2">10k+</div>
                <p class="text-gray-600">Patients satisfaits</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="text-4xl font-bold text-blue-600 mb-2">24/7</div>
                <p class="text-gray-600">Service d'urgence</p>
            </div>
        </div>

        <!-- Section Avantages Clinique -->
        <section class="my-20">
            <h2 class="text-4xl font-bold text-center mb-4 gradient-text">Pourquoi choisir notre clinique ?</h2>
            <p class="text-center text-gray-500 mb-12 text-lg">Découvrez ce qui fait la différence de notre établissement pour votre santé et votre confort.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Carte 1 -->
                <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col items-start hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-blue-100 p-3 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2 text-primary">Disponibilité 24/7</h3>
                    <p class="text-gray-600 mb-4">Notre équipe médicale est disponible à toute heure pour répondre à vos besoins urgents.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Urgences médicales</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Consultations rapides</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Assistance téléphonique</li>
                    </ul>
                </div>
                <!-- Carte 2 -->
                <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col items-start hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-purple-100 p-3 rounded-full mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2 text-primary">Équipe pluridisciplinaire</h3>
                    <p class="text-gray-600 mb-4">Des spécialistes dans chaque domaine pour une prise en charge globale et personnalisée.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Médecins expérimentés</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Soins personnalisés</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Accompagnement humain</li>
                    </ul>
                </div>
                <!-- Carte 3 -->
                <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col items-start hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-green-100 p-3 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 014-4h3m4 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2 text-primary">Technologies avancées</h3>
                    <p class="text-gray-600 mb-4">Des équipements de dernière génération pour des diagnostics précis et rapides.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Imagerie médicale HD</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Plateforme numérique</li>
                        <li class="flex items-center text-green-600"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>Suivi en temps réel</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Services Principaux avec Images -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Rendez-vous" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4">Rendez-vous en ligne</h2>
                    <p class="text-gray-600 mb-4">Réservez votre consultation en quelques clics, 24h/24 et 7j/7</p>
                    <a href="rendez-vous.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">Prendre RDV</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Espace Patient" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4">Espace Patient</h2>
                    <p class="text-gray-600 mb-4">Accédez à votre dossier médical et suivez vos consultations</p>
                    <a href="login.php" class="inline-block bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">Se connecter</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Services" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4">Nos Services</h2>
                    <p class="text-gray-600 mb-4">Découvrez notre gamme complète de services médicaux</p>
                    <a href="services.php" class="inline-block bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600 transition-colors">En savoir plus</a>
                </div>
            </div>
        </div>

        <!-- Section Équipe Médicale -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">Notre Équipe Médicale</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dr. Martin" class="w-48 h-48 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold">Dr. Martin</h3>
                    <p class="text-gray-600">Cardiologue</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1594824476967-48c8b964273f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dr. Sophie" class="w-48 h-48 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold">Dr. Sophie</h3>
                    <p class="text-gray-600">Pédiatre</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dr. Pierre" class="w-48 h-48 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold">Dr. Pierre</h3>
                    <p class="text-gray-600">Dermatologue</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                    <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dr. Marie" class="w-48 h-48 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold">Dr. Marie</h3>
                    <p class="text-gray-600">Gynécologue</p>
                </div>
            </div>
        </div>

        <!-- Section Équipements Modernes -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-center mb-12">Nos Équipements Modernes</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative overflow-hidden rounded-lg">
                    <img src="https://images.unsplash.com/photo-1581093458791-9d15482442f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Équipement médical" class="w-full h-64 object-cover hover-scale">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                        <h3 class="text-white text-xl font-semibold">Technologie de pointe</h3>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-lg">
                    <img src="https://images.unsplash.com/photo-1581093458791-9d15482442f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Salle d'examen" class="w-full h-64 object-cover hover-scale">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                        <h3 class="text-white text-xl font-semibold">Salles d'examen modernes</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Vidéo Présentation -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12 text-primary" data-aos="fade-up">Découvrez Notre Clinique</h2>
            <div class="video-background rounded-lg overflow-hidden shadow-lg" data-aos="zoom-in">
                <video autoplay muted loop playsinline>
                    <source src="https://assets.mixkit.co/videos/preview/mixkit-medical-team-in-a-hospital-4297-large.mp4" type="video/mp4">
                </video>
                <div class="video-overlay"></div>
                <div class="video-content">
                    <h3 class="text-4xl font-bold mb-4">Une Clinique à Votre Service</h3>
                    <p class="text-xl mb-8">Découvrez nos installations modernes et notre équipe médicale dévouée</p>
                    <a href="contact.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all">Nous Contacter</a>
                </div>
            </div>
        </div>

        <!-- Section Spécialités -->
        <div class="bg-gray-50 py-16 mb-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-primary" data-aos="fade-up">Nos Spécialités Médicales</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="100">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Cardiologie" class="w-full h-48 object-cover rounded-lg mb-4">
                        <h3 class="text-xl font-semibold mb-2 text-primary">Cardiologie</h3>
                        <p class="text-gray-600">Diagnostic et traitement des maladies cardiaques avec les dernières technologies.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="200">
                        <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Pédiatrie" class="w-full h-48 object-cover rounded-lg mb-4">
                        <h3 class="text-xl font-semibold mb-2 text-primary">Pédiatrie</h3>
                        <p class="text-gray-600">Soins spécialisés pour les enfants de tous âges dans un environnement adapté.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="300">
                        <img src="https://images.unsplash.com/photo-1581093458791-9d15482442f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dermatologie" class="w-full h-48 object-cover rounded-lg mb-4">
                        <h3 class="text-xl font-semibold mb-2 text-primary">Dermatologie</h3>
                        <p class="text-gray-600">Traitement des affections cutanées avec des équipements de pointe.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galerie Photos -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">Notre Galerie</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="relative group overflow-hidden rounded-lg" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Clinique" class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-lg font-semibold">Notre Établissement</span>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-lg" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Équipement" class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-lg font-semibold">Équipements Modernes</span>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-lg" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Équipe" class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-lg font-semibold">Notre Équipe</span>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-lg" data-aos="fade-up" data-aos-delay="400">
                    <img src="https://images.unsplash.com/photo-1581093458791-9d15482442f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Services" class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-lg font-semibold">Nos Services</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actualités et Événements -->
        <div class="bg-gray-50 py-16 mb-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">Actualités & Événements</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-sm text-blue-600 mb-2">15 Mars 2024</div>
                        <h3 class="text-xl font-semibold mb-2">Nouveau Scanner 3D</h3>
                        <p class="text-gray-600 mb-4">Nous sommes fiers d'annoncer l'arrivée de notre nouveau scanner 3D de dernière génération.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800">Lire la suite →</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-sm text-blue-600 mb-2">10 Mars 2024</div>
                        <h3 class="text-xl font-semibold mb-2">Journée Portes Ouvertes</h3>
                        <p class="text-gray-600 mb-4">Venez découvrir nos installations et rencontrer notre équipe médicale.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800">Lire la suite →</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-sm text-blue-600 mb-2">5 Mars 2024</div>
                        <h3 class="text-xl font-semibold mb-2">Nouveau Service de Télémédecine</h3>
                        <p class="text-gray-600 mb-4">Consultations en ligne disponibles pour plus de confort et de sécurité.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800">Lire la suite →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">Questions Fréquentes</h2>
            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                        <button class="faq-button w-full px-6 py-4 text-left focus:outline-none flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                            <span class="text-lg font-semibold">Comment prendre rendez-vous ?</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 max-h-0 overflow-hidden transition-all duration-300">
                            <p class="text-gray-600">Vous pouvez prendre rendez-vous en ligne via notre plateforme, par téléphone au 01 23 45 67 89, ou directement à notre accueil.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                        <button class="faq-button w-full px-6 py-4 text-left focus:outline-none flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                            <span class="text-lg font-semibold">Quels sont vos horaires d'ouverture ?</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 max-h-0 overflow-hidden transition-all duration-300">
                            <p class="text-gray-600">Nous sommes ouverts du lundi au vendredi de 8h à 20h, et le samedi de 9h à 17h. Notre service d'urgence est disponible 24h/24.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                        <button class="faq-button w-full px-6 py-4 text-left focus:outline-none flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                            <span class="text-lg font-semibold">Quels documents dois-je apporter ?</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 max-h-0 overflow-hidden transition-all duration-300">
                            <p class="text-gray-600">N'oubliez pas d'apporter votre carte vitale, votre pièce d'identité, et tout document médical pertinent (ordonnances, résultats d'analyses, etc.).</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                        <button class="faq-button w-full px-6 py-4 text-left focus:outline-none flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                            <span class="text-lg font-semibold">Comment fonctionne la télémédecine ?</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 max-h-0 overflow-hidden transition-all duration-300">
                            <p class="text-gray-600">Nos consultations en ligne sont disponibles via notre plateforme sécurisée. Vous recevrez un lien par email pour rejoindre la consultation à l'heure prévue.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-primary text-white rounded-lg p-12 text-center">
            <h2 class="text-3xl font-bold mb-4">Besoin d'un rendez-vous ?</h2>
            <p class="text-xl mb-8">Notre équipe est là pour vous accompagner</p>
            <a href="rendez-vous.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">Prendre rendez-vous maintenant</a>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
    <script src="assets/js/main.js"></script>
    <script src="js/faq.js"></script>
</body>
</html> 