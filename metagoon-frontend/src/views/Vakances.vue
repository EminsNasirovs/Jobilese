<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-[#E0E7FF]">
    <main class="max-w-7xl mx-auto px-6 lg:px-12 py-20">
      
      <section class="mb-20 space-y-6">
        <h1 class="text-6xl md:text-8xl font-medium tracking-[-0.04em] leading-none">
          Atrodi savu <br />
          <span class="italic font-serif">nākamo soli.</span>
        </h1>
      </section>

      <section class="mb-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 border-b border-black/10 pb-8 group-focus-within:border-black transition-colors duration-500">
          <div class="space-y-2">
            <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400">Meklēt</label>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Amata nosaukums..." 
              class="w-full bg-transparent text-xl font-light outline-none placeholder:text-gray-200"
            />
          </div>
          
          <div class="space-y-2">
            <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400">Kategorija</label>
            <select v-model="selectedCategory" class="w-full bg-transparent text-xl font-light outline-none appearance-none cursor-pointer">
              <option value="">Visas nozares</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-400">Atrašanās vieta</label>
            <select v-model="selectedCounty" class="w-full bg-transparent text-xl font-light outline-none appearance-none cursor-pointer">
              <option value="">Visa Latvija</option>
              <option v-for="place in locations" :key="place" :value="place">{{ place }}</option>
            </select>
          </div>

          <div class="flex items-end justify-end">
            <button 
              @click="clearFilters"
              class="text-[10px] uppercase tracking-[0.2em] font-bold text-gray-400 hover:text-black transition-colors underline underline-offset-8"
            >
              Notīrīt filtrus
            </button>
          </div>
        </div>
      </section>

      <div v-if="user && user.role === 'uzņēmējs'" class="flex justify-end mb-12">
        <button
          @click="showModal = true"
          class="bg-black text-white px-8 py-4 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all duration-500"
        >
          + Izveidot vakanci
        </button>
      </div>

      <section>
        <div v-if="filteredJobs.length" class="grid gap-12 md:grid-cols-2 lg:grid-cols-3">
          <JobCard
            v-for="job in filteredJobs"
            :key="job.id"
            v-bind="job"
            :currentUserId="user?.id"
            :role="user?.role"
            :isFavorite="favorites.includes(job.id)"
            @favoriteChanged="onFavoriteChanged"
            class="hover:-translate-y-2 transition-transform duration-500"
          />
        </div>
        
        <div v-else class="py-40 text-center space-y-4">
          <p class="text-4xl font-serif italic text-gray-200">Nekas netika atrasts.</p>
          <p class="text-xs uppercase tracking-widest text-gray-400">Mēģiniet mainīt meklēšanas kritērijus.</p>
        </div>
      </section>
    </main>

    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-sm bg-black/10">
      <div class="bg-white w-full max-w-2xl p-12 shadow-[0_30px_100px_rgba(0,0,0,0.1)] space-y-8 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-start">
          <h2 class="text-4xl font-light tracking-tight">Jauna <span class="font-serif italic">vakance</span></h2>
          <button @click="showModal = false" class="text-gray-300 hover:text-black transition-colors text-2xl">&times;</button>
        </div>

        <form @submit.prevent="createVacancy" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2 border-b border-black/10 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Nosaukums</label>
              <input v-model="newVacancy.title" type="text" required class="w-full bg-transparent outline-none font-medium" />
            </div>
            <div class="space-y-2 border-b border-black/10 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Alga</label>
              <div class="flex gap-2">
                <input v-model.number="newVacancy.salary" type="number" required class="w-full bg-transparent outline-none font-medium" />
                <select v-model="newVacancy.salary_type" class="bg-transparent outline-none text-[10px] font-bold uppercase">
                  <option value="Brutto">Brutto</option>
                  <option value="Neto">Neto</option>
                </select>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2 border-b border-black/10 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Kategorija</label>
              <select v-model="newVacancy.category" required class="w-full bg-transparent outline-none appearance-none">
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>
            <div class="space-y-2 border-b border-black/10 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Pilsēta</label>
              <select v-model="newVacancy.county" required class="w-full bg-transparent outline-none appearance-none">
                <option v-for="place in locations" :key="place" :value="place">{{ place }}</option>
              </select>
            </div>
          </div>

          <div class="space-y-2 border-b border-black/10 pb-2">
            <label class="text-[10px] uppercase font-bold text-gray-400">Apraksts</label>
            <textarea v-model="newVacancy.description" required class="w-full bg-transparent outline-none min-h-[100px] resize-none"></textarea>
          </div>

          <div class="flex items-center gap-6">
            <div class="flex-1 border-2 border-dashed border-gray-100 rounded-lg p-4 text-center group hover:border-blue-600 transition-colors cursor-pointer relative">
              <input type="file" accept="image/*" @change="handleFileChange" class="absolute inset-0 opacity-0 cursor-pointer" />
              <p v-if="!logoPreview" class="text-[10px] uppercase font-bold text-gray-400 group-hover:text-blue-600 transition-colors">Pievienot Logo</p>
              <img v-else :src="logoPreview" class="h-12 mx-auto object-contain" />
            </div>
            <button type="submit" class="flex-1 bg-black text-white h-16 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all">
              Publicēt vakanci
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, computed, onMounted, watch } from "vue";
import api from "@/services/api";
import JobCard from "@/components/JobCard.vue";

const jobs = ref([]);
const user = ref(null);
const showModal = ref(false);
const logoPreview = ref(null);
const error = ref(null);
const route = useRoute();
const router = useRouter();
const favorites = ref([]);

const categories = ["IT & Programmēšana", "Pārdošana", "Mārketings", "Finanses", "Loģistika", "Ražošana", "Klientu atbalsts", "Veselība"];
const locations = ["Rīga", "Liepāja", "Jelgava", "Daugavpils", "Valmiera", "Ogre", "Ventspils", "Rēzekne"];

const newVacancy = ref({
  title: "",
  salary: 0, 
  salary_type: "Brutto", 
  description: "",
  category: "",
  county: "",
  logo: null,
});

const searchQuery = ref(route.query.keyword || "");
const selectedCounty = ref(route.query.county || "");
const selectedCategory = ref(route.query.category || "");

watch([searchQuery, selectedCounty, selectedCategory], ([keyword, county, category]) => {
  router.replace({ query: { keyword, county, category } });
});

const clearFilters = () => {
  searchQuery.value = "";
  selectedCounty.value = "";
  selectedCategory.value = "";
};

const filteredJobs = computed(() => {
  return jobs.value.filter((job) => {
    const matchCategory = selectedCategory.value ? job.category === selectedCategory.value : true;
    const matchCounty = selectedCounty.value ? job.county === selectedCounty.value : true;
    const matchSearch = searchQuery.value ? job.title.toLowerCase().includes(searchQuery.value.toLowerCase()) : true;
    return matchCategory && matchCounty && matchSearch;
  });
});

const onFavoriteChanged = ({ id, favorited }) => {
  if (favorited && !favorites.value.includes(id)) {
    favorites.value.push(id);
  } else {
    favorites.value = favorites.value.filter((favId) => favId !== id);
  }
};

const fetchJobs = async () => {
  try {
    const res = await api.get("/vacancies");
    jobs.value = res.data;
  } catch (err) { console.error(err); }
};

const fetchUser = async () => {
  try {
    const res = await api.get("/user");
    user.value = res.data;
    const favRes = await api.get("/favorites");
    favorites.value = favRes.data.map((f) => f.job_vacancy_id);
  } catch { 
    user.value = null; 
    favorites.value = [];
  }
};

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  newVacancy.value.logo = file;
  const reader = new FileReader();
  reader.onload = (event) => (logoPreview.value = event.target.result);
  reader.readAsDataURL(file);
};

const createVacancy = async () => {
  try {
    let logoPath = null;
    if (newVacancy.value.logo) {
      const formData = new FormData();
      formData.append("file", newVacancy.value.logo);
      const uploadRes = await api.post("/upload", formData);
      logoPath = uploadRes.data.path;
    }
    
    await api.post("/vacancies", {
        ...newVacancy.value,
        salary: newVacancy.value.salary.toString(),
        logo: logoPath,
    });
    
    showModal.value = false;
    fetchJobs();
  } catch (err) {
    error.value = "Neizdevās izveidot vakanci";
  }
};

onMounted(() => {
  fetchUser();
  fetchJobs();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

select {
  background-image: none;
}
</style>