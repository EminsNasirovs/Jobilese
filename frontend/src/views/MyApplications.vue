<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans">
    <main class="max-w-7xl mx-auto px-6 lg:px-12 py-20">

      <section class="mb-20 space-y-6">
        <div class="flex items-center gap-4">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Mans profils</span>
        </div>
        <h1 class="text-6xl md:text-8xl font-medium tracking-tight leading-none uppercase italic font-serif">
          Mani <br />
          <span class="text-blue-600">Pieteikumi.</span>
        </h1>
      </section>

      <div v-if="applications.length === 0" class="py-40 text-center border-t border-black/[0.05]">
        <p class="text-4xl font-serif italic text-gray-200">Vēl neesat pieteicies.</p>
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mt-4">Atrodiet vakanci un pieteicieties!</p>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="app in applications"
          :key="app.id"
          class="bg-white border border-black/[0.03] p-8 transition-all duration-500 hover:border-black/10"
        >
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <!-- Vacancy info -->
            <div class="flex items-start gap-6">
              <img
                v-if="app.vacancy?.logo_url"
                :src="app.vacancy.logo_url"
                alt="logo"
                class="w-14 h-14 object-contain border border-black/[0.05] p-2"
              />
              <div class="space-y-1">
                <h3 class="text-xl font-medium tracking-tight">{{ app.vacancy?.title || "–" }}</h3>
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
                  {{ app.vacancy?.company }} • {{ app.vacancy?.county }}
                </p>
                <p class="text-[10px] text-gray-400 mt-1">
                  Pieteikts: {{ formatDate(app.created_at) }}
                </p>
              </div>
            </div>

            <!-- Status -->
            <span
              class="shrink-0 text-[10px] font-bold uppercase tracking-widest px-5 py-2 rounded-full self-start"
              :class="{
                'bg-yellow-50 text-yellow-600': app.status === 'pending',
                'bg-green-50 text-green-600': app.status === 'accepted',
                'bg-red-50 text-red-500': app.status === 'denied',
              }"
            >
              {{ app.status === 'pending' ? 'Izskatīšanā' : app.status === 'accepted' ? 'Pieņemts' : 'Noraidīts' }}
            </span>
          </div>

          <!-- Cover letter preview -->
          <p v-if="app.cover_letter" class="mt-4 text-sm text-gray-500 italic leading-relaxed border-l-2 border-gray-100 pl-4 line-clamp-2">
            "{{ app.cover_letter }}"
          </p>

          <!-- Employer response -->
          <div
            v-if="app.employer_response"
            class="mt-6 p-5 border-l-4 text-sm leading-relaxed"
            :class="{
              'border-green-500 bg-green-50 text-green-800': app.status === 'accepted',
              'border-red-400 bg-red-50 text-red-800': app.status === 'denied',
            }"
          >
            <p class="text-[10px] uppercase font-bold tracking-widest mb-2 opacity-60">Darba devēja atbilde</p>
            <p>{{ app.employer_response }}</p>
          </div>

          <div v-if="app.status !== 'pending' && !app.employer_response" class="mt-4 text-[10px] uppercase font-bold tracking-widest text-gray-300">
            Atbilde vēl nav sniegta.
          </div>

          <!-- Chat button -->
          <div class="mt-6 pt-4 border-t border-black/[0.04]">
            <button
              @click="startChat(app)"
              class="px-8 py-3 border border-black text-[10px] font-bold uppercase tracking-widest hover:bg-black hover:text-white transition-colors"
            >
              Rakstīt darba devējam
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/services/api.js";
import { useToast } from "@/composables/useToast";

const applications = ref([]);
const router = useRouter();
const { error } = useToast();

const startChat = async (app) => {
  try {
    await api.post(
      '/chat/start',
      { other_user_id: app.vacancy.user_id, vacancy_id: app.vacancy_id },
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );
    router.push('/chat');
  } catch (e) {
    error('Neizdevās sākt sarunu');
  }
};

const formatDate = (d) =>
  new Date(d).toLocaleDateString("lv-LV", { day: "2-digit", month: "2-digit", year: "numeric" });

onMounted(async () => {
  try {
    const res = await api.get("/my-applications", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    applications.value = res.data;
  } catch (err) {
    console.error("Failed to fetch my applications:", err);
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;500;700&display=swap');
.font-serif { font-family: 'DM Serif Display', serif; }
main { font-family: 'Inter', sans-serif; }
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
