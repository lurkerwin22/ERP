<x-layout>
    <div class="max-w-5xl mx-auto space-y-5">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span class="text-3xl leading-none">🤖</span>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Assistant IA ERP</h1>
                    <p class="text-xs font-medium text-gray-600">Analyse intelligente des stocks, ventes et créances clients en temps réel.</p>
                </div>
            </div>

            <!-- Proactive Alert Badges (High Contrast) -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                    📦 {{ $alerts['low_stock_count'] ?? 0 }} alertes stock
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                    💰 {{ $alerts['high_debt_customers'] ?? 0 }} créances en retard
                </span>
            </div>
        </div>

        <!-- Main Chat Container (High Contrast Background) -->
        <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 flex flex-col h-[620px] overflow-hidden">
            
            <!-- Messages Stream Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-5 scroll-smooth">
                <div class="flex gap-3 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                        IA
                    </div>
                    <div class="bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-4 py-3 max-w-xl leading-relaxed border border-slate-700/60 shadow-sm">
                        Bonjour ! Je suis votre assistant ERP. Posez-moi une question sur vos ventes, l'état de votre stock, ou vos clients débiteurs.
                    </div>
                </div>
            </div>

            <!-- Quick Action Chips -->
            <div class="px-5 py-3 bg-slate-950/60 border-t border-slate-800/80 flex items-center gap-2 overflow-x-auto text-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Suggestions:</span>
                
                <button type="button" data-prompt="Quels produits sont presque en rupture ?" class="quick-prompt-btn px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white border border-slate-700 transition-all shrink-0 font-medium shadow-2xs cursor-pointer flex items-center gap-1.5">
                    <span>🔍</span>
                    <span>Produits en rupture</span>
                </button>
                
                <button type="button" data-prompt="Fais-moi un résumé des ventes du mois" class="quick-prompt-btn px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white border border-slate-700 transition-all shrink-0 font-medium shadow-2xs cursor-pointer flex items-center gap-1.5">
                    <span>📊</span>
                    <span>Résumé des ventes</span>
                </button>
                
                <button type="button" data-prompt="Quels clients ont des créances élevées ?" class="quick-prompt-btn px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white border border-slate-700 transition-all shrink-0 font-medium shadow-2xs cursor-pointer flex items-center gap-1.5">
                    <span>💶</span>
                    <span>Créances clients</span>
                </button>
            </div>

            <!-- Input Bar -->
            <form id="chat-form" data-endpoint="{{ route('ai.chat') }}" class="p-4 border-t border-slate-800 bg-slate-950 flex gap-3">
                @csrf
                <input 
                    type="text" 
                    id="user-input" 
                    placeholder="Posez votre question..." 
                    class="flex-1 bg-slate-900 text-slate-100 rounded-lg px-4 py-2 border border-slate-800 focus:outline-none focus:border-indigo-500"
                    required
                >
                <button type="submit" id="send-btn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition-colors">
                    Envoyer &rarr;
                </button>
            </form>
         

        </div>
    </div>
</x-layout>