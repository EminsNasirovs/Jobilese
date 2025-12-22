<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <main class="max-w-7xl mx-auto px-6 lg:px-12 py-20">
      
      <section class="mb-20 space-y-6">
        <div class="flex items-center gap-4 overflow-hidden">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Privātā kolekcija</span>
        </div>
        <h1 class="text-6xl md:text-8xl font-medium tracking-tight leading-none uppercase italic font-serif">
          Mani <br />
          <span class="text-blue-600">Favorīti.</span>
        </h1>
        <p class="text-gray-400 text-sm tracking-widest uppercase font-bold max-w-xl">
          Tava personīgā izlase ar iespējām, kas piesaistīja uzmanību.
        </p>
      </section>

      <section v-if="favorites.length > 0">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-3 border-t border-black/[0.05] pt-16">
          <div
            v-for="fav in favorites"
            :key="fav.id"
            class="relative group"
          >
            <div class="absolute -top-4 left-0 opacity-0 group-hover:opacity-100 transition-opacity">
              <span class="text-[9px] font-bold uppercase tracking-widest text-red-400">Saglabāts</span>
            </div>

            <JobCard
              :id="fav.vacancy.id"
              :logo="fav.vacancy.logo"
              :title="fav.vacancy.title"
              :company="fav.vacancy.company"
              :description="fav.vacancy.description"
              :salary="fav.vacancy.salary"
              :category="fav.vacancy.category"
              :county="fav.vacancy.county"
              :isFavorite="true"
              @favoriteChanged="handleRemove(fav.vacancy.id)"
              class="hover:-translate-y-2 transition-transform duration-700"
            />
          </div>
        </div>
      </section>

      <section v-else class="text-center py-40 border-t border-black/[0.05]">
        <h2 class="text-4xl font-serif italic text-gray-200">
          Tukšs arhīvs.
        </h2>
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mt-6 mb-10">
          Tu vēl neesi atzīmējis nevienu vakanci kā sev saistošu.
        </p>
        <RouterLink
          to="/vacancies"
          class="inline-block bg-black text-white px-10 py-5 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-blue-600 transition-all duration-500 shadow-xl"
        >
          Pārlūkot vakances
        </RouterLink>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import api from "@/services/api";
import JobCard from "@/components/JobCard.vue";

const token = localStorage.getItem("token");
const favorites = ref([]);

const fetchFavorites = async () => {
  if (!token) return;
  try {
    const res = await api.get("/favorites", {
      headers: { Authorization: `Bearer ${token}` },
    });
    // Pārliecināmies, ka dati tiek paņemti pareizi atkarībā no API struktūras
    favorites.value = res.data;
  } catch (err) {
    console.error("Failed to load favorites:", err);
  }
};

const handleRemove = (vacancyId) => {
  // Kad JobCard noziņo par izmaiņām, mēs vienkārši izfiltrējam to no saraksta
  favorites.value = favorites.value.filter(
    (f) => f.vacancy.id !== vacancyId
  );
};

onMounted(fetchFavorites);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;500;700&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

main {
  font-family: 'Inter', sans-serif;
}

/* Pievienojam nelielu animāciju saraksta ielādei */
section {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>