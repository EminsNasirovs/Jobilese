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

      <div
        v-if="applications.length === 0"
        class="py-40 text-center border-t border-black/[0.05]"
      >
        <p class="text-4xl font-serif italic text-gray-200">Pašlaik pieteikumu nav.</p>
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mt-4">Jūsu vakancēm vēl neviens nav pieteicies.</p>
      </div>

      <div v-else class="space-y-4">
        <div class="grid grid-cols-12 px-6 pb-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
          <div class="col-span-4">Kandidāts / Vakance</div>
          <div class="col-span-3">Kontakti</div>
          <div class="col-span-3">Pavadzīme</div>
          <div class="col-span-2 text-right">Dokumenti</div>
        </div>

        <div
          v-for="app in applications"
          :key="app.id"
          class="grid grid-cols-12 items-center px-6 py-8 bg-white border border-black/[0.03] hover:border-black transition-all duration-500 group"
        >
          <div class="col-span-4">
            <h3 class="text-lg font-medium tracking-tight group-hover:text-blue-600 transition-colors">
              {{ app.user?.firstname }} {{ app.user?.lastname }}
            </h3>
            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mt-1">
              {{ app.vacancy?.title || "–" }}
            </p>
          </div>

          <div class="col-span-3">
            <p class="text-sm font-medium">{{ app.user?.email || "–" }}</p>
            <p class="text-[10px] text-gray-400 uppercase tracking-tighter mt-1">Sazināties e-pastā</p>
          </div>

          <div class="col-span-3 pr-8">
            <p class="text-xs text-gray-500 line-clamp-2 italic leading-relaxed">
              "{{ app.cover_letter || "Pavadzīme nav pievienota." }}"
            </p>
          </div>

          <div class="col-span-2 text-right">
            <button
              v-if="app.cv_path"
              @click="openCv(app.id)"
              class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest bg-black text-white px-5 py-3 hover:bg-blue-600 transition-colors"
            >
              Atvērt CV
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M7 17L17 7M17 7H8M17 7V16" />
              </svg>
            </button>
            <span v-else class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Nav CV</span>
          </div>
        </div>
      </div>
    </main>

    <div
      v-if="showCvModal"
      class="fixed inset-0 z-[100] bg-white/90 backdrop-blur-md flex items-center justify-center p-6 animate-in fade-in duration-500"
    >
      <div class="bg-white border border-black/[0.05] w-full max-w-6xl h-[90vh] relative shadow-[0_40px_100px_rgba(0,0,0,0.1)] flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-black/[0.05]">
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Kandidāta dokumentācija</span>
          <button
            @click="showCvModal = false"
            class="text-2xl hover:text-blue-600 transition-colors"
          >
            &times;
          </button>
        </div>
        
        <div class="flex-grow bg-gray-50 p-4">
          <iframe
            v-if="pdfUrl"
            :src="pdfUrl"
            class="w-full h-full border-none"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api.js";
import { useToast } from "@/composables/useToast";

const { error } = useToast();

const applications = ref([]);
const showCvModal = ref(false);
const pdfUrl = ref(null);

const openCv = (id) => {
  const app = applications.value.find((a) => a.id === id);
  if (!app || !app.cv_path) {
    error("CV nav atrasts");
    return;
  }
  pdfUrl.value = `http://127.0.0.1:8000/storage/${app.cv_path}`;
  showCvModal.value = true;
};

onMounted(async () => {
  try {
    const res = await api.get("/applications", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    applications.value = res.data;
  } catch (err) {
    console.error("Failed to fetch applications:", err);
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

.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>