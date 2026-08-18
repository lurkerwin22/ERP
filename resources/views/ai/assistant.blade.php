@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 sm:p-6" x-data="aiAssistant()">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[80vh]">
        
        <!-- Header -->
        <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-xl">
                    🤖
                </div>
                <div>
                    <h2 class="font-semibold text-base">Assistant IA Business</h2>
                    <p class="text-xs text-slate-400">Analyse intelligente de votre ERP</p>
                </div>
            </div>
            <span class="px-2.5 py-1 text-xs rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">
                Connecté
            </span>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50" x-ref="chatContainer">
            
            <!-- Welcome Banner -->
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-lg p-4 text-sm text-indigo-900">
                <p class="font-medium mb-1">Bonjour {{ auth()->user()->name }} 👋</p>
                <p class="text-indigo-700">Que souhaitez-vous savoir sur votre entreprise aujourd'hui ?</p>
                
                <!-- Quick Suggestion Chips -->
                <div class="flex flex-wrap gap-2 mt-3">
                    <button @click="sendQuick('Quels produits sont presque en rupture ?')" 
                            class="px-3 py-1.5 bg-white border border-indigo-200 text-xs font-medium text-indigo-700 rounded-md hover:bg-indigo-100/50 transition">
                        📦 Stock bas
                    </button>
                    <button @click="sendQuick('Combien avons-nous vendu cette semaine ?')" 
                            class="px-3 py-1.5 bg-white border border-indigo-200 text-xs font-medium text-indigo-700 rounded-md hover:bg-indigo-100/50 transition">
                        📊 Ventes de la semaine
                    </button>
                    <button @click="sendQuick('Quels clients ont des dettes non réglées ?')" 
                            class="px-3 py-1.5 bg-white border border-indigo-200 text-xs font-medium text-indigo-700 rounded-md hover:bg-indigo-100/50 transition">
                        💳 Dettes clients
                    </button>
                </div>
            </div>

            <!-- Messages Stream -->
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[80%] text-sm' : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[80%] text-sm shadow-sm'">
                        <div class="whitespace-pre-line" x-text="msg.content"></div>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-none px-4 py-3 text-slate-500 text-xs flex items-center space-x-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>L'assistant analyse les données de l'ERP...</span>
                </div>
            </div>
        </div>

        <!-- Input Form -->
        <div class="p-3 bg-white border-t border-slate-200">
            <form @submit.prevent="sendMessage()" class="flex items-center space-x-2">
                <input type="text" 
                       x-model="input" 
                       :disabled="isLoading"
                       placeholder="Posez votre question sur les ventes, stocks ou clients..." 
                       class="flex-1 border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent disabled:bg-slate-50">
                <button type="submit" 
                        :disabled="isLoading || !input.trim()"
                        class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition flex items-center justify-center">
                    <span>Envoyer</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function aiAssistant() {
    return {
        input: '',
        messages: [],
        isLoading: false,

        sendQuick(promptText) {
            this.input = promptText;
            this.sendMessage();
        },

        async sendMessage() {
            if (!this.input.trim() || this.isLoading) return;

            const userQuery = this.input;
            this.messages.push({ role: 'user', content: userQuery });
            this.input = '';
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const response = await fetch("{{ route('ai.assistant.chat') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        message: userQuery,
                        history: this.messages.slice(-6) // Keep recent context
                    })
                });

                const data = await response.json();
                this.messages.push({ role: 'assistant', content: data.reply });
            } catch (error) {
                this.messages.push({ 
                    role: 'assistant', 
                    content: "Une erreur est survenue lors du traitement de votre demande." 
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
                container.scrollTop = container.scrollHeight;
            });
        }
    }
}
</script>
@endsection