<?php
require_once 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinique Médicale - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="responsive.css">
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
        @keyframes glow {
            0%, 100% {
                text-shadow:
                    0 0 12px #fff,
                    0 0 24px #6366f1,
                    0 0 36px #7c3aed,
                    0 1px 0 #fff;
            }
            50% {
                text-shadow:
                    0 0 24px #fff,
                    0 0 48px #6366f1,
                    0 0 72px #7c3aed,
                    0 2px 4px #fff;
            }
        }
        
        /* Effet de parallaxe pour l'arrière-plan */
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* Animation pour les particules */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Effet de glassmorphism */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Gradient animé pour les boutons */
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }
        
        /* Animation pour les blobs */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Effet de shadow 3D */
        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .scroll-to-top:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        
        .scroll-to-top svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }
        
        .scroll-to-top:hover svg {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-primary text-white shadow">
        <div class="container mx-auto flex justify-between items-center py-4 px-4">
            <div class="flex items-center space-x-5">
                <img src="assets/images/logo.jpg" alt="Logo Clinique" class="h-12 w-12 object-contain">
                <span class="border-l border-yellow-300 h-8 mx-2"></span>
                <span class="text-3xl font-extrabold tracking-widest uppercase"
                    style="
                        letter-spacing:0.18em;
                        background: linear-gradient(90deg, #fff 0%, #6366f1 40%, #7c3aed 100%);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                        text-fill-color: transparent;
                        animation: glow 2s infinite alternate;
                        font-family: 'Segoe UI', 'Arial Rounded MT Bold', Arial, sans-serif;
                        padding: 0 0.2em;">
                    VAIDYA MITRA
                </span>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-blue-200 font-medium">Accueil</a>
                <a href="login.php" class="hover:text-blue-200 font-medium">Prendre RDV</a>
                <a href="login.php" class="hover:text-blue-200 font-medium">Espace Patient</a>
                <a href="#services" class="hover:text-blue-200 font-medium">Services</a>
                <a href="#contact" class="hover:text-blue-200 font-medium">Contact</a>
            </nav>
            
            <!-- Mobile Hamburger Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white hover:text-blue-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-blue-900">
            <nav class="flex flex-col py-4">
                <a href="index.php" class="px-4 py-2 text-white hover:bg-blue-800">Accueil</a>
                <a href="login.php" class="px-4 py-2 text-white hover:bg-blue-800">Prendre RDV</a>
                <a href="login.php" class="px-4 py-2 text-white hover:bg-blue-800">Espace Patient</a>
                <a href="#services" class="px-4 py-2 text-white hover:bg-blue-800">Services</a>
                <a href="#contact" class="px-4 py-2 text-white hover:bg-blue-800">Contact</a>
            </nav>
        </div>
    </header>
    <main>
        <!-- Hero Section Moderne -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Arrière-plan avec image et overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                     alt="Clinique médicale" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-800/80 to-purple-900/70"></div>
                <div class="absolute inset-0 bg-black/30"></div>
            </div>
            
            <!-- Particules flottantes -->
            <div class="absolute inset-0 z-10">
                <div class="absolute top-20 left-10 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                <div class="absolute top-40 right-20 w-1 h-1 bg-blue-300 rounded-full animate-bounce"></div>
                <div class="absolute bottom-40 left-20 w-3 h-3 bg-purple-300 rounded-full animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-1 h-1 bg-white rounded-full animate-bounce"></div>
            </div>
            
            <!-- Contenu principal -->
            <div class="relative z-20 container mx-auto px-4 text-center text-white">
                <div class="max-w-5xl mx-auto">
                    <!-- Badge de confiance -->
                    <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-6 py-2 mb-8 border border-white/30" data-aos="fade-down" data-aos-delay="200">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Plus de 20 ans d'excellence médicale</span>
                    </div>
                    
                    <!-- Titre principal -->
                    <h1 class="text-6xl md:text-7xl font-bold mb-6 leading-tight" data-aos="fade-up" data-aos-delay="300">
                        <span class="bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent">
                            Votre Santé,
                        </span>
                        <br>
                        <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Notre Priorité
                        </span>
                    </h1>
                    
                    <!-- Sous-titre -->
                    <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="400">
                        Une équipe médicale qualifiée et dévouée à votre service pour des soins de qualité et un accompagnement personnalisé
                    </p>
                    
                    <!-- Boutons d'action -->
                    <div class="flex flex-col sm:flex-row justify-center gap-4 mb-12" data-aos="fade-up" data-aos-delay="500">
                        <a href="login.php" class="group relative inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <span class="relative z-10 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Prendre Rendez-vous
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        
                        <a href="#contact" class="group relative inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full overflow-hidden transition-all duration-300 hover:bg-white hover:text-blue-600 hover:scale-105">
                            <span class="relative z-10 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Nous Contacter
                            </span>
                        </a>
                    </div>
                    
                    <!-- Statistiques rapides -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="600">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-blue-300 mb-1">20+</div>
                            <div class="text-sm text-gray-300">Années d'expérience</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-purple-300 mb-1">50+</div>
                            <div class="text-sm text-gray-300">Spécialistes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-pink-300 mb-1">10k+</div>
                            <div class="text-sm text-gray-300">Patients satisfaits</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-green-300 mb-1">24/7</div>
                            <div class="text-sm text-gray-300">Service d'urgence</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce" data-aos="fade-up" data-aos-delay="700">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </section>
        
        <!-- Section Urgences Améliorée -->
        <div class="relative bg-gradient-to-r from-red-600 via-red-500 to-red-600 text-white py-6 overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent animate-pulse"></div>
            </div>
            <div class="relative container mx-auto px-4 text-center">
                <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-xl font-bold">URGENCES 24/7</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-2xl md:text-3xl font-bold">+237 678 760 117</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenu principal du site -->
        <div class="container mx-auto px-4 py-12">
                        <!-- Section Statistiques Améliorée -->
            <section class="py-20 mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Nos Chiffres Clés
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Découvrez l'impact de notre engagement envers l'excellence médicale à travers nos réalisations
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Statistique 1 -->
                    <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-blue-200" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-5xl font-bold text-blue-600 mb-2 group-hover:scale-110 transition-transform">20+</div>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Années d'expérience</p>
                            <p class="text-gray-600 text-sm">D'excellence médicale et de soins personnalisés</p>
                        </div>
                    </div>

                    <!-- Statistique 2 -->
                    <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-purple-200" data-aos="fade-up" data-aos-delay="200">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-purple-500 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="text-5xl font-bold text-purple-600 mb-2 group-hover:scale-110 transition-transform">50+</div>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Spécialistes</p>
                            <p class="text-gray-600 text-sm">Médecins qualifiés dans toutes les spécialités</p>
                        </div>
                    </div>

                    <!-- Statistique 3 -->
                    <div class="group relative bg-gradient-to-br from-pink-50 to-pink-100 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-pink-200" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-pink-500 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div class="text-5xl font-bold text-pink-600 mb-2 group-hover:scale-110 transition-transform">10k+</div>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Patients satisfaits</p>
                            <p class="text-gray-600 text-sm">Qui nous font confiance pour leur santé</p>
                        </div>
                    </div>

                    <!-- Statistique 4 -->
                    <div class="group relative bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-green-200" data-aos="fade-up" data-aos-delay="400">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-green-500 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="text-5xl font-bold text-green-600 mb-2 group-hover:scale-110 transition-transform">24/7</div>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Service d'urgence</p>
                            <p class="text-gray-600 text-sm">Disponible pour vos besoins urgents</p>
                        </div>
                    </div>
                </div>
            </section>
        <!-- Section Avantages Clinique Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50"></div>
            <div class="absolute top-0 left-0 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Pourquoi Choisir Notre Clinique ?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Découvrez ce qui fait la différence de notre établissement pour votre santé et votre confort
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <!-- Carte 1 - Disponibilité 24/7 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-blue-600 transition-colors">Disponibilité 24/7</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Notre équipe médicale est disponible à toute heure pour répondre à vos besoins urgents avec professionnalisme et empathie.</p>
                            <ul class="space-y-3">
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Urgences médicales
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Consultations rapides
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Assistance téléphonique
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Carte 2 - Équipe pluridisciplinaire -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-purple-600 transition-colors">Équipe pluridisciplinaire</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Des spécialistes dans chaque domaine pour une prise en charge globale et personnalisée de votre santé.</p>
                            <ul class="space-y-3">
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Médecins expérimentés
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Soins personnalisés
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Accompagnement humain
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Carte 3 - Technologies avancées -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-blue-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-green-600 transition-colors">Technologies avancées</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Des équipements de dernière génération pour des diagnostics précis et rapides avec une précision maximale.</p>
                            <ul class="space-y-3">
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Imagerie médicale HD
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Plateforme numérique
                                </li>
                                <li class="flex items-center text-green-600 group-hover:text-green-700 transition-colors">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Suivi en temps réel
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Services Principaux Améliorée -->
        <section class="py-20 mb-20">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Nos Services Principaux
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Découvrez nos services innovants conçus pour votre confort et votre bien-être
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <!-- Service 1 - Rendez-vous en ligne -->
                <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Rendez-vous" 
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-blue-600 transition-colors">Rendez-vous en ligne</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Réservez votre consultation en quelques clics, 24h/24 et 7j/7. Simple, rapide et sécurisé.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Disponible 24/7</span>
                            </div>
                            <a href="login.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-full hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                                Prendre RDV
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service 2 - Espace Patient -->
                <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Espace Patient" 
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-green-600 transition-colors">Espace Patient</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Accédez à votre dossier médical, suivez vos consultations et gérez vos rendez-vous en toute simplicité.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Sécurisé</span>
                            </div>
                            <a href="login.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-full hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105">
                                Se connecter
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service 3 - Nos Services -->
                <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-left" data-aos-delay="300">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Services" 
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute top-4 right-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-purple-600 transition-colors">Nos Services</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Découvrez notre gamme complète de services médicaux spécialisés pour tous vos besoins de santé.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Complet</span>
                            </div>
                            <a href="#services" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-full hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                En savoir plus
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Équipe Médicale Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"></div>
            <div class="absolute top-10 left-10 w-32 h-32 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-20 right-20 w-24 h-24 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Notre Équipe Médicale
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Une équipe de professionnels expérimentés et dévoués à votre santé
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
                    <!-- Dr. Martin - Cardiologue -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="relative mb-6">
                                <img src="assets/images/bb.jpg" alt="Dr. Martin" class="w-32 h-32 rounded-full mx-auto object-cover group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-blue-600 transition-colors">Dr. Martin</h3>
                            <p class="text-blue-600 font-semibold mb-3">Cardiologue</p>
                            <p class="text-gray-600 text-sm mb-4">Spécialiste en cardiologie avec plus de 15 ans d'expérience</p>
                            <div class="flex justify-center space-x-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Cardiologie</span>
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">15+ ans</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dr. Sophie - Pédiatre -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                        <div class="absolute inset-0 bg-gradient-to-r from-pink-600 to-purple-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="relative mb-6">
                                <img src="assets/images/téléchargement.jfif" alt="Dr. Sophie" class="w-32 h-32 rounded-full mx-auto object-cover group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-pink-600 transition-colors">Dr. Sophie</h3>
                            <p class="text-pink-600 font-semibold mb-3">Pédiatre</p>
                            <p class="text-gray-600 text-sm mb-4">Spécialiste en pédiatrie, passionnée par la santé des enfants</p>
                            <div class="flex justify-center space-x-2">
                                <span class="px-3 py-1 bg-pink-100 text-pink-600 text-xs rounded-full">Pédiatrie</span>
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">12+ ans</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dr. Pierre - Dermatologue -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="relative mb-6">
                                <img src="assets/images/rr.jfif" alt="Dr. Pierre" class="w-32 h-32 rounded-full mx-auto object-cover group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-green-600 transition-colors">Dr. Pierre</h3>
                            <p class="text-green-600 font-semibold mb-3">Dermatologue</p>
                            <p class="text-gray-600 text-sm mb-4">Expert en dermatologie et chirurgie dermatologique</p>
                            <div class="flex justify-center space-x-2">
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">Dermatologie</span>
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">18+ ans</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dr. Marie - Gynécologue -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="400">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="relative mb-6">
                                <img src="assets/images/doctor-3.jpg" alt="Dr. Marie" class="w-32 h-32 rounded-full mx-auto object-cover group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-purple-600 transition-colors">Dr. Marie</h3>
                            <p class="text-purple-600 font-semibold mb-3">Gynécologue</p>
                            <p class="text-gray-600 text-sm mb-4">Spécialiste en gynécologie et obstétrique</p>
                            <div class="flex justify-center space-x-2">
                                <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">Gynécologie</span>
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">20+ ans</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Équipements Modernes Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-blue-50 to-indigo-50"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-green-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-green-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Nos Équipements Modernes
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Des technologies de pointe pour des diagnostics précis et des soins de qualité
                    </p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-7xl mx-auto">
                    <!-- Équipement 1 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-right" data-aos-delay="100">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/medical-equipment.jpg" 
                                 alt="Équipement médical" 
                                 class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute top-6 left-6">
                                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-green-600 transition-colors">Technologie de Pointe</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Nos équipements de dernière génération permettent des diagnostics précis et des traitements efficaces.</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center text-green-600">
                                    <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Imagerie médicale haute résolution
                                </li>
                                <li class="flex items-center text-green-600">
                                    <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Systèmes de monitoring avancés
                                </li>
                                <li class="flex items-center text-green-600">
                                    <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Équipements stériles et sécurisés
                                </li>
                            </ul>
                            <div class="flex items-center justify-between">
                                <span class="px-4 py-2 bg-green-100 text-green-600 text-sm rounded-full font-semibold">Certifié ISO</span>
                                <span class="text-sm text-gray-500">Mise à jour régulière</span>
                            </div>
                        </div>
                    </div>

                    <!-- Équipement 2 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-left" data-aos-delay="200">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/salle.jpg" 
                                 alt="Salle d'examen" 
                                 class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute top-6 left-6">
                                <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-blue-600 transition-colors">Salles d'Examen Modernes</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Des espaces confortables et équipés pour vous offrir les meilleures conditions de soins.</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center text-blue-600">
                                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Environnement stérile et contrôlé
                                </li>
                                <li class="flex items-center text-blue-600">
                                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Éclairage adapté et confortable
                                </li>
                                <li class="flex items-center text-blue-600">
                                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    Système de climatisation médicale
                                </li>
                            </ul>
                            <div class="flex items-center justify-between">
                                <span class="px-4 py-2 bg-blue-100 text-blue-600 text-sm rounded-full font-semibold">Confort Optimal</span>
                                <span class="text-sm text-gray-500">Température contrôlée</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Vidéo Présentation -->
       
        <!-- Section Spécialités Médicales Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50"></div>
            <div class="absolute top-0 left-20 w-40 h-40 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 right-20 w-32 h-32 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 bg-clip-text text-transparent">
                        Nos Spécialités Médicales
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Une gamme complète de spécialités pour répondre à tous vos besoins de santé
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <!-- Cardiologie -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/cardio.jpg" 
                                 alt="Cardiologie" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-red-600/80 via-red-500/40 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-red-600 transition-colors">Cardiologie</h3>
                            <p class="text-gray-600 mb-4 leading-relaxed">Diagnostic et traitement des maladies cardiaques avec les dernières technologies d'imagerie.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-red-100 text-red-600 text-xs rounded-full">ECG</span>
                                    <span class="px-3 py-1 bg-red-100 text-red-600 text-xs rounded-full">Échographie</span>
                                </div>
                                <a href="#" class="text-red-600 hover:text-red-700 font-semibold text-sm">En savoir plus →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Pédiatrie -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/dermatologie.jpg" 
                                 alt="Pédiatrie" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/80 via-blue-500/40 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">Pédiatrie</h3>
                            <p class="text-gray-600 mb-4 leading-relaxed">Soins spécialisés pour les enfants de tous âges dans un environnement adapté et rassurant.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Vaccins</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Suivi</span>
                                </div>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">En savoir plus →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Dermatologie -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/téléchargement.jfif" 
                                 alt="Dermatologie" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-green-600/80 via-green-500/40 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-green-600 transition-colors">Dermatologie</h3>
                            <p class="text-gray-600 mb-4 leading-relaxed">Traitement des affections cutanées avec des équipements de pointe et des techniques modernes.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">Dépistage</span>
                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">Traitement</span>
                                </div>
                                <a href="#" class="text-green-600 hover:text-green-700 font-semibold text-sm">En savoir plus →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Galerie Photos Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50"></div>
            <div class="absolute top-10 right-10 w-32 h-32 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-gray-700 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Notre Galerie
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Découvrez notre établissement et nos équipements en images
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
                    <!-- Image 1 - Établissement -->
                    <div class="group relative overflow-hidden rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                        <img src="assets/images/waiting-room.jpg" 
                             alt="Notre Établissement" 
                             class="w-full h-80 object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="text-center text-white">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Notre Établissement</h3>
                                <p class="text-sm opacity-90">Un environnement moderne et accueillant</p>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs rounded-full">Établissement</span>
                        </div>
                    </div>

                    <!-- Image 2 - Équipements -->
                    <div class="group relative overflow-hidden rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                        <img src="assets/images/salle.jpg" 
                             alt="Équipements Modernes" 
                             class="w-full h-80 object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-600/80 via-blue-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="text-center text-white">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Équipements Modernes</h3>
                                <p class="text-sm opacity-90">Technologies de pointe</p>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-blue-500/80 backdrop-blur-sm text-white text-xs rounded-full">Équipements</span>
                        </div>
                    </div>

                    <!-- Image 3 - Équipe -->
                    <div class="group relative overflow-hidden rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                        <img src="assets/images/medical-team.jpg" 
                             alt="Notre Équipe" 
                             class="w-full h-80 object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-green-600/80 via-green-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="text-center text-white">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Notre Équipe</h3>
                                <p class="text-sm opacity-90">Professionnels expérimentés</p>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-green-500/80 backdrop-blur-sm text-white text-xs rounded-full">Équipe</span>
                        </div>
                    </div>

                    <!-- Image 4 - Services -->
                    <div class="group relative overflow-hidden rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="400">
                        <img src="assets/images/cardio.jpg" 
                             alt="Nos Services" 
                             class="w-full h-80 object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-600/80 via-purple-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="text-center text-white">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Nos Services</h3>
                                <p class="text-sm opacity-90">Soins spécialisés</p>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-purple-500/80 backdrop-blur-sm text-white text-xs rounded-full">Services</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Actualités et Événements Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-orange-50 to-yellow-50"></div>
            <div class="absolute top-0 left-0 w-40 h-40 bg-orange-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 right-0 w-32 h-32 bg-yellow-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-gray-700 via-orange-600 to-yellow-600 bg-clip-text text-transparent">
                        Actualités & Événements
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Restez informés des dernières nouveautés et événements de notre clinique
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <!-- Actualité 1 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 p-8">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-blue-600 font-semibold">15 Mars 2024</div>
                                    <div class="text-xs text-gray-500">Technologie</div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 group-hover:text-blue-600 transition-colors">Nouveau Scanner 3D</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Nous sommes fiers d'annoncer l'arrivée de notre nouveau scanner 3D de dernière génération pour des diagnostics encore plus précis.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Innovation</span>
                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">Diagnostic</span>
                                </div>
                                <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                                    Lire la suite
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actualité 2 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 p-8">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-orange-600 font-semibold">10 septembre 2025</div>
                                    <div class="text-xs text-gray-500">Événement</div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 group-hover:text-orange-600 transition-colors">Journée Portes Ouvertes</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Venez découvrir nos installations modernes et rencontrer notre équipe médicale lors de notre journée portes ouvertes.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-orange-100 text-orange-600 text-xs rounded-full">Événement</span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">Rencontre</span>
                                </div>
                                <a href="#" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                                    Lire la suite
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actualité 3 -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-4 border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-3xl opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="relative z-10 p-8">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm text-green-600 font-semibold">5 Octobre 2025</div>
                                    <div class="text-xs text-gray-500">Service</div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 group-hover:text-green-600 transition-colors">Nouveau Service de Télémédecine</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Consultations en ligne disponibles pour plus de confort et de sécurité. Accédez à nos soins depuis chez vous.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-full">Télémédecine</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">Innovation</span>
                                </div>
                                <a href="#" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                                    Lire la suite
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section FAQ Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50"></div>
            <div class="absolute top-0 right-0 w-48 h-48 bg-yellow-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-orange-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600 bg-clip-text text-transparent">
                        Questions Fréquentes
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Trouvez rapidement les réponses à vos questions les plus courantes
                    </p>
                </div>
                
                <div class="max-w-4xl mx-auto">
                    <div class="space-y-6">
                        <!-- FAQ Item 1 -->
                        <div class="group bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 hover:shadow-3xl transition-all duration-500" data-aos="fade-up" data-aos-delay="100">
                            <button class="faq-button w-full px-8 py-6 text-left focus:outline-none flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">Comment prendre rendez-vous ?</span>
                                </div>
                                <svg class="w-8 h-8 transform transition-transform duration-300 text-orange-500 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="px-8 pb-6 max-h-0 overflow-hidden transition-all duration-500">
                                <div class="pt-4 border-t border-gray-100">
                                    <p class="text-gray-600 leading-relaxed mb-4">Vous pouvez prendre rendez-vous de plusieurs façons :</p>
                                    <ul class="space-y-2 text-gray-600">
                                        <li class="flex items-center">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                            <span>En ligne via notre plateforme sécurisée</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                            <span>Par téléphone au +237 678 760 117</span>
                                        </li>
                                        <li class="flex items-center">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                            <span>Directement à notre accueil</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="group bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 hover:shadow-3xl transition-all duration-500" data-aos="fade-up" data-aos-delay="200">
                            <button class="faq-button w-full px-8 py-6 text-left focus:outline-none flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">Quels sont vos horaires d'ouverture ?</span>
                                </div>
                                <svg class="w-8 h-8 transform transition-transform duration-300 text-orange-500 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="px-8 pb-6 max-h-0 overflow-hidden transition-all duration-500">
                                <div class="pt-4 border-t border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Horaires de consultation :</h4>
                                            <ul class="space-y-1 text-gray-600">
                                                <li>Lundi - Vendredi : 8h - 20h</li>
                                                <li>Samedi : 9h - 17h</li>
                                                <li>Dimanche : Fermé</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Service d'urgence :</h4>
                                            <p class="text-gray-600">Disponible 24h/24 et 7j/7</p>
                                            <p class="text-orange-600 font-semibold mt-2">+237 678 760 117</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="group bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 hover:shadow-3xl transition-all duration-500" data-aos="fade-up" data-aos-delay="300">
                            <button class="faq-button w-full px-8 py-6 text-left focus:outline-none flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">Quels documents dois-je apporter ?</span>
                                </div>
                                <svg class="w-8 h-8 transform transition-transform duration-300 text-orange-500 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="px-8 pb-6 max-h-0 overflow-hidden transition-all duration-500">
                                <div class="pt-4 border-t border-gray-100">
                                    <p class="text-gray-600 leading-relaxed mb-4">Pour une consultation optimale, n'oubliez pas d'apporter :</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Documents obligatoires :</h4>
                                            <ul class="space-y-1 text-gray-600">
                                                <li>• Carte vitale</li>
                                                <li>• Pièce d'identité</li>
                                                <li>• Carte mutuelle (si applicable)</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Documents médicaux :</h4>
                                            <ul class="space-y-1 text-gray-600">
                                                <li>• Ordonnances récentes</li>
                                                <li>• Résultats d'analyses</li>
                                                <li>• Imagerie médicale</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="group bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 hover:shadow-3xl transition-all duration-500" data-aos="fade-up" data-aos-delay="400">
                            <button class="faq-button w-full px-8 py-6 text-left focus:outline-none flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">Comment fonctionne la télémédecine ?</span>
                                </div>
                                <svg class="w-8 h-8 transform transition-transform duration-300 text-orange-500 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="px-8 pb-6 max-h-0 overflow-hidden transition-all duration-500">
                                <div class="pt-4 border-t border-gray-100">
                                    <p class="text-gray-600 leading-relaxed mb-4">Nos consultations en ligne sont simples et sécurisées :</p>
                                    <div class="bg-blue-50 rounded-2xl p-4 mb-4">
                                        <h4 class="font-semibold text-blue-800 mb-2">Processus de consultation :</h4>
                                        <ol class="space-y-2 text-blue-700">
                                            <li class="flex items-start">
                                                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm mr-3 mt-0.5">1</span>
                                                <span>Prenez rendez-vous en ligne</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm mr-3 mt-0.5">2</span>
                                                <span>Recevez un lien sécurisé par email</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm mr-3 mt-0.5">3</span>
                                                <span>Rejoignez la consultation à l'heure prévue</span>
                                            </li>
                                        </ol>
                                    </div>
                                    <p class="text-sm text-gray-500">* Une connexion internet stable et un ordinateur/tablette avec caméra sont nécessaires.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section CTA Améliorée -->
        <section class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan avec gradient et image -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-purple-900 to-blue-900"></div>
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white/5 to-transparent animate-pulse"></div>
            </div>
            
            <div class="relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="mb-8" data-aos="fade-up" data-aos-delay="100">
                        <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-6 py-2 mb-6 border border-white/30">
                            <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Service disponible 24/7</span>
                        </div>
                        
                        <h2 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                            <span class="bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent">
                                Besoin d'un rendez-vous ?
                            </span>
                        </h2>
                        
                        <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto leading-relaxed">
                            Notre équipe médicale expérimentée est là pour vous accompagner dans votre parcours de santé
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-6 mb-12" data-aos="fade-up" data-aos-delay="200">
                        <a href="login.php" class="group relative inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-white to-gray-100 text-blue-900 font-bold rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <span class="relative z-10 flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Prendre rendez-vous maintenant
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-gray-100 to-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        
                        <a href="#contact" class="group relative inline-flex items-center justify-center px-10 py-4 border-2 border-white text-white font-bold rounded-full overflow-hidden transition-all duration-300 hover:bg-white hover:text-blue-900 hover:scale-105">
                            <span class="relative z-10 flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Nous contacter
                            </span>
                        </a>
                    </div>
                    
                    <!-- Statistiques rapides -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-blue-300 mb-1">5 min</div>
                            <div class="text-sm text-gray-300">Temps de prise de RDV</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-purple-300 mb-1">24h</div>
                            <div class="text-sm text-gray-300">Confirmation rapide</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-pink-300 mb-1">100%</div>
                            <div class="text-sm text-gray-300">Sécurisé</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-green-300 mb-1">Gratuit</div>
                            <div class="text-sm text-gray-300">Service de réservation</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Services Améliorée -->
        <section id="services" class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50"></div>
            <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Nos Services
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Une gamme complète de services médicaux pour répondre à tous vos besoins de santé
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <!-- Service 1 - Consultations Générales -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/consult.jfif" 
                                 alt="Consultations médicales" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-indigo-600/80 via-indigo-500/20 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-indigo-600 transition-colors">Consultations Générales</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Consultations médicales pour adultes et enfants, prévention et suivi personnalisé avec nos médecins généralistes expérimentés.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 text-xs rounded-full">Généraliste</span>
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 text-xs rounded-full">Prévention</span>
                                </div>
                                <a href="login.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-full hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                                    Prendre RDV
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 2 - Spécialistes -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/images.jfif" 
                                 alt="Spécialistes" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-purple-600/80 via-purple-500/20 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-purple-600 transition-colors">Spécialistes</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Cardiologie, pédiatrie, dermatologie, gynécologie, et bien d'autres spécialités avec des médecins experts dans leur domaine.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">Experts</span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">Spécialités</span>
                                </div>
                                <a href="login.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-full hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                    Prendre RDV
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 3 - Vaccinations -->
                    <div class="group relative bg-white rounded-3xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-500 hover:-translate-y-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="relative overflow-hidden">
                            <img src="assets/images/v.jfif" 
                                 alt="Vaccinations" 
                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-pink-600/80 via-pink-500/20 to-transparent"></div>
                            <div class="absolute top-4 right-4">
                                <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-pink-600 transition-colors">Vaccinations</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">Vaccinations pour tous les âges, campagnes de prévention et conseils santé pour protéger votre famille.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <span class="px-3 py-1 bg-pink-100 text-pink-600 text-xs rounded-full">Prévention</span>
                                    <span class="px-3 py-1 bg-pink-100 text-pink-600 text-xs rounded-full">Tous âges</span>
                                </div>
                                <a href="login.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-full hover:from-pink-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                                    Prendre RDV
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section Contact Améliorée -->
        <section id="contact" class="py-20 mb-20 relative overflow-hidden">
            <!-- Arrière-plan décoratif -->
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50"></div>
            <div class="absolute top-0 left-0 w-40 h-40 bg-teal-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-0 right-0 w-32 h-32 bg-cyan-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 bg-clip-text text-transparent">
                        Contactez-nous
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Notre équipe est à votre disposition pour répondre à toutes vos questions
                    </p>
                </div>
                
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Image et informations -->
                        <div class="space-y-8" data-aos="fade-right" data-aos-delay="100">
                            <div class="relative overflow-hidden rounded-3xl shadow-2xl">
                                <img src="assets/images/ok.jpg" 
                                     alt="Notre équipe médicale" 
                                     class="w-full h-96 object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute bottom-6 left-6 text-white">
                                    <h3 class="text-2xl font-bold mb-2">Notre Équipe</h3>
                                    <p class="text-gray-200">Prête à vous accompagner</p>
                                </div>
                            </div>
                            
                            <!-- Informations de contact -->
                            <div class="bg-white rounded-3xl shadow-2xl p-8">
                                <h3 class="text-2xl font-bold mb-6 text-gray-800">Informations de contact</h3>
                                <div class="space-y-6">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-teal-100 rounded-2xl flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Adresse</h4>
                                            <p class="text-gray-600">Douala, Cameroun</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-cyan-100 rounded-2xl flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Téléphone</h4>
                                            <p class="text-gray-600">+237 678 760 117</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Email</h4>
                                            <p class="text-gray-600">contact@vaidyamitra.cm</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulaire de contact -->
                        <div class="bg-white rounded-3xl shadow-2xl p-8" data-aos="fade-left" data-aos-delay="200">
                            <div class="mb-8">
                                <h3 class="text-2xl font-bold mb-2 text-gray-800">Envoyez-nous un message</h3>
                                <p class="text-gray-600">Nous vous répondrons dans les plus brefs délais</p>
                            </div>
                            
                            <form id="contactForm" onsubmit="sendToWhatsApp(event)" class="space-y-6">
                                <div>
                                    <label for="nom" class="block text-gray-700 font-semibold mb-2">Nom complet *</label>
                                    <input type="text" 
                                           id="nom" 
                                           name="nom" 
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300"
                                           placeholder="Votre nom complet">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email *</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300"
                                           placeholder="votre.email@exemple.com">
                                </div>
                                
                                <div>
                                    <label for="message" class="block text-gray-700 font-semibold mb-2">Message *</label>
                                    <textarea id="message" 
                                              name="message" 
                                              rows="5" 
                                              required 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300 resize-none"
                                              placeholder="Décrivez votre demande..."></textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full group relative inline-flex items-center justify-center px-8 py-5 bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white font-bold text-lg rounded-2xl overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl border-2 border-green-400 shadow-lg">
                                    <span class="relative z-10 flex items-center">
                                        <svg class="w-7 h-7 mr-3 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        <span class="text-xl">Envoyer via WhatsApp</span>
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 via-green-700 to-green-800 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-500 opacity-20 animate-pulse"></div>
                                </button>
                            </form>
                            
                            <div class="mt-6 p-6 bg-green-50 rounded-2xl border-2 border-green-200 shadow-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-green-800 mb-1">Envoi WhatsApp</h4>
                                        <p class="text-green-700">Votre message sera envoyé directement sur WhatsApp pour une réponse rapide et personnalisée</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white mt-12">
        <!-- Section principale du footer -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Logo et description -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="assets/images/logo.jpg" alt="Logo Clinique" class="h-12 w-12 object-contain rounded-full">
                        <span class="text-2xl font-bold tracking-wider">VAIDYA MITRA</span>
                    </div>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                        Votre partenaire de confiance pour des soins de santé de qualité. Notre équipe médicale dévouée est là pour vous accompagner dans votre bien-être.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Services rapides -->
                <div>
                    <h3 class="text-xl font-semibold mb-4 border-b border-blue-600 pb-2">Services Rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="login.php" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Prendre Rendez-vous
                        </a></li>
                        <li><a href="login.php" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Espace Patient
                        </a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Nos Services
                        </a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Contact
                        </a></li>
                    </ul>
                </div>

                <!-- Informations de contact -->
                <div>
                    <h3 class="text-xl font-semibold mb-4 border-b border-blue-600 pb-2">Contact</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 mt-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                
                                <p class="text-gray-300">Douala, Cameroun</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-gray-300">+237 678 760 117</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-300">contact@vaidyamitra.cm</span>
                        </div>
                    </div>
                </div>

                <!-- Horaires d'ouverture -->
                <div>
                    <h3 class="text-xl font-semibold mb-4 border-b border-blue-600 pb-2">Horaires</h3>
                    <div class="space-y-2 text-gray-300">
                        <div class="flex justify-between">
                            <span>Lundi - Vendredi</span>
                            <span>8h - 20h</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Samedi</span>
                            <span>9h - 17h</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dimanche</span>
                            <span>Fermé</span>
                        </div>
                        <div class="mt-4 p-2 bg-red-600 rounded text-center">
                            <span class="font-semibold">Urgences 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section inférieure du footer -->
        <div class="border-t border-blue-700">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-300">&copy; 2025 <span class="font-semibold">VAIDYA MITRA</span>. Tous droits réservés.</p>
                    </div>
                    <div class="flex space-x-6 text-sm">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">Politique de confidentialité</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">Conditions d'utilisation</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">Mentions légales</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" class="scroll-to-top" title="Retour en haut">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Scroll to Top Button
        const scrollToTopBtn = document.getElementById('scroll-to-top');
        
        // Show button when user scrolls down 300px
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        // Smooth scroll to top when button is clicked
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Animation FAQ
        document.querySelectorAll('.faq-button').forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.nextElementSibling;
                if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                    content.style.maxHeight = null;
                } else {
                    document.querySelectorAll('.faq-button + div').forEach(div => div.style.maxHeight = null);
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });
        });
        
        // Fonction pour envoyer le formulaire par WhatsApp
        function sendToWhatsApp(event) {
            event.preventDefault();
            
            // Récupérer les valeurs du formulaire
            const nom = document.getElementById('nom').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;
            
            // Vérifier que tous les champs sont remplis
            if (!nom || !email || !message) {
                alert('Veuillez remplir tous les champs du formulaire.');
                return;
            }
            
            // Formater le message pour WhatsApp
            const whatsappMessage = `*Nouveau message de contact - Clinique Vaidya Mitra*

*Nom:* ${nom}
*Email:* ${email}
*Message:* ${message}

---
Envoyé depuis le site web de la clinique.`;
            
            // Encoder le message pour l'URL
            const encodedMessage = encodeURIComponent(whatsappMessage);
            
            // Numéro WhatsApp du Cameroun (avec l'indicatif +237)
            const phoneNumber = '237678760117';
            
            // Créer l'URL WhatsApp
            const whatsappURL = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
            
            // Ouvrir WhatsApp dans un nouvel onglet
            window.open(whatsappURL, '_blank');
            
            // Afficher un message de confirmation
            alert('Redirection vers WhatsApp... Votre message sera envoyé au numéro +237 678 760 117');
            
            // Optionnel : Réinitialiser le formulaire
            document.getElementById('contactForm').reset();
        }
    </script>
</body>
</html>
