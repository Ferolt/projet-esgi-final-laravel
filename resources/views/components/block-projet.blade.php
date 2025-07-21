<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @if (isset($data) && count($data) > 0)
            @foreach ($data as $projet)
            <div class="group h-full">
                <a href="{{ route('projet.show', $projet) }}" class="block h-full">
                    <article class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 h-full flex flex-col justify-between hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:bg-white dark:hover:bg-gray-800">
                        <div class="flex-1 flex flex-col space-y-4">
                            <!-- Titre et statut -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                    {{ $projet->name }}
                                </h3>
                                @if($projet->statut)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 text-blue-800 dark:text-blue-200">
                                        <i class="fas fa-circle text-xs mr-2"></i>
                                        {{ $projet->statut }}
                                    </span>
                                @endif
                            </div>
                            <!-- Projet et date -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-project-diagram text-white text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Projet</span>
                                </div>
                                @if($projet->created_at)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $projet->created_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            <!-- Utilisateur et tâches -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-white text-xs"></i>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $projet->user->name ?? 'Utilisateur' }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    @if($projet->tasks_count ?? 0 > 0)
                                        <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full flex items-center">
                                            <i class="fas fa-tasks mr-1"></i>
                                            {{ $projet->tasks_count ?? 0 }} tâches
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Barre de progression -->
                        @if(isset($projet->tasks_count) && $projet->tasks_count > 0)
                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Progression</span>
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">
                                        {{ $projet->completed_tasks_count ?? 0 }}/{{ $projet->tasks_count }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $projet->tasks_count > 0 ? min((($projet->completed_tasks_count ?? 0) / $projet->tasks_count) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endif
                    </article>
                </a>
            </div>
            @endforeach
    @else
        <div class="col-span-full">
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun projet trouvé</h3>
                <p class="text-gray-500 dark:text-gray-400">Commencez par créer votre premier projet</p>
            </div>
        </div>
        @endif
</div>
