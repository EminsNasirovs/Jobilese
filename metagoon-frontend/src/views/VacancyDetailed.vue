<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111]">
    <section v-if="vacancy" class="relative overflow-hidden border-b border-black/[0.05] bg-gray-50/50">
      <div class="max-w-7xl mx-auto px-6 py-24 lg:px-12">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
          
          <div class="relative group w-full lg:w-1/3">
            <div class="aspect-square bg-white shadow-[20px_20px_60px_rgba(0,0,0,0.05)] border border-black/[0.03] flex items-center justify-center p-12 transition-transform duration-700 group-hover:scale-[1.02]">
              <img
                :src="vacancy.logo_url || '/default-logo.png'"
                alt="Company Logo"
                class="w-full h-full object-contain"
              />
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-blue-600/5 -z-10 rounded-full blur-3xl"></div>
          </div>

          <div class="flex-1 space-y-8">
            <div class="inline-flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
              <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
              <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600">{{ vacancy.category }}</span>
            </div>
            
            <h1 class="text-6xl md:text-8xl font-bold tracking-tight leading-[0.85] uppercase italic font-serif">
              {{ vacancy.title }}<span class="text-blue-600">.</span>
            </h1>

            <div class="flex flex-wrap gap-8 items-center pt-4">
              <div class="space-y-1">
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em]">Uzņēmums</p>
                <p class="text-2xl font-medium tracking-tight">{{ vacancy.company }}</p>
              </div>
              <div class="w-[1px] h-10 bg-black/10 hidden md:block"></div>
              <div class="space-y-1">
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em]">Atrašanās vieta</p>
                <p class="text-2xl font-medium tracking-tight">{{ vacancy.county }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <main v-if="vacancy" class="max-w-7xl mx-auto px-6 lg:px-12 py-20">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-20">
        
        <div class="lg:col-span-8 space-y-12">
          <div class="space-y-6">
            <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-gray-300">Par pozīciju</h3>
            <div class="text-2xl font-light leading-relaxed text-gray-600 first-letter:text-6xl first-letter:font-serif first-letter:mr-3 first-letter:float-left first-letter:text-black">
              {{ vacancy.description }}
            </div>
          </div>

          <div v-if="isLoggedIn && role !== 'uzņēmējs'" class="bg-black p-12 text-white flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="space-y-2 text-center md:text-left">
              <h3 class="text-3xl font-medium tracking-tight">Gatavs jaunām iespējām?</h3>
              <p class="text-gray-400 text-sm tracking-wide">Pievieno savu CV un kļūsti par daļu no komandas.</p>
            </div>
            <button
              @click="showApplicationModal = true"
              class="whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white px-12 py-5 text-xs font-bold uppercase tracking-[0.2em] transition-all duration-500 shadow-[0_20px_40px_rgba(37,99,235,0.3)]"
            >
              Pieteikties tagad
            </button>
          </div>
        </div>

        <div class="lg:col-span-4 space-y-12">
          <div class="p-10 border-2 border-black bg-white shadow-[10px_10px_0px_#2563eb]">
            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2">Piedāvātais atalgojums</p>
            <div class="flex items-baseline gap-2">
              <span class="text-5xl font-bold tracking-tighter">€{{ vacancy.salary }}</span>
              <span class="text-sm font-bold text-blue-600 uppercase">{{ vacancy.salary_type }}</span>
            </div>
            <div class="mt-8 space-y-4">
               <button @click="toggleFavorite" class="w-full py-4 border border-black font-bold uppercase text-[10px] tracking-widest hover:bg-gray-50 transition-colors">
                 {{ isFavorited ? '❤️ Saglabāts' : '🤍 Saglabāt favorītos' }}
               </button>
               <template v-if="user && (vacancy.user_id === user.id || role === 'admin')">
                <div class="grid grid-cols-2 gap-2">
                  <button @click="showEditModal = true" class="py-3 bg-gray-100 font-bold uppercase text-[9px] tracking-widest hover:bg-black hover:text-white transition-all">Rediģēt</button>
                  <button @click="deleteVacancy" class="py-3 bg-red-50 text-red-500 font-bold uppercase text-[9px] tracking-widest hover:bg-red-500 hover:text-white transition-all">Dzēst</button>
                </div>
               </template>
            </div>
          </div>

          <div class="space-y-6">
            <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-gray-300">Diskusija</h3>
            <div class="space-y-8 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
              <div v-for="comment in comments" :key="comment.id" class="border-l-2 border-blue-600 pl-6 py-2">
                <p class="text-sm font-medium leading-relaxed">{{ comment.comment_text }}</p>
                <p class="text-[10px] uppercase font-bold text-gray-400 mt-2 tracking-tighter">
                  {{ comment.user?.username }} • {{ formatDate(comment.created_at) }}
                </p>
              </div>
            </div>
            
            <div v-if="isLoggedIn" class="pt-4">
              <input 
                v-model="newComment.comment_text"
                @keyup.enter="addComment"
                type="text" 
                placeholder="Raksti ziņu..." 
                class="w-full bg-transparent border-b-2 border-gray-100 py-3 outline-none focus:border-blue-600 transition-colors text-sm"
              />
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;700;900&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

h1, h2, h3, button {
  font-family: 'Inter', sans-serif;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #eee;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #2563eb;
}
</style>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();

const vacancy = ref(null);
const user = ref(null);
const role = localStorage.getItem("role");
const token = localStorage.getItem("token");
const isLoggedIn = !!token;

const isFavorited = ref(false);
const comments = ref([]);
const newComment = ref({ comment_text: "", vacancy_id: route.params.id });

const showApplicationModal = ref(false);
const showEditModal = ref(false);
const coverLetter = ref("");
const cvFile = ref(null);

const editVacancyData = ref({
  title: "",
  salary: "",
  salary_type: "Brutto",
  description: "",
  category: "",
  county: "",
});

// ====== FETCHING ======
const fetchUser = async () => {
  if (!token) return;
  try {
    const res = await api.get("/user", {
      headers: { Authorization: `Bearer ${token}` },
    });
    user.value = res.data;
  } catch (err) {
    console.error("Failed to fetch user:", err);
  }
};

const fetchVacancy = async () => {
  try {
    const res = await api.get(`/vacancies/${route.params.id}`);
    vacancy.value = res.data.data || res.data;
  } catch (err) {
    console.error("Failed to fetch vacancy:", err);
  }
};
const fetchComments = async () => {
  try {
    const res = await api.get(`/vacancies/${route.params.id}/comments`);
    comments.value = res.data.data || res.data;
  } catch (err) {
    console.error("Failed to fetch comments:", err);
  }
};

const addComment = async () => {
  if (!newComment.value.comment_text.trim()) return alert("Lūdzu, ieraksti komentāru!");
  try {
    const res = await api.post(
      `/comments`,
      { ...newComment.value },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    comments.value.unshift(res.data.data || res.data);
    newComment.value.comment_text = "";
  } catch (err) {
    console.error("Add comment error:", err);
    alert("❌ Neizdevās pievienot komentāru");
  }
};

const deleteComment = async (id) => {
  if (!confirm("Vai tiešām vēlies dzēst šo komentāru?")) return;
  try {
    await api.delete(`/comments/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    comments.value = comments.value.filter((c) => c.id !== id);
  } catch (err) {
    console.error("Delete comment error:", err);
    alert("❌ Neizdevās dzēst komentāru");
  }
};
// ====== EDIT & DELETE ======
const prepareEditData = () => {
  if (!vacancy.value) return;
  editVacancyData.value = {
    title: vacancy.value.title,
    salary: vacancy.value.salary,
    salary_type: vacancy.value.salary_type || "Brutto",
    description: vacancy.value.description,
    category: vacancy.value.category,
    county: vacancy.value.county,
  };
};

const updateVacancy = async () => {
  try {
    const res = await api.put(
      `/vacancies/${vacancy.value.id}`,
      { ...editVacancyData.value },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    vacancy.value = res.data.data;
    showEditModal.value = false;
    alert("✅ Vakance veiksmīgi atjaunota!");
  } catch (err) {
    console.error("Update error:", err);
    alert(err.response?.data?.message || "❌ Neizdevās atjaunot vakanci");
  }
};

const deleteVacancy = async () => {
  if (!confirm("Vai tiešām vēlies dzēst šo vakanci?")) return;
  try {
    await api.delete(`/vacancies/${vacancy.value.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    alert("Vakance dzēsta!");
    router.push("/vacancies");
  } catch (err) {
    console.error("Delete error:", err);
    alert("❌ Neizdevās dzēst vakanci");
  }
};

// ====== PLACEHOLDER METHODS ======
const toggleFavorite = () => {};

const formatDate = (date) => new Date(date).toLocaleString();
const handleFile = (e) => (cvFile.value = e.target.files[0]);
const submitApplication = async () => {
  if (!coverLetter.value.trim() && !cvFile.value) {
    alert("Lūdzu pievienojiet motivācijas vēstuli vai CV!");
    return;
  }

  const formData = new FormData();
  formData.append("cover_letter", coverLetter.value);
  if (cvFile.value) formData.append("cv", cvFile.value);

  try {
    const res = await api.post(
      `/vacancies/${vacancy.value.id}/apply`,
      formData,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      }
    );

    alert("✅ Pieteikums veiksmīgi nosūtīts!");
    showApplicationModal.value = false;
    coverLetter.value = "";
    cvFile.value = null;
  } catch (err) {
    console.error("Application error:", err);
    alert(
      err.response?.data?.message ||
        "❌ Neizdevās nosūtīt pieteikumu. Pārbaudi datus!"
    );
  }
};

const fetchFavorites = () => {};

onMounted(async () => {
  await fetchUser();
  await fetchVacancy();
  await fetchComments();
  await fetchFavorites();
});
</script>


