<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111]">
    <section v-if="vacancy" class="relative overflow-hidden border-b border-black/[0.05] bg-gray-50/50">
      <div class="max-w-7xl mx-auto px-6 py-24 lg:px-12">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
          
          <div class="relative group w-full lg:w-1/3">
            <div class="aspect-square bg-white shadow-[20px_20px_60px_rgba(0,0,0,0.05)] border border-black/[0.03] flex items-center justify-center p-12 transition-transform duration-700 group-hover:scale-[1.02]">
              <img
                :src="vacancy.logo_url || '/default-logo.svg'"
                @error="$event.target.src='/default-logo.svg'"
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
                  <button @click="prepareEditData(); showEditModal = true" class="py-3 bg-gray-100 font-bold uppercase text-[9px] tracking-widest hover:bg-black hover:text-white transition-all">Rediģēt</button>
                  <button @click="deleteVacancy" class="py-3 bg-red-50 text-red-500 font-bold uppercase text-[9px] tracking-widest hover:bg-red-500 hover:text-white transition-all">Dzēst</button>
                </div>
               </template>
            </div>
          </div>

          <div class="space-y-6">
            <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-gray-300">Diskusija</h3>
            <div class="space-y-8 max-h-[500px] overflow-y-auto pr-4 custom-scrollbar">
              <div v-for="comment in comments" :key="comment.id" class="border-l-2 border-blue-600 pl-6 py-2">
                <p class="text-sm font-medium leading-relaxed">{{ comment.comment_text }}</p>
                <div class="flex items-center gap-4 mt-2">
                  <p class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter">
                    {{ comment.user?.username }} • {{ formatDate(comment.created_at) }}
                  </p>
                  <button
                    v-if="isLoggedIn"
                    @click="startReply(comment)"
                    class="text-[9px] uppercase font-bold tracking-widest text-blue-600 hover:text-black transition-colors"
                  >
                    Atbildēt
                  </button>
                  <button
                    v-if="user && comment.user?.id === user.id"
                    @click="deleteComment(comment.id)"
                    class="text-[9px] uppercase font-bold tracking-widest text-red-400 hover:text-red-600 transition-colors"
                  >
                    Dzēst
                  </button>
                </div>

                <!-- Replies -->
                <div v-if="comment.replies && comment.replies.length" class="mt-4 space-y-4 pl-4 border-l border-gray-100">
                  <div v-for="reply in comment.replies" :key="reply.id" class="py-1">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ reply.comment_text }}</p>
                    <div class="flex items-center gap-4 mt-1">
                      <p class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter">
                        {{ reply.user?.username }} • {{ formatDate(reply.created_at) }}
                      </p>
                      <button
                        v-if="user && reply.user?.id === user.id"
                        @click="deleteComment(reply.id)"
                        class="text-[9px] uppercase font-bold tracking-widest text-red-400 hover:text-red-600 transition-colors"
                      >
                        Dzēst
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Inline reply form -->
                <div v-if="replyingTo === comment.id" class="mt-3 flex items-center gap-2">
                  <input
                    v-model="replyText"
                    @keyup.enter="submitReply(comment.id)"
                    type="text"
                    :placeholder="`Atbildēt uz ${comment.user?.username}...`"
                    class="flex-1 bg-transparent border-b border-gray-200 py-2 outline-none focus:border-blue-600 transition-colors text-sm"
                    ref="replyInput"
                  />
                  <button @click="submitReply(comment.id)" class="text-[9px] uppercase font-bold tracking-widest text-blue-600 hover:text-black transition-colors">Sūtīt</button>
                  <button @click="replyingTo = null" class="text-[9px] uppercase font-bold tracking-widest text-gray-400 hover:text-black transition-colors">Atcelt</button>
                </div>
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

    <!-- ===== APPLICATION MODAL ===== -->
    <div
      v-if="showApplicationModal"
      class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-sm bg-black/10"
    >
      <div class="bg-white w-full max-w-xl p-12 shadow-[0_30px_100px_rgba(0,0,0,0.1)] space-y-8 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-1">Pieteikties uz</p>
            <h2 class="text-3xl font-light tracking-tight">{{ vacancy?.title }}</h2>
          </div>
          <button @click="showApplicationModal = false" class="text-gray-300 hover:text-black transition-colors text-2xl">&times;</button>
        </div>

        <div class="space-y-6">
          <div class="space-y-2">
            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Motivācijas vēstule</label>
            <textarea
              v-model="coverLetter"
              rows="5"
              placeholder="Pastāsti par sevi un kāpēc esi piemērots šim amatam..."
              class="w-full bg-transparent border-b border-gray-200 py-3 outline-none focus:border-black transition-colors text-sm resize-none"
            ></textarea>
          </div>

          <div class="space-y-3">
            <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">CV</label>

            <!-- CV source switcher -->
            <div class="flex gap-2 text-[10px] uppercase font-bold tracking-widest">
              <button
                type="button"
                @click="cvSource = 'saved'"
                class="px-4 py-2 border transition-colors"
                :class="cvSource === 'saved' ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-black'"
              >Saglabāts CV</button>
              <button
                type="button"
                @click="cvSource = 'upload'"
                class="px-4 py-2 border transition-colors"
                :class="cvSource === 'upload' ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-black'"
              >Augšupielādēt PDF</button>
              <button
                type="button"
                @click="cvSource = 'none'"
                class="px-4 py-2 border transition-colors"
                :class="cvSource === 'none' ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-black'"
              >Bez CV</button>
            </div>

            <!-- Saved CV picker -->
            <div v-if="cvSource === 'saved'" class="space-y-2">
              <select v-if="savedCvs.length" v-model.number="selectedSavedCvId"
                      class="w-full bg-transparent border border-gray-200 p-3 text-sm outline-none focus:border-black">
                <option v-for="cv in savedCvs" :key="cv.id" :value="cv.id">
                  {{ cv.title }}{{ cv.is_default ? ' ★' : '' }}
                </option>
              </select>
              <p v-else class="text-xs text-gray-400 italic">
                Vēl nav saglabāta CV. <RouterLink to="/cv" class="text-blue-600 underline">Izveidojiet to CV redaktorā</RouterLink>.
              </p>
            </div>

            <!-- Upload PDF -->
            <div v-else-if="cvSource === 'upload'" class="border-2 border-dashed border-gray-100 p-6 text-center hover:border-blue-600 transition-colors cursor-pointer relative">
              <input type="file" accept=".pdf" @change="handleFile" class="absolute inset-0 opacity-0 cursor-pointer" />
              <p class="text-[10px] uppercase font-bold text-gray-400">
                {{ cvFile ? cvFile.name : 'Pievienot CV failu' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Hidden CV render target for PDF export -->
        <div v-if="cvSource === 'saved' && selectedSavedCvData" class="cv-offscreen">
          <CvDocument :cv-data="selectedSavedCvData" :profile="user || {}" paper-id="apply-cv-paper" />
        </div>

        <div class="flex gap-4 pt-2">
          <button
            @click="submitApplication"
            class="flex-1 bg-black text-white py-5 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all"
          >
            Nosūtīt pieteikumu
          </button>
          <button
            @click="showApplicationModal = false"
            class="px-8 border border-gray-200 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 transition-all"
          >
            Atcelt
          </button>
        </div>
      </div>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-sm bg-black/10"
    >
      <div class="bg-white w-full max-w-xl p-12 shadow-[0_30px_100px_rgba(0,0,0,0.1)] space-y-8 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-start">
          <h2 class="text-3xl font-light tracking-tight">Rediģēt <span class="font-serif italic">vakanci</span></h2>
          <button @click="showEditModal = false" class="text-gray-300 hover:text-black transition-colors text-2xl">&times;</button>
        </div>

        <form @submit.prevent="updateVacancy" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-1 border-b border-gray-100 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Nosaukums</label>
              <input v-model="editVacancyData.title" required class="w-full bg-transparent outline-none font-medium" />
            </div>
            <div class="space-y-1 border-b border-gray-100 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Alga</label>
              <div class="flex gap-2">
                <input v-model="editVacancyData.salary" required class="w-full bg-transparent outline-none font-medium" />
                <select v-model="editVacancyData.salary_type" class="bg-transparent outline-none text-[10px] font-bold uppercase">
                  <option value="Brutto">Brutto</option>
                  <option value="Neto">Neto</option>
                </select>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-1 border-b border-gray-100 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Kategorija</label>
              <input v-model="editVacancyData.category" required class="w-full bg-transparent outline-none font-medium" />
            </div>
            <div class="space-y-1 border-b border-gray-100 pb-2">
              <label class="text-[10px] uppercase font-bold text-gray-400">Pilsēta</label>
              <input v-model="editVacancyData.county" required class="w-full bg-transparent outline-none font-medium" />
            </div>
          </div>
          <div class="space-y-1 border-b border-gray-100 pb-2">
            <label class="text-[10px] uppercase font-bold text-gray-400">Apraksts</label>
            <textarea v-model="editVacancyData.description" required rows="4" class="w-full bg-transparent outline-none resize-none text-sm"></textarea>
          </div>
          <div class="flex gap-4 pt-2">
            <button type="submit" class="flex-1 bg-black text-white py-5 text-xs font-bold uppercase tracking-widest hover:bg-blue-600 transition-all">
              Saglabāt
            </button>
            <button type="button" @click="showEditModal = false" class="px-8 border border-gray-200 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 transition-all">
              Atcelt
            </button>
          </div>
        </form>
      </div>
    </div>
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

/* Off-screen render slot for converting saved CV to PDF */
.cv-offscreen {
  position: fixed;
  left: -10000px;
  top: 0;
  pointer-events: none;
  opacity: 0;
}
</style>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";
import { toPng } from 'html-to-image';
import { jsPDF } from 'jspdf';
import api from "@/services/api";
import { useToast } from "@/composables/useToast";
import CvDocument from "@/components/CvDocument.vue";

const { success, error, info } = useToast();

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
const replyingTo = ref(null);
const replyText = ref("");

const showApplicationModal = ref(false);
const showEditModal = ref(false);
const coverLetter = ref("");
const cvFile = ref(null);
const cvSource = ref('saved'); // 'saved' | 'upload' | 'none'
const savedCvs = ref([]);
const selectedSavedCvId = ref(null);
const selectedSavedCvData = computed(() =>
  savedCvs.value.find(c => c.id === selectedSavedCvId.value) || null
);

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
  if (!newComment.value.comment_text.trim()) return info("Lūdzu, ieraksti komentāru!");
  try {
    const res = await api.post(
      `/comments`,
      { ...newComment.value },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    comments.value.unshift(res.data.data || res.data);
    newComment.value.comment_text = "";
  } catch (err) {
    error("Neizdevās pievienot komentāru");
  }
};

const deleteComment = async (id) => {
  if (!confirm("Vai tiešām vēlies dzēst šo komentāru?")) return;
  try {
    await api.delete(`/comments/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    // Remove from top-level or from replies
    comments.value = comments.value
      .filter((c) => c.id !== id)
      .map((c) => ({
        ...c,
        replies: c.replies ? c.replies.filter((r) => r.id !== id) : [],
      }));
  } catch (err) {
    error("Neizdevās dzēst komentāru");
  }
};

const startReply = (comment) => {
  replyingTo.value = comment.id;
  replyText.value = "";
};

const submitReply = async (parentId) => {
  if (!replyText.value.trim()) return;
  try {
    const res = await api.post(
      `/comments`,
      { comment_text: replyText.value, vacancy_id: Number(route.params.id), parent_id: parentId },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    const reply = res.data.data || res.data;
    const parent = comments.value.find((c) => c.id === parentId);
    if (parent) {
      if (!parent.replies) parent.replies = [];
      parent.replies.push(reply);
    }
    replyingTo.value = null;
    replyText.value = "";
  } catch (err) {
    error("Neizdevās pievienot atbildi");
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
    success("Vakance veiksmīgi atjaunota!");
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās atjaunot vakanci");
  }
};

const deleteVacancy = async () => {
  if (!confirm("Vai tiešām vēlies dzēst šo vakanci?")) return;
  try {
    await api.delete(`/vacancies/${vacancy.value.id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    success("Vakance dzēsta!");
    router.push("/vakances");
  } catch (err) {
    error("Neizdevās dzēst vakanci");
  }
};

// ====== FAVORITES ======
const toggleFavorite = async () => {
  if (!isLoggedIn) return info("Lūdzu, piesakies, lai saglabātu favorītos!");
  try {
    const res = await api.post(`/favorites/${vacancy.value.id}`);
    isFavorited.value = res.data.favorited;
  } catch (err) {
    error("Neizdevās mainīt favorītu statusu");
  }
};

const formatDate = (date) => new Date(date).toLocaleString();
const handleFile = (e) => (cvFile.value = e.target.files[0]);

const fetchSavedCvs = async () => {
  if (!token) return;
  try {
    const res = await api.get("/cv");
    savedCvs.value = res.data || [];
    if (savedCvs.value.length) {
      const def = savedCvs.value.find(c => c.is_default) || savedCvs.value[0];
      selectedSavedCvId.value = def.id;
    } else {
      cvSource.value = 'upload'; // no saved CVs → default to upload tab
    }
  } catch (err) { /* silent */ }
};

const renderSavedCvToPdfBlob = async () => {
  await new Promise(r => setTimeout(r, 300));
  const el = document.getElementById('apply-cv-paper');
  if (!el) throw new Error('CV render not found');
  await document.fonts.ready;
  const dataUrl = await toPng(el, { quality: 1, pixelRatio: 1.5, backgroundColor: '#ffffff' });
  const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
  const props = pdf.getImageProperties(dataUrl);
  const pdfWidth = pdf.internal.pageSize.getWidth();
  const pdfHeight = (props.height * pdfWidth) / props.width;
  pdf.addImage(dataUrl, 'PNG', 0, 0, pdfWidth, pdfHeight);
  const buf = await pdf.output('arraybuffer');
  return new Blob([buf], { type: 'application/pdf' });
};

const submitApplication = async () => {
  if (!coverLetter.value.trim() && cvSource.value === 'none') {
    info("Lūdzu pievienojiet motivācijas vēstuli vai CV!");
    return;
  }
  if (cvSource.value === 'upload' && !cvFile.value && !coverLetter.value.trim()) {
    info("Lūdzu pievienojiet CV failu vai pārslēdziet režīmu.");
    return;
  }
  if (cvSource.value === 'saved' && !selectedSavedCvData.value && !coverLetter.value.trim()) {
    info("Lūdzu izvēlieties saglabātu CV vai pievienojiet motivācijas vēstuli.");
    return;
  }

  const formData = new FormData();
  formData.append("cover_letter", coverLetter.value);

  try {
    if (cvSource.value === 'upload' && cvFile.value) {
      formData.append("cv", cvFile.value);
    } else if (cvSource.value === 'saved' && selectedSavedCvData.value) {
      const blob = await renderSavedCvToPdfBlob();
      const safeName = (selectedSavedCvData.value.title || 'CV').replace(/[^a-z0-9_-]+/gi, '_');
      formData.append("cv", new File([blob], `${safeName}.pdf`, { type: 'application/pdf' }));
    }

    await api.post(
      `/vacancies/${vacancy.value.id}/apply`,
      formData,
      { headers: { Authorization: `Bearer ${token}` } }
    );

    success("Pieteikums veiksmīgi nosūtīts!");
    showApplicationModal.value = false;
    coverLetter.value = "";
    cvFile.value = null;
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās nosūtīt pieteikumu. Pārbaudi datus!");
  }
};

const fetchFavorites = async () => {
  if (!token) return;
  try {
    const res = await api.get("/favorites");
    const ids = res.data.map((f) => f.job_vacancy_id);
    isFavorited.value = ids.includes(Number(route.params.id));
  } catch (err) {
    // silent
  }
};

onMounted(async () => {
  await fetchUser();
  await fetchVacancy();
  await fetchComments();
  await fetchFavorites();
  await fetchSavedCvs();
});
</script>


