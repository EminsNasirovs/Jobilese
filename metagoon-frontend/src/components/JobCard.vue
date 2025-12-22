<template>
  <div
    @click="goToVacancy"
    class="relative cursor-pointer group p-8 bg-white border border-black/[0.05] 
           hover:border-black transition-all duration-700 ease-out overflow-hidden flex flex-col h-full"
  >
    <button
      @click.stop="toggleFavorite"
      class="absolute top-6 right-6 text-lg transition-all duration-500 z-20"
      :class="isFavorited ? 'text-red-500' : 'text-gray-200 hover:text-black'"
    >
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        :fill="isFavorited ? 'currentColor' : 'none'" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-5 h-5"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
      </svg>
    </button>

    <div class="flex flex-col h-full space-y-6">
      <div class="w-16 h-16 bg-gray-50 flex items-center justify-center overflow-hidden grayscale group-hover:grayscale-0 transition-all duration-700">
        <img
          :src="logo ? `http://127.0.0.1:8000/storage/${logo}` : '/default-logo.png'"
          alt="company logo"
          class="w-full h-full object-contain p-2"
        />
      </div>

      <div class="space-y-2">
        <h2 class="text-2xl font-medium tracking-tight leading-tight group-hover:text-blue-600 transition-colors duration-500">
          {{ title }}
        </h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400">
          {{ company }} • {{ county }}
        </p>
      </div>

      <p class="text-sm text-gray-500 leading-relaxed italic line-clamp-2 flex-grow">
        "{{ description }}"
      </p>

      <div class="pt-6 border-t border-black/[0.03] flex justify-between items-end">
        <div class="space-y-3">
          <span class="inline-block text-[9px] font-bold uppercase tracking-[0.15em] px-2 py-1 bg-gray-50 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
            {{ category }}
          </span>
          <div class="space-y-1">
            <p class="text-[9px] uppercase font-bold text-gray-300 tracking-widest">Mēneša alga</p>
            <p class="text-lg font-medium tabular-nums text-[#111111]">
              €{{ salary }}<span class="text-[10px] ml-1 text-gray-400">/ Bruto</span>
            </p>
          </div>
        </div>
        
        <div class="mb-1 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-y-2 group-hover:translate-y-0">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-black">
            <path d="M17 7L7 17M17 7H8M17 7V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import api from "@/services/api";

const props = defineProps({
  id: Number,
  logo: String,
  title: String,
  company: String,
  salary: String,
  description: String,
  category: String,
  county: String,
  isFavorite: { type: Boolean, default: false },
});

const emit = defineEmits(["favoriteChanged"]);
const router = useRouter();
const token = localStorage.getItem("token");
const isFavorited = ref(props.isFavorite);

const toggleFavorite = async () => {
  if (!token) {
    alert("Lūdzu pieslēdzieties, lai pievienotu favorītiem.");
    return;
  }
  try {
    const res = await api.post(`/favorites/${props.id}`, {}, {
      headers: { Authorization: `Bearer ${token}` },
    });
    isFavorited.value = res.data.favorited;
    emit("favoriteChanged", { id: props.id, favorited: isFavorited.value });
  } catch (err) {
    console.error(err);
  }
};

onMounted(() => { isFavorited.value = props.isFavorite; });
watch(() => props.isFavorite, (newVal) => { isFavorited.value = newVal; });

const goToVacancy = () => router.push(`/vacancy/${props.id}`);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

div {
  font-family: 'Inter', sans-serif;
}
</style>