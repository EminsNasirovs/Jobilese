<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <main class="max-w-7xl mx-auto px-6 lg:px-12 py-20">

      <section class="mb-20 space-y-6">
        <div class="flex items-center gap-4 overflow-hidden">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Pārvaldība</span>
        </div>
        <h1 class="text-6xl md:text-8xl font-medium tracking-tight leading-none uppercase italic font-serif">
          Saņemtie <br />
          <span class="text-blue-600">Pieteikumi.</span>
        </h1>
      </section>

      <div v-if="applications.length === 0" class="py-40 text-center border-t border-black/[0.05]">
        <p class="text-4xl font-serif italic text-gray-200">Pašlaik pieteikumu nav.</p>
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mt-4">Jūsu vakancēm vēl neviens nav pieteicies.</p>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="app in applications"
          :key="app.id"
          class="bg-white border border-black/[0.03] hover:border-black/10 transition-all duration-500 p-8"
        >
          <!-- Top row -->
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-1">
              <h3 class="text-xl font-medium tracking-tight">
                {{ app.user?.firstname }} {{ app.user?.lastname }}
              </h3>
              <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ app.vacancy?.title || "–" }}</p>
              <p class="text-sm text-gray-500">{{ app.user?.email }}</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
              <span
                class="text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-full"
                :class="{
                  'bg-yellow-50 text-yellow-600': app.status === 'pending',
                  'bg-green-50 text-green-600': app.status === 'accepted',
                  'bg-red-50 text-red-500': app.status === 'denied',
                }"
              >
                {{ app.status === 'pending' ? 'Gaida' : app.status === 'accepted' ? 'Pieņemts' : 'Noraidīts' }}
              </span>
              <button
                v-if="app.cv_path"
                @click="openCv(app.id)"
                class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest bg-black text-white px-5 py-3 hover:bg-blue-600 transition-colors"
              >
                CV
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path d="M7 17L17 7M17 7H8M17 7V16" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Cover letter -->
          <p v-if="app.cover_letter" class="mt-4 text-sm text-gray-500 italic leading-relaxed border-l-2 border-gray-100 pl-4">
            "{{ app.cover_letter }}"
          </p>

          <!-- Employer previous response -->
          <div v-if="app.employer_response" class="mt-4 p-4 bg-gray-50 border border-gray-100 text-sm text-gray-600 space-y-1">
            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Jūsu atbilde</p>
            <p>{{ app.employer_response }}</p>
          </div>

          <!-- Action buttons — only for pending applications -->
          <div v-if="app.status === 'pending'" class="mt-6 border-t border-black/[0.05] pt-6 flex gap-3">
            <button
              @click="openAcceptModal(app)"
              class="px-8 py-3 bg-green-600 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-green-700 transition-colors"
            >
              Pieņemt
            </button>
            <button
              @click="denyApp(app)"
              class="px-8 py-3 bg-red-500 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-red-600 transition-colors"
            >
              Noraidīt
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- CV Modal -->
    <div
      v-if="showCvModal"
      class="fixed inset-0 z-[100] bg-white/90 backdrop-blur-md flex items-center justify-center p-6 animate-in fade-in duration-500"
    >
      <div class="bg-white border border-black/[0.05] w-full max-w-6xl h-[90vh] relative shadow-[0_40px_100px_rgba(0,0,0,0.1)] flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-black/[0.05]">
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Kandidāta dokumentācija</span>
          <button @click="showCvModal = false" class="text-2xl hover:text-blue-600 transition-colors">&times;</button>
        </div>
        <div class="flex-grow bg-gray-50 p-4">
          <iframe v-if="pdfUrl" :src="pdfUrl" class="w-full h-full border-none"></iframe>
        </div>
      </div>
    </div>

    <!-- Accept Modal -->
    <div
      v-if="showAcceptModal"
      class="fixed inset-0 z-[200] flex items-center justify-center p-6"
      @click.self="showAcceptModal = false"
    >
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showAcceptModal = false"></div>
      <div class="relative bg-white w-full max-w-lg shadow-[0_40px_100px_rgba(0,0,0,0.15)] z-10 animate-in fade-in slide-in-from-bottom-4 duration-300">
        <div class="p-8 space-y-6">
          <div class="space-y-1">
            <p class="text-[10px] uppercase font-bold tracking-[0.3em] text-gray-400">Pieņemt kandidātu</p>
            <h2 class="text-2xl font-medium tracking-tight">
              {{ acceptingApp?.user?.firstname }} {{ acceptingApp?.user?.lastname }}
            </h2>
            <p class="text-sm text-gray-400">{{ acceptingApp?.vacancy?.title }}</p>
          </div>

          <div class="space-y-2">
            <p class="text-[10px] uppercase font-bold tracking-[0.2em] text-gray-400">
              Ziņa kandidātam <span class="normal-case font-normal text-gray-300">(neobligāti)</span>
            </p>
            <textarea
              v-model="acceptMessage"
              rows="4"
              placeholder="Apsveicam! Esam priecīgi jūs uzaicināt uz darba interviju..."
              class="w-full bg-transparent border border-gray-100 p-4 text-sm outline-none focus:border-black transition-colors resize-none"
            ></textarea>
          </div>

          <div class="flex gap-3">
            <button
              @click="confirmAccept"
              :disabled="acceptLoading"
              class="flex-1 px-8 py-4 bg-green-600 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-green-700 transition-colors disabled:opacity-50"
            >
              {{ acceptLoading ? 'Apstrādā...' : 'Pieņemt' }}
            </button>
            <button
              @click="showAcceptModal = false"
              class="px-8 py-4 border border-black text-[10px] font-bold uppercase tracking-widest hover:bg-black hover:text-white transition-colors"
            >
              Atcelt
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api, { storageUrl } from "@/services/api.js";
import { useToast } from "@/composables/useToast";

const { error, success } = useToast();

const applications = ref([]);
const showCvModal = ref(false);
const pdfUrl = ref(null);

const showAcceptModal = ref(false);
const acceptingApp = ref(null);
const acceptMessage = ref("");
const acceptLoading = ref(false);

const openCv = (id) => {
  const app = applications.value.find((a) => a.id === id);
  if (!app?.cv_path) { error("CV nav atrasts"); return; }
  pdfUrl.value = storageUrl(app.cv_path);
  showCvModal.value = true;
};

const openAcceptModal = (app) => {
  acceptingApp.value = app;
  acceptMessage.value = "";
  showAcceptModal.value = true;
};

const confirmAccept = async () => {
  if (!acceptingApp.value) return;
  acceptLoading.value = true;
  try {
    const app = acceptingApp.value;
    const msg = acceptMessage.value.trim();

    await api.post(`/applications/${app.id}/respond`, {
      status: "accepted",
      employer_response: msg || null,
    });
    app.status = "accepted";
    app.employer_response = msg || null;

    if (msg) {
      const chatRes = await api.post("/chat/start", {
        other_user_id: app.user_id,
        vacancy_id: app.vacancy_id,
      });
      await api.post(`/chat/${chatRes.data.id}/send`, { body: msg });
    }

    success("Kandidāts pieņemts!");
    showAcceptModal.value = false;
    acceptingApp.value = null;
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās pieņemt kandidātu");
  } finally {
    acceptLoading.value = false;
  }
};

const denyApp = async (app) => {
  try {
    await api.post(`/applications/${app.id}/respond`, { status: "denied", employer_response: null });
    app.status = "denied";
    success("Kandidāts noraidīts.");
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās noraidīt");
  }
};

onMounted(async () => {
  try {
    const res = await api.get("/applications");
    applications.value = res.data;
  } catch (err) {
    error("Neizdevās ielādēt pieteikumus");
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;500;700&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

main {
  font-family: 'Inter', sans-serif;
}
</style>
