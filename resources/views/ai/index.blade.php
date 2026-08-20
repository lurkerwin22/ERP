<x-layout>
    <!-- Markdown Parser & Dark Theme Typography Styling -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        /* Custom Markdown Styling for Dark Chat Theme */
        .ai-response h1, .ai-response h2, .ai-response h3 { font-weight: 700; color: #f8fafc; margin-top: 1rem; margin-bottom: 0.5rem; }
        .ai-response h2 { font-size: 1.1rem; border-bottom: 1px solid #334155; padding-bottom: 0.25rem; color: #818cf8; }
        .ai-response h3 { font-size: 0.95rem; color: #cbd5e1; }
        .ai-response p { margin-bottom: 0.6rem; line-height: 1.6; color: #e2e8f0; }
        .ai-response ul, .ai-response ol { padding-left: 1.25rem; margin-top: 0.4rem; margin-bottom: 0.6rem; }
        .ai-response ul { list-style-type: disc; }
        .ai-response ol { list-style-type: decimal; }
        .ai-response li { margin-bottom: 0.25rem; color: #cbd5e1; }
        .ai-response strong { color: #ffffff; font-weight: 600; }
        .ai-response table { width: 100%; border-collapse: collapse; margin-top: 0.75rem; margin-bottom: 0.75rem; font-size: 0.825rem; overflow-x: auto; display: block; }
        .ai-response th, .ai-response td { border: 1px solid #334155; padding: 0.5rem 0.75rem; text-align: left; }
        .ai-response th { background-color: #0f172a; color: #a5b4fc; font-weight: 600; }
        .ai-response td { background-color: #1e293b/60; color: #cbd5e1; }
        .ai-response tr:nth-child(even) td { background-color: #0f172a/40; }
        .ai-response code { background-color: #0f172a; padding: 0.15rem 0.35rem; border-radius: 0.25rem; font-size: 0.8rem; color: #a5b4fc; font-family: monospace; }
        .ai-response blockquote { border-left: 3px solid #6366f1; padding-left: 0.75rem; margin: 0.5rem 0; color: #94a3b8; italic: true; }
    </style>

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

            <!-- Proactive Alert Badges -->
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

        <!-- Main Chat Container -->
        <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 flex flex-col h-[650px] overflow-hidden">
            
            <!-- Messages Stream Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 scroll-smooth">
                <!-- Welcome Message -->
                <div class="flex gap-3 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                        IA
                    </div>
                    <div class="bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-4 py-3 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                        Bonjour ! Je suis votre assistant ERP. Posez-moi une question sur vos ventes, l'état de votre stock, ou vos clients débiteurs.
                    </div>
                </div>
            </div>

            <!-- Input & Actions Panel -->
            <div class="p-4 border-t border-slate-800 bg-slate-950 space-y-3">
                
                <!-- Quick Prompt Chips -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 mr-1">Raccourcis :</span>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-blue-300 border border-slate-700 hover:bg-slate-800 hover:border-blue-500/50 hover:text-blue-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.sales') }}"
                            data-prompt="Fais-moi un résumé des ventes du mois">
                        <span>📊</span> Résumé des ventes
                    </button>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-rose-300 border border-slate-700 hover:bg-slate-800 hover:border-rose-500/50 hover:text-rose-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.stock') }}"
                            data-prompt="Quels produits sont presque en rupture ?">
                        <span>📦</span> Alertes ruptures
                    </button>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-amber-300 border border-slate-700 hover:bg-slate-800 hover:border-amber-500/50 hover:text-amber-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.debts') }}"
                            data-prompt="Quels clients ont des créances élevées ?">
                        <span>💰</span> Clients débiteurs
                    </button>
                </div>

                <!-- Input Form -->
                <form id="chat-form" data-endpoint="{{ route('ai.chat') }}" class="flex gap-2">
                    @csrf
                    <input 
                        type="text" 
                        id="user-input" 
                        placeholder="Posez votre question ou sélectionnez un raccourci..." 
                        class="flex-1 bg-slate-900 text-slate-100 placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm border border-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                        autocomplete="off"
                        required
                    >
                    <button type="submit" id="send-btn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 cursor-pointer shadow-xs">
                        <span>Envoyer</span>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Chat Engine JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatStream = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const userInput = document.getElementById('user-input');
            const csrfToken = document.querySelector('input[name="_token"]')?.value;

            // Configure Marked.js for breaking lines properly
            if (typeof marked !== 'undefined') {
                marked.setOptions({
                    breaks: true,
                    gfm: true
                });
            }

            // Handle Chip Clicks
            document.querySelectorAll('.chip-btn').forEach(button => {
                button.addEventListener('click', async function () {
                    const promptText = this.getAttribute('data-prompt');
                    const endpoint = this.getAttribute('data-endpoint');

                    appendMessage('user', promptText);

                    if (endpoint) {
                        const loaderId = appendLoadingIndicator();
                        try {
                            const response = await fetch(endpoint, {
                                headers: { 
                                    'X-Requested-With': 'XMLHttpRequest', 
                                    'Accept': 'application/json' 
                                }
                            });
                            const reportData = await response.json();
                            removeLoadingIndicator(loaderId);
                            appendMessage('assistant', formatReportHtml(reportData), true);
                        } catch (err) {
                            removeLoadingIndicator(loaderId);
                            appendMessage('assistant', '⚠️ Erreur lors de la récupération du rapport.');
                        }
                    } else {
                        submitChatMessage(promptText);
                    }
                });
            });

            // Handle Manual Form Submit
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const text = userInput.value.trim();
                if (!text) return;

                appendMessage('user', text);
                userInput.value = '';
                submitChatMessage(text);
            });

            async function submitChatMessage(messageText) {
                const endpoint = chatForm.getAttribute('data-endpoint');
                const loaderId = appendLoadingIndicator();

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            messages: [
                                { role: 'user', content: messageText }
                            ]
                        })
                    });

                    const data = await response.json();
                    removeLoadingIndicator(loaderId);
                    
                    const content = data.content || data.message || 'Réponse reçue.';
                    appendMessage('assistant', content);
                } catch (err) {
                    removeLoadingIndicator(loaderId);
                    appendMessage('assistant', '⚠️ Impossible d\'interroger le service IA.');
                }
            }

            function appendMessage(role, content, isRawHtml = false) {
                const wrapper = document.createElement('div');
                wrapper.className = `flex gap-3 text-sm ${role === 'user' ? 'justify-end' : ''}`;

                if (role === 'user') {
                    wrapper.innerHTML = `
                        <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-xs px-4 py-3 max-w-xl leading-relaxed shadow-xs">
                            ${escapeHtml(content)}
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                            Vous
                        </div>
                    `;
                } else {
                    // Parse Markdown to HTML if not pre-rendered HTML
                    let formattedContent = content;
                    if (!isRawHtml && typeof marked !== 'undefined') {
                        formattedContent = marked.parse(content);
                    }

                    wrapper.innerHTML = `
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                            IA
                        </div>
                        <div class="ai-response bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-5 py-3.5 max-w-3xl leading-relaxed border border-slate-700/60 shadow-xs">
                            ${formattedContent}
                        </div>
                    `;
                }

                chatStream.appendChild(wrapper);
                chatStream.scrollTop = chatStream.scrollHeight;
            }

            function appendLoadingIndicator() {
                const id = 'loader-' + Date.now();
                const wrapper = document.createElement('div');
                wrapper.id = id;
                wrapper.className = 'flex gap-3 text-sm';
                wrapper.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">
                        IA
                    </div>
                    <div class="bg-slate-800 text-slate-300 rounded-2xl rounded-tl-xs px-4 py-3 border border-slate-700/60 shadow-xs flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                `;
                chatStream.appendChild(wrapper);
                chatStream.scrollTop = chatStream.scrollHeight;
                return id;
            }

            function removeLoadingIndicator(id) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }

            function formatReportHtml(report) {
                if (report.type === 'stock') {
                    const items = report.data.items || report.data;
                    if (!Array.isArray(items) || items.length === 0) {
                        return '✅ Aucun produit en rupture ou proche du seuil d\'alerte.';
                    }
                    const list = items.slice(0, 10).map(i => `
                        <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                            <span class="font-medium text-slate-200">${i.name || i.label}</span>
                            <span class="text-xs px-2 py-0.5 rounded ${i.stock <= 0 ? 'bg-rose-900/60 text-rose-300' : 'bg-amber-900/60 text-amber-300'}">
                                ${i.stock} unités (Seuil: ${i.alert_threshold || '-'})
                            </span>
                        </li>
                    `).join('');
                    return `<div class="font-semibold text-amber-400 mb-2">${report.title}</div><ul class="space-y-1">${list}</ul>`;
                }

                if (report.type === 'sales') {
                    const total = Number(report.data.total_sales || report.data.total || 0).toLocaleString('fr-TN', { minimumFractionDigits: 2 });
                    const count = report.data.count || report.data.sales_count || 0;
                    return `
                        <div class="font-semibold text-blue-400 mb-2">${report.title}</div>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                                <div class="text-xs text-slate-400">Chiffre d'affaires</div>
                                <div class="text-base font-bold text-emerald-400">${total} TND</div>
                            </div>
                            <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                                <div class="text-xs text-slate-400">Commandes</div>
                                <div class="text-base font-bold text-slate-200">${count}</div>
                            </div>
                        </div>
                    `;
                }

                if (report.type === 'debts') {
                    const debts = report.data.debts || report.data;
                    if (!Array.isArray(debts) || debts.length === 0) {
                        return '✅ Aucun client débiteur en retard.';
                    }
                    const list = debts.slice(0, 10).map(d => `
                        <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                            <span class="font-medium text-slate-200">${d.customer_name || d.name}</span>
                            <span class="font-semibold text-rose-400">${Number(d.balance || d.debt || 0).toFixed(2)} TND</span>
                        </li>
                    `).join('');
                    return `<div class="font-semibold text-rose-400 mb-2">${report.title}</div><ul class="space-y-1">${list}</ul>`;
                }

                return `<pre class="text-xs bg-slate-950 p-2 rounded overflow-x-auto">${JSON.stringify(report.data, null, 2)}</pre>`;
            }

            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</x-layout>