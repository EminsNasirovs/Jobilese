<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen">

      <!-- ================= EDITOR PANEL ================= -->
      <div class="p-8 lg:p-16 border-r border-gray-100 overflow-y-auto max-h-screen custom-scrollbar">
        <header class="mb-8">
          <h1 class="text-5xl font-serif italic mb-4">CV Redaktors.</h1>
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
            Izveidojiet vairākus CV un izvēlieties piemērotāko katrai vakancei
          </p>
        </header>

        <!-- CV switcher / list -->
        <section class="mb-10 space-y-3">
          <div class="flex justify-between items-center">
            <label class="section-label">Mani CV ({{ cvList.length }})</label>
            <button @click="createNewCv" type="button"
                    class="text-[10px] font-bold uppercase tracking-widest text-blue-600 hover:text-black transition-colors">
              + Jauns CV
            </button>
          </div>
          <div v-if="cvList.length === 0" class="text-xs text-gray-400 italic">
            Vēl nav saglabātu CV. Aizpildiet veidlapu un noklikšķiniet "Saglabāt".
          </div>
          <div v-else class="flex flex-wrap gap-2">
            <button
              v-for="cv in cvList"
              :key="cv.id"
              @click="selectCv(cv)"
              type="button"
              class="px-3 py-2 border text-xs font-medium transition-all flex items-center gap-2"
              :class="cv.id === selectedCvId
                ? 'border-black bg-black text-white'
                : 'border-gray-200 hover:border-black bg-white'"
            >
              <span>{{ cv.title }}</span>
              <span v-if="cv.is_default" class="text-[8px] font-bold uppercase tracking-widest"
                    :class="cv.id === selectedCvId ? 'text-blue-300' : 'text-blue-600'">★</span>
            </button>
          </div>
        </section>

        <!-- Title + default toggle -->
        <section class="mb-10 space-y-4">
          <div class="space-y-2">
            <label class="section-label">CV nosaukums</label>
            <input v-model="cvData.title" class="input-editorial" placeholder="Piem. Vue.js izstrādātāja CV" />
          </div>
          <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
            <input type="checkbox" v-model="cvData.is_default" />
            <span>Lietot šo kā noklusējuma CV pieteikumos</span>
          </label>
        </section>

        <!-- Template chooser -->
        <section class="mb-12">
          <label class="section-label mb-4 block">Dizains</label>
          <div class="grid grid-cols-3 gap-3">
            <button
              v-for="t in templates"
              :key="t.key"
              @click="cvData.template = t.key"
              type="button"
              class="p-4 border text-left transition-all"
              :class="cvData.template === t.key
                ? 'border-black bg-black text-white shadow-[4px_4px_0px_#2563eb]'
                : 'border-gray-200 hover:border-black bg-white'"
            >
              <p class="text-[9px] font-bold uppercase tracking-widest mb-2"
                 :class="cvData.template === t.key ? 'text-blue-300' : 'text-gray-400'">
                {{ t.label }}
              </p>
              <p class="text-xs" :class="cvData.template === t.key ? 'text-white' : 'text-gray-600'">
                {{ t.desc }}
              </p>
            </button>
          </div>
        </section>

        <div class="space-y-12">
          <section class="space-y-4">
            <label class="section-label">Par mani</label>
            <textarea v-model="cvData.summary" class="input-editorial h-32"
                      placeholder="Īss apraksts par jūsu mērķiem un prasmēm..." />
          </section>

          <section class="space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
              <label class="section-label text-blue-600">Darba Pieredze</label>
              <button @click="addExperience" class="text-[10px] font-bold uppercase hover:text-blue-600">+ Pievienot</button>
            </div>
            <div v-for="(item, index) in cvData.experience" :key="'exp-' + index"
                 class="p-6 bg-gray-50 space-y-4 relative group">
              <button @click="removeExperience(index)"
                      class="absolute top-4 right-4 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100">✕</button>
              <input v-model="item.role" class="input-editorial" placeholder="Amats" />
              <input v-model="item.company" class="input-editorial" placeholder="Uzņēmums" />
              <input v-model="item.years" class="input-editorial" placeholder="Periods (piem. 2020 - 2023)" />
              <textarea v-model="item.desc" class="input-editorial h-20" placeholder="Pienākumu apraksts..." />
            </div>
          </section>

          <section class="space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
              <label class="section-label text-blue-600">Izglītība</label>
              <button @click="addEducation" class="text-[10px] font-bold uppercase hover:text-blue-600">+ Pievienot</button>
            </div>
            <div v-for="(item, index) in cvData.education" :key="'edu-' + index"
                 class="p-6 bg-gray-50 space-y-4 relative group">
              <button @click="removeEducation(index)"
                      class="absolute top-4 right-4 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100">✕</button>
              <input v-model="item.school" class="input-editorial" placeholder="Mācību iestāde" />
              <div class="grid grid-cols-2 gap-4">
                <input v-model="item.degree" class="input-editorial" placeholder="Grāds / Specialitāte" />
                <input v-model="item.year" class="input-editorial" placeholder="Periods (piem. 2018 - 2022)" />
              </div>
            </div>
          </section>

          <section class="space-y-4">
            <label class="section-label">Prasmes</label>
            <div class="flex flex-wrap gap-2 mb-3">
              <span v-for="(skill, i) in cvData.skills" :key="i"
                    class="bg-black text-white text-[9px] px-2 py-1 uppercase font-bold flex items-center gap-2">
                {{ skill }}
                <button @click="cvData.skills.splice(i, 1)" class="hover:text-red-400 font-normal">✕</button>
              </span>
            </div>
            <input v-model="newSkill" @keyup.enter="addSkill" class="input-editorial"
                   placeholder="Ieraksti prasmi un spied Enter" />
          </section>

          <div class="grid grid-cols-3 gap-3">
            <button @click="saveCV"
                    class="bg-black text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-gray-800 transition-all shadow-xl">
              {{ selectedCvId ? 'Atjaunot' : 'Saglabāt' }}
            </button>
            <button @click="exportPDF"
                    class="bg-blue-600 text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-blue-700 transition-all shadow-xl">
              Eksportēt PDF
            </button>
            <button @click="deleteCv" :disabled="!selectedCvId"
                    class="bg-red-50 text-red-500 py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-red-500 hover:text-white transition-all disabled:opacity-30 disabled:cursor-not-allowed">
              Dzēst
            </button>
          </div>
        </div>
      </div>

      <!-- ================= PREVIEW PANEL ================= -->
      <div class="bg-gray-200 p-8 lg:p-16 flex justify-center overflow-y-auto">
        <CvDocument :cv-data="cvData" :profile="profile" paper-id="cv-paper" />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { toPng } from 'html-to-image';
import { jsPDF } from 'jspdf';
import api from "@/services/api";
import { useToast } from "@/composables/useToast";
import CvDocument from "@/components/CvDocument.vue";

const { success, error } = useToast();

const templates = [
  { key: "editorial", label: "Editorial", desc: "Centrēts, serif virsraksts, klasisks." },
  { key: "sidebar",   label: "Sidebar",   desc: "Tumša kreisā josla ar kontaktiem." },
  { key: "minimal",   label: "Minimal",   desc: "Liels nosaukums, divkolonnu izkārtojums." },
];

const profile = ref({ firstname: "", lastname: "", email: "", username: "" });
const cvList = ref([]);
const selectedCvId = ref(null);

const blankCv = () => ({
  title: "Mans CV",
  is_default: false,
  summary: "",
  experience: [{ role: "", company: "", years: "", desc: "" }],
  education: [{ school: "", degree: "", year: "" }],
  skills: [],
  template: "editorial",
});

const cvData = ref(blankCv());
const newSkill = ref("");

const normalizeCv = (raw) => ({
  title: raw.title || "Mans CV",
  is_default: !!raw.is_default,
  summary: raw.summary || "",
  experience: raw.experience?.length ? raw.experience : [{ role: "", company: "", years: "", desc: "" }],
  education: raw.education?.length ? raw.education : [{ school: "", degree: "", year: "" }],
  skills: raw.skills || [],
  template: raw.template || "editorial",
});

const fetchProfile = async () => {
  try {
    const res = await api.get("/profile");
    profile.value = res.data;
  } catch (err) { /* silent */ }
};

const fetchCvs = async () => {
  try {
    const res = await api.get("/cv");
    cvList.value = res.data || [];
    if (cvList.value.length) {
      const first = cvList.value.find(c => c.is_default) || cvList.value[0];
      selectCv(first);
    } else {
      selectedCvId.value = null;
      cvData.value = blankCv();
    }
  } catch (err) {
    error("Neizdevās ielādēt CV sarakstu");
  }
};

const selectCv = (cv) => {
  selectedCvId.value = cv.id;
  cvData.value = normalizeCv(cv);
};

const createNewCv = () => {
  selectedCvId.value = null;
  cvData.value = blankCv();
};

const addExperience = () => cvData.value.experience.push({ role: "", company: "", years: "", desc: "" });
const removeExperience = i => cvData.value.experience.splice(i, 1);
const addEducation = () => cvData.value.education.push({ school: "", degree: "", year: "" });
const removeEducation = i => cvData.value.education.splice(i, 1);
const addSkill = () => {
  if (!newSkill.value.trim()) return;
  cvData.value.skills.push(newSkill.value.trim());
  newSkill.value = "";
};

const saveCV = async () => {
  try {
    const payload = { ...cvData.value };
    if (selectedCvId.value) {
      const res = await api.put(`/cv/${selectedCvId.value}`, payload);
      success("CV atjaunots!");
      await fetchCvs();
      const updated = cvList.value.find(c => c.id === res.data.cv.id);
      if (updated) selectCv(updated);
    } else {
      const res = await api.post("/cv", payload);
      success("CV izveidots!");
      await fetchCvs();
      const created = cvList.value.find(c => c.id === res.data.cv.id);
      if (created) selectCv(created);
    }
  } catch (err) {
    error("Kļūda saglabājot CV");
  }
};

const deleteCv = async () => {
  if (!selectedCvId.value) return;
  if (!confirm(`Vai tiešām dzēst CV "${cvData.value.title}"?`)) return;
  try {
    await api.delete(`/cv/${selectedCvId.value}`);
    success("CV dzēsts");
    await fetchCvs();
  } catch (err) {
    error("Neizdevās dzēst CV");
  }
};

const exportPDF = async () => {
  const element = document.getElementById("cv-paper");
  if (!element) return;
  try {
    await document.fonts.ready;
    const dataUrl = await toPng(element, { quality: 1, pixelRatio: 2, backgroundColor: '#ffffff' });
    const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
    const imgProps = pdf.getImageProperties(dataUrl);
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
    pdf.addImage(dataUrl, 'PNG', 0, 0, pdfWidth, pdfHeight);
    pdf.save(`${profile.value.firstname || "CV"}_${cvData.value.template}.pdf`);
  } catch (err) {
    error("Kļūda eksportējot PDF.");
  }
};

onMounted(async () => {
  await fetchProfile();
  await fetchCvs();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap");
.font-serif { font-family: "DM Serif Display", serif; }
.section-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #9CA3AF;
}
.input-editorial {
  width: 100%;
  background: transparent;
  border-bottom: 1px solid rgba(0,0,0,0.1);
  padding: 10px 0;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
}
.input-editorial:focus { border-bottom-color: #2563EB; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>
