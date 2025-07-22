@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
        <!-- Icône offline -->
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                <i class="fas fa-wifi-slash text-3xl text-orange-500"></i>
            </div>
        </div>

        <!-- Titre -->
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Mode hors ligne
        </h1>

        <!-- Description -->
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            Vous êtes actuellement hors ligne. Certaines fonctionnalités peuvent être limitées.
        </p>

        <!-- Fonctionnalités disponibles -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                Fonctionnalités disponibles :
            </h3>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i>
                    Consultation des projets en cache
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i>
                    Visualisation des tâches
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i>
                    Navigation dans l'application
                </li>
            </ul>
        </div>

        <!-- Statut de connexion -->
        <div class="flex items-center justify-center space-x-2 text-gray-500 dark:text-gray-400 mb-6">
            <div id="connection-indicator" class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
            <span id="connection-text">Vérification de la connexion...</span>
        </div>

        <!-- Bouton de rafraîchissement -->
        <button onclick="window.location.reload()" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
            <i class="fas fa-sync-alt mr-2"></i>
            Vérifier la connexion
        </button>

        <!-- Lien vers l'accueil -->
        <div class="mt-4">
            <a href="/" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<script>
    // Vérification périodique de la connexion
    function checkConnection() {
        const indicator = document.getElementById('connection-indicator');
        const text = document.getElementById('connection-text');
        
        if (navigator.onLine) {
            indicator.className = 'w-3 h-3 bg-green-500 rounded-full';
            text.textContent = 'Connexion rétablie !';
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 2000);
        } else {
            indicator.className = 'w-3 h-3 bg-red-500 rounded-full animate-pulse';
            text.textContent = 'Aucune connexion Internet';
        }
    }

    // Vérifier la connexion au chargement
    checkConnection();

    // Écouter les changements de connexion
    window.addEventListener('online', checkConnection);
    window.addEventListener('offline', checkConnection);

    // Vérification périodique toutes les 5 secondes
    setInterval(checkConnection, 5000);
</script>
@endsection
