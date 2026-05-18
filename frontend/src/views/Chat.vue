<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans flex flex-col" style="height:100vh;">
    <div class="flex flex-1 overflow-hidden max-w-7xl w-full mx-auto px-6 lg:px-12 py-10 gap-6">

      <!-- ===== LEFT: Conversation list ===== -->
      <aside class="w-80 shrink-0 flex flex-col border-r border-black/[0.05] pr-6 overflow-y-auto">
        <div class="mb-8 space-y-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Ziņojumi</span>
          <h2 class="text-3xl font-medium tracking-tight">Sarunas</h2>
        </div>

        <div v-if="conversations.length === 0" class="text-sm text-gray-400 italic mt-4">
          Nav sarunu vēl.
        </div>

        <div
          v-for="conv in conversations"
          :key="conv.id"
          @click="selectConversation(conv)"
          class="py-4 border-b border-black/[0.04] cursor-pointer transition-all duration-300 hover:pl-2 group relative"
          :class="{ 'pl-2 border-l-2 border-blue-600': selectedConv?.id === conv.id }"
        >
          <div class="flex justify-between items-start gap-2">
            <div class="space-y-0.5 min-w-0 flex-1">
              <p class="text-sm font-medium group-hover:text-blue-600 transition-colors">
                {{ otherPerson(conv) }}
              </p>
              <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
                {{ conv.vacancy?.title || 'Vispārīga saruna' }}
              </p>
              <p class="text-xs text-gray-400 mt-1 truncate max-w-[200px]">
                {{ conv.latest_message?.body || '—' }}
              </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <span
                v-if="conv.unread_count > 0"
                class="bg-blue-600 text-white text-[9px] font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1"
              >
                {{ conv.unread_count }}
              </span>
              <button
                @click.stop="deleteConversation(conv)"
                title="Dzēst sarunu"
                class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-500 transition-all"
              >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- ===== RIGHT: Chat window ===== -->
      <main class="flex-1 flex flex-col overflow-hidden">

        <!-- Empty state -->
        <div v-if="!selectedConv" class="flex-1 flex flex-col items-center justify-center text-center space-y-4">
          <p class="text-5xl font-serif italic text-gray-200">Izvēlieties sarunu</p>
          <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400">vai sāciet jaunu no vakances lapas</p>
        </div>

        <template v-else>
          <!-- Header -->
          <div class="pb-6 border-b border-black/[0.05] mb-6 flex items-start justify-between">
            <div class="space-y-1">
              <h3 class="text-2xl font-medium tracking-tight">{{ otherPerson(selectedConv) }}</h3>
              <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
                {{ selectedConv.vacancy?.company }} — {{ selectedConv.vacancy?.title || 'Vispārīga saruna' }}
              </p>
            </div>
          </div>

          <!-- Messages -->
          <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar" ref="messagesEl">
            <div
              v-for="msg in messages"
              :key="msg.id"
              class="flex"
              :class="msg.sender_id === currentUserId ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[65%] px-5 py-3 text-sm leading-relaxed"
                :class="msg.sender_id === currentUserId
                  ? 'bg-black text-white'
                  : 'bg-gray-50 border border-black/[0.05] text-gray-800'"
              >
                <p>{{ msg.body }}</p>
                <p
                  class="text-[9px] mt-1 opacity-50 uppercase tracking-widest"
                  :class="msg.sender_id === currentUserId ? 'text-right' : ''"
                >
                  {{ formatTime(msg.created_at) }}
                  <span v-if="msg.sender_id === currentUserId && msg.read_at"> · Lasīts</span>
                </p>
              </div>
            </div>

            <div v-if="messages.length === 0" class="text-center text-sm text-gray-400 italic py-12">
              Sāciet sarunu, uzrakstot ziņojumu zemāk.
            </div>
          </div>

          <!-- Input -->
          <div class="mt-6 pt-6 border-t border-black/[0.05] flex gap-4 items-end">
            <textarea
              v-model="newMessage"
              @keydown.enter.exact.prevent="sendMessage"
              rows="2"
              placeholder="Rakstiet ziņojumu... (Enter — sūtīt)"
              class="flex-1 bg-transparent border border-gray-100 p-4 text-sm outline-none focus:border-black transition-colors resize-none"
            ></textarea>
            <button
              @click="sendMessage"
              :disabled="!newMessage.trim()"
              class="px-8 py-4 bg-black text-white text-[10px] font-bold uppercase tracking-widest hover:bg-blue-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed shrink-0"
            >
              Sūtīt
            </button>
          </div>
        </template>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';
import api from '@/services/api.js';
import { useToast } from '@/composables/useToast';

const { error, success } = useToast();

const conversations = ref([]);
const selectedConv = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messagesEl = ref(null);
const currentUserId = Number(localStorage.getItem('user_id'));
const token = localStorage.getItem('token');

let pollInterval = null;
let echoConvId = null;

const authHeaders = { headers: { Authorization: `Bearer ${token}` } };

const otherPerson = (conv) => {
  if (conv.applicant?.id === currentUserId) {
    const e = conv.employer;
    return e ? `${e.firstname || ''} ${e.lastname || ''}`.trim() || e.username : '—';
  }
  const a = conv.applicant;
  return a ? `${a.firstname || ''} ${a.lastname || ''}`.trim() || a.username : '—';
};

const formatTime = (d) =>
  new Date(d).toLocaleString('lv-LV', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });

const scrollToBottom = async () => {
  await nextTick();
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight;
};

const fetchConversations = async () => {
  try {
    const res = await api.get('/chat/conversations', authHeaders);
    conversations.value = res.data;
  } catch (e) {
    // silent
  }
};

const listenToConversation = (convId) => {
  if (!window.Echo) return;

  // Leave previous channel
  if (echoConvId !== null) {
    window.Echo.leave(`conversation.${echoConvId}`);
    echoConvId = null;
  }

  echoConvId = convId;
  window.Echo.private(`conversation.${convId}`)
    .listen('.MessageSent', (data) => {
      // Avoid duplicate if we somehow receive our own (toOthers should prevent it)
      if (!messages.value.find(m => m.id === data.id)) {
        messages.value.push(data);
        scrollToBottom();
      }
      // Update conversation list preview
      const conv = conversations.value.find(c => c.id === data.conversation_id);
      if (conv) conv.latest_message = data;
    });
};

const selectConversation = async (conv) => {
  selectedConv.value = conv;
  conv.unread_count = 0;
  await loadMessages(conv.id);
  listenToConversation(conv.id);
};

const loadMessages = async (convId) => {
  try {
    const res = await api.get(`/chat/${convId}/messages`, authHeaders);
    messages.value = res.data;
    scrollToBottom();
  } catch (e) {
    // silent
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConv.value) return;
  const body = newMessage.value.trim();
  newMessage.value = '';
  try {
    const res = await api.post(`/chat/${selectedConv.value.id}/send`, { body }, authHeaders);
    messages.value.push(res.data);
    scrollToBottom();
    // Update preview in conversation list
    const conv = conversations.value.find(c => c.id === selectedConv.value.id);
    if (conv) conv.latest_message = res.data;
  } catch (e) {
    error('Neizdevās nosūtīt ziņojumu');
    newMessage.value = body;
  }
};

const deleteConversation = async (conv) => {
  if (!confirm(`Vai tiešām dzēst sarunu ar ${otherPerson(conv)}? Visi ziņojumi tiks dzēsti.`)) return;
  try {
    await api.delete(`/chat/${conv.id}`, authHeaders);
    conversations.value = conversations.value.filter(c => c.id !== conv.id);
    if (selectedConv.value?.id === conv.id) {
      selectedConv.value = null;
      messages.value = [];
      if (window.Echo && echoConvId !== null) {
        window.Echo.leave(`conversation.${echoConvId}`);
        echoConvId = null;
      }
    }
    success('Saruna dzēsta');
  } catch (e) {
    error('Neizdevās dzēst sarunu');
  }
};

// Poll conversations list every 8s to refresh unread counts from other conversations
const startPolling = () => {
  pollInterval = setInterval(async () => {
    await fetchConversations();
  }, 8000);
};

onMounted(async () => {
  await fetchConversations();
  startPolling();
});

onUnmounted(() => {
  clearInterval(pollInterval);
  if (window.Echo && echoConvId !== null) {
    window.Echo.leave(`conversation.${echoConvId}`);
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;500;700&display=swap');
.font-serif { font-family: 'DM Serif Display', serif; }
* { font-family: 'Inter', sans-serif; }

.custom-scrollbar::-webkit-scrollbar { width: 3px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #eee; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #2563eb; }
</style>
