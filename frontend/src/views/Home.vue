<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-[#E0E7FF]">
    
    <main class="max-w-7xl mx-auto px-6 lg:px-12 py-24">
      
      <section class="relative mb-40">
        <div class="space-y-4">
          <div class="flex items-center gap-4 overflow-hidden">
            <span class="h-[1px] w-12 bg-black"></span>
            <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400">Nākamās paaudzes karjera</span>
          </div>
          
          <h1 class="text-7xl md:text-[110px] font-medium tracking-[-0.04em] leading-[0.9] lg:w-3/4">
            Māksla atrast <br />
            <span class="italic font-serif">īsto darbu.</span>
          </h1>
        </div>

        <div class="mt-20 max-w-4xl group">
          <div class="flex flex-col md:flex-row items-end gap-8 border-b border-black/10 pb-4 group-focus-within:border-black transition-colors duration-500">
            <div class="flex-1 w-full">
              <label class="block text-[10px] uppercase tracking-widest font-bold mb-2 text-gray-400">Vakance</label>
              <input 
                v-model="keyword" 
                type="text" 
                placeholder="Produktu dizaineris" 
                class="w-full bg-transparent text-3xl md:text-4xl font-light outline-none placeholder:text-gray-200"
              />
            </div>
            <div class="flex-1 w-full">
              <label class="block text-[10px] uppercase tracking-widest font-bold mb-2 text-gray-400">Pilsēta</label>
              <select v-model="county" class="w-full bg-transparent text-3xl md:text-4xl font-light outline-none appearance-none cursor-pointer">
                <option value="">Visa Latvija</option>
                <option value="Rīga">Rīga</option>
                <option value="Valmiera">Valmiera</option>
                <option value="Liepāja">Liepāja</option>
              </select>
            </div>
            <button 
              @click="handleSearch"
              class="w-full md:w-auto bg-black text-white px-10 py-5 text-sm font-bold uppercase tracking-widest hover:bg-blue-600 transition-all duration-700"
            >
              Meklēt
            </button>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-24 mb-40">
        <div class="space-y-4">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Aktīvas vakances</p>
          <p class="text-6xl font-light tabular-nums leading-none">{{ stats.vacancies }}</p>
          <div class="h-[1px] w-full bg-gray-100"></div>
        </div>
        <div class="space-y-4">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Darba devēji</p>
          <p class="text-6xl font-light tabular-nums leading-none">{{ stats.companies }}</p>
          <div class="h-[1px] w-full bg-gray-100"></div>
        </div>
        <div class="space-y-4">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Kandidāti</p>
          <p class="text-6xl font-light tabular-nums leading-none">{{ stats.unemployed }}</p>
          <div class="h-[1px] w-full bg-gray-100"></div>
        </div>
      </section>

      <section class="grid lg:grid-cols-2 gap-20 items-center mb-40">
  <div class="aspect-[4/5] bg-gray-100 relative overflow-hidden group">
    <img 
      src="/images/homepage.jpg" 
      alt="Moderns birojs" 
      class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
    />
    
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-90"></div>
    
    <div class="absolute bottom-10 left-10 text-white z-10">
      <p class="text-xs font-bold uppercase tracking-[0.2em] mb-2">Atlasītas iespējas</p>
      <h3 class="text-3xl font-serif italic">Kvalitāte pāri kvantitātei.</h3>
    </div>
  </div>
  
  <div class="space-y-12">
    <h2 class="text-5xl font-light tracking-tight leading-tight">Mēs savienojam <span class="font-serif italic">vizionārus uzņēmumus</span> ar Latvijas labākajiem talantiem.</h2>
    <div class="space-y-8 max-w-md">
      <div class="space-y-2">
        <h4 class="font-bold text-sm uppercase">Gudra paziņojumu sistēma</h4>
        <p class="text-gray-500 leading-relaxed italic">Negaidi — saņem informāciju par jaunām vakancēm savā e-pastā reāllaikā.</p>
      </div>
      <div class="space-y-2">
        <h4 class="font-bold text-sm uppercase">Tieša saziņa</h4>
        <p class="text-gray-500 leading-relaxed italic">Mēs noņemam barjeras. Sazinies ar lēmumu pieņēmējiem tieši un bez kavēšanās.</p>
      </div>
      <RouterLink to="/registracija" class="inline-flex items-center gap-4 group mt-4">
        <span class="text-sm font-bold uppercase tracking-widest border-b-2 border-black pb-1">Sākt meklēšanu</span>
        <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="2" /></svg>
      </RouterLink>
    </div>
  </div>
</section>

      <section class="space-y-12">
        <div class="flex justify-between items-end border-b border-black pb-6">
          <h2 class="text-4xl font-light">Nozares</h2>
          <RouterLink to="/vakances" class="text-xs font-bold uppercase tracking-widest hover:text-blue-600 transition-colors">Skatīt visas</RouterLink>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
          <RouterLink 
            v-for="(cat, index) in categories" 
            :key="cat" 
            :to="{ path: '/vakances', query: { category: cat } }" 
            class="py-8 md:px-8 first:pl-0 hover:bg-gray-50 transition-colors group"
          >
            <p class="text-gray-400 text-xs mb-2 group-hover:text-black transition-colors">0{{ index + 1 }}</p>
            <p class="text-2xl font-light tracking-tight">{{ cat }}</p>
          </RouterLink>
        </div>
      </section>
      
    </main>

    <footer class="bg-black text-white py-32 px-6">
      <div class="max-w-7xl mx-auto text-center space-y-12">
        <h2 class="text-6xl md:text-8xl font-light tracking-tighter italic font-serif">Vai esi gatavs izaugsmei?</h2>
        <div class="flex flex-col sm:flex-row justify-center gap-8">
          <RouterLink to="/registracija" class="px-12 py-5 bg-white text-black font-bold uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all duration-500">Reģistrēties</RouterLink>
          <RouterLink to="/login" class="px-12 py-5 border border-white/20 font-bold uppercase tracking-widest hover:border-white transition-all duration-500">Ienākt</RouterLink>
        </div>
        <p class="text-gray-500 text-[10px] uppercase tracking-[0.5em] pt-20">Jobilese &copy; 2025 // Izstrādāts Latvijā</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, RouterLink } from "vue-router";
import api from "@/services/api.js";

const router = useRouter();
const keyword = ref("");
const county = ref("");
const stats = ref({ vacancies: 0, companies: 0, unemployed: 0 });
const categories = ['Tehnoloģijas', 'Mārketings', 'Dizains', 'Finanses'];

const handleSearch = () => {
  router.push({ 
    path: "/vakances", 
    query: { keyword: keyword.value, county: county.value } 
  });
};

const fetchStats = async () => {
  try {
    const res = await api.get("/stats");
    stats.value = res.data;
  } catch (err) {
    console.error("Stats failed to load");
  }
};

onMounted(fetchStats);
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