<x-layout>
    <style>
        .ai-response h1, .ai-response h2, .ai-response h3 { font-weight: 700; color: #f8fafc; margin-top: 1rem; margin-bottom: 0.5rem; }
        .ai-response h2 { font-size: 1.1rem; border-bottom: 1px solid #334155; padding-bottom: 0.25rem; color: #818cf8; }
        .ai-response p { margin-bottom: 0.6rem; line-height: 1.6; color: #e2e8f0; }
        .ai-response ul, .ai-response ol { padding-left: 1.25rem; margin-top: 0.4rem; margin-bottom: 0.6rem; }
        .ai-response li { margin-bottom: 0.25rem; color: #cbd5e1; }
        .ai-response strong { color: #ffffff; font-weight: 600; }
        .ai-response table { width: 100%; border-collapse: collapse; margin: 0.75rem 0; font-size: 0.825rem; overflow-x: auto; display: block; }
        .ai-response th, .ai-response td { border: 1px solid #334155; padding: 0.5rem 0.75rem; text-align: left; }
        .ai-response th { background-color: #0f172a; color: #a5b4fc; }
        .ai-response td { background-color: #1e293b; color: #cbd5e1; }
    </style>

    <div class="max-w-6xl mx-auto space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span class="text-3xl leading-none">🤖</span>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Assistant IA ERP</h1>
                    <p class="text-xs font-medium text-gray-600">Analyse intelligente des stocks, ventes et créances clients en temps réel.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
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

        <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 flex flex-col lg:flex-row h-[700px] overflow-hidden">
            <!-- Conversation history -->
            <aside class="w-full lg:w-72 shrink-0 border-b lg:border-b-0 lg:border-r border-slate-800 bg-slate-950 flex flex-col">
                <div class="p-4 border-b border-slate-800">
                    <button
                        id="new-chat-btn"
                        type="button"
                        data-endpoint="{{ route('ai.conversations.store') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition cursor-pointer"
                    >
                        <span class="text-lg leading-none">+</span>
                        Nouvelle conversation
                    </button>
                </div>

                <div class="px-4 pt-4 pb-2">
                    <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Historique</h2>
                </div>

                <nav id="conversation-list" class="flex-1 overflow-y-auto px-2 pb-3 space-y-1">
                    @forelse($conversations as $item)
                        <a
                            href="{{ route('ai.conversations.show', $item) }}"
                            class="conversation-item group flex items-center gap-2 rounded-xl px-3 py-2.5 transition {{ $item->id === $conversation->id ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}"
                        >
                            <span class="text-sm shrink-0">💬</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium">{{ $item->title ?: 'Nouvelle conversation' }}</span>
                                <span class="block text-[10px] text-slate-500 mt-0.5">
                                    {{ $item->updated_at?->isToday() ? "Aujourd'hui" : ($item->updated_at?->isYesterday() ? 'Hier' : $item->updated_at?->format('d/m/Y')) }}
                                    @if($item->messages_count ?? 0) · {{ $item->messages_count }} messages @endif
                                </span>
                            </span>
                        </a>
                    @empty
                        <p class="px-3 py-4 text-xs text-slate-500">Aucune conversation.</p>
                    @endforelse
                </nav>
            </aside>

            <!-- Chat -->
            <section class="min-w-0 flex-1 flex flex-col">
                <header class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-800 bg-slate-900">
                    <div class="min-w-0">
                        <h2 id="conversation-title" class="font-semibold text-white truncate">{{ $conversation->title ?: 'Nouvelle conversation' }}</h2>
                        <p class="text-[11px] text-slate-500">Vos conversations sont privées et liées à votre compte.</p>
                    </div>
                    <button
                        id="delete-chat-btn"
                        type="button"
                        data-endpoint="{{ route('ai.conversations.destroy', $conversation) }}"
                        class="shrink-0 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 border border-slate-700 hover:bg-rose-950/40 hover:border-rose-900 transition cursor-pointer"
                    >
                        Supprimer
                    </button>
                </header>

                <div id="chat-messages" class="flex-1 overflow-y-auto p-5 md:p-6 space-y-4 scroll-smooth">
                    @if($messages->isEmpty())
                        <div id="welcome-message" class="flex gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                            <div class="bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-4 py-3 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                                Bonjour ! Je suis votre assistant ERP. Posez-moi une question sur vos ventes, l'état de votre stock, ou vos clients débiteurs.
                            </div>
                        </div>
                    @else
                        @foreach($messages as $msg)
                            <div class="flex gap-3 text-sm {{ $msg->role === 'user' ? 'justify-end' : '' }}">
                                @if($msg->role === 'assistant')
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                                @endif
                                <div class="{{ $msg->role === 'user' ? 'bg-indigo-600 text-white' : 'ai-response bg-slate-800 text-slate-100 border border-slate-700/60' }} rounded-2xl px-5 py-3.5 max-w-2xl leading-relaxed shadow-xs">
                                    @if($msg->role === 'user')
                                        {{ $msg->content }}
                                    @else
                                        {!! Str::markdown($msg->content ?? '') !!}
                                    @endif
                                </div>
                                @if($msg->role === 'user')
                                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">Vous</div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="p-4 border-t border-slate-800 bg-slate-950 space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-400 mr-1">Raccourcis :</span>
                        <button type="button" class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-blue-300 border border-slate-700 hover:bg-slate-800 hover:border-blue-500/50 transition-all cursor-pointer" data-prompt="Fais-moi un résumé des ventes du mois">
                            📊 Résumé des ventes
                        </button>
                        <button type="button" class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-rose-300 border border-slate-700 hover:bg-slate-800 hover:border-rose-500/50 transition-all cursor-pointer" data-prompt="Quels produits sont presque en rupture ?">
                            📦 Alertes ruptures
                        </button>
                        <button type="button" class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-amber-300 border border-slate-700 hover:bg-slate-800 hover:border-amber-500/50 transition-all cursor-pointer" data-prompt="Quels clients ont des créances élevées ?">
                            💰 Clients débiteurs
                        </button>
                    </div>

                    <form id="chat-form" data-endpoint="{{ route('ai.conversations.messages.store', $conversation) }}" class="flex gap-2">
                        @csrf
                        <input
                            type="text"
                            id="user-input"
                            placeholder="Posez votre question..."
                            class="flex-1 min-w-0 bg-slate-900 text-slate-100 placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm border border-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                            autocomplete="off"
                            maxlength="5000"
                            required
                        >
                        <button type="submit" id="send-btn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 cursor-pointer shadow-xs">
                            <span>Envoyer</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-layout>
