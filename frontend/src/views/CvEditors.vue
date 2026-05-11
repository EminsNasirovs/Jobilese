<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen">

      <!-- ================= EDITOR PANEL ================= -->
      <div class="p-8 lg:p-16 border-r border-gray-100 overflow-y-auto max-h-screen custom-scrollbar">
        <header class="mb-12">
          <h1 class="text-5xl font-serif italic mb-4">CV Redaktors.</h1>
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
            Izveidojiet profesionālu eksportu
          </p>
        </header>

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

          <div class="grid grid-cols-2 gap-3">
            <button @click="saveCV"
                    class="bg-black text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-gray-800 transition-all shadow-xl">
              Saglabāt
            </button>
            <button @click="exportPDF"
                    class="bg-blue-600 text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-blue-700 transition-all shadow-xl">
              Eksportēt PDF
            </button>
          </div>
        </div>
      </div>

      <!-- ================= PREVIEW PANEL ================= -->
      <div class="bg-gray-200 p-8 lg:p-16 flex justify-center overflow-y-auto">
        <div id="cv-paper"
             class="bg-white w-[210mm] min-h-[297mm] shadow-2xl relative flex flex-col text-[#111111] overflow-hidden">

          <!-- ====== EDITORIAL ====== -->
          <div v-if="cvData.template === 'editorial'" class="p-[20mm] flex flex-col flex-grow">
            <div class="text-center border-b-2 border-black pb-10 mb-10">
              <h2 class="text-5xl font-serif uppercase tracking-tighter mb-4">
                {{ profile.firstname }} {{ profile.lastname }}
              </h2>
              <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">
                {{ profile.email }} <span class="mx-2">•</span> {{ profile.username }}
              </div>
            </div>
            <div class="space-y-10 flex-grow">
              <div v-if="cvData.summary">
                <h3 class="preview-section-title">Profils</h3>
                <p class="text-[13px] leading-relaxed italic text-gray-800">{{ cvData.summary }}</p>
              </div>
              <div v-if="cvData.experience.length">
                <h3 class="preview-section-title">Darba Pieredze</h3>
                <div v-for="(item, index) in cvData.experience" :key="index" class="mb-6 break-inside-avoid">
                  <div class="flex justify-between items-baseline mb-1">
                    <span class="font-bold text-base uppercase">{{ item.role || "Amats" }}</span>
                    <span class="text-xs italic font-serif text-gray-500">{{ item.company || "Uzņēmums" }}</span>
                  </div>
                  <p v-if="item.years" class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">{{ item.years }}</p>
                  <p class="text-[12px] text-gray-600 leading-snug whitespace-pre-line">{{ item.desc }}</p>
                </div>
              </div>
              <div v-if="cvData.education.length">
                <h3 class="preview-section-title">Izglītība</h3>
                <div v-for="(item, index) in cvData.education" :key="index" class="mb-6 break-inside-avoid">
                  <div class="flex justify-between items-baseline mb-1">
                    <span class="font-bold text-base uppercase">{{ item.school || "Mācību iestāde" }}</span>
                    <span class="text-xs italic font-serif text-gray-500">{{ item.year }}</span>
                  </div>
                  <p class="text-[12px] text-gray-600 leading-snug">{{ item.degree }}</p>
                </div>
              </div>
              <div v-if="cvData.skills.length">
                <h3 class="preview-section-title">Prasmes</h3>
                <div class="flex flex-wrap gap-x-6 gap-y-2">
                  <span v-for="skill in cvData.skills" :key="skill"
                        class="text-[12px] font-bold uppercase tracking-widest italic">/ {{ skill }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ====== SIDEBAR ====== -->
          <div v-else-if="cvData.template === 'sidebar'" class="grid grid-cols-[35%_65%] flex-grow">
            <aside class="bg-[#0F172A] text-white p-[15mm] space-y-8">
              <div>
                <h2 class="text-3xl font-serif leading-tight">{{ profile.firstname }}</h2>
                <h2 class="text-3xl font-serif leading-tight uppercase">{{ profile.lastname }}</h2>
                <div class="h-[2px] w-12 bg-blue-500 mt-4"></div>
              </div>
              <div class="space-y-2">
                <p class="text-[9px] uppercase font-bold tracking-widest text-blue-300">Kontakti</p>
                <p class="text-[11px] break-words">{{ profile.email }}</p>
                <p class="text-[11px] text-gray-400">@{{ profile.username }}</p>
              </div>
              <div v-if="cvData.skills.length" class="space-y-3">
                <p class="text-[9px] uppercase font-bold tracking-widest text-blue-300">Prasmes</p>
                <ul class="space-y-1.5">
                  <li v-for="skill in cvData.skills" :key="skill" class="text-[11px] flex items-center gap-2">
                    <span class="h-1 w-1 bg-blue-500 rounded-full"></span>{{ skill }}
                  </li>
                </ul>
              </div>
              <div v-if="cvData.education.length" class="space-y-3">
                <p class="text-[9px] uppercase font-bold tracking-widest text-blue-300">Izglītība</p>
                <div v-for="(item, index) in cvData.education" :key="index" class="space-y-0.5 break-inside-avoid">
                  <p class="text-[11px] font-bold">{{ item.school }}</p>
                  <p class="text-[10px] text-gray-400">{{ item.degree }}</p>
                  <p class="text-[9px] text-blue-300 uppercase tracking-widest">{{ item.year }}</p>
                </div>
              </div>
            </aside>
            <main class="p-[15mm] space-y-8">
              <div v-if="cvData.summary">
                <p class="text-[10px] uppercase font-bold tracking-widest text-blue-600 mb-3">Profils</p>
                <p class="text-[13px] leading-relaxed text-gray-800">{{ cvData.summary }}</p>
              </div>
              <div v-if="cvData.experience.length">
                <p class="text-[10px] uppercase font-bold tracking-widest text-blue-600 mb-4">Darba Pieredze</p>
                <div v-for="(item, index) in cvData.experience" :key="index"
                     class="mb-5 pb-5 border-b border-gray-100 last:border-0 break-inside-avoid">
                  <p class="font-bold text-sm uppercase">{{ item.role || "Amats" }}</p>
                  <p class="text-xs text-gray-600 italic">{{ item.company }}</p>
                  <p v-if="item.years" class="text-[9px] text-gray-400 uppercase tracking-widest mt-1">{{ item.years }}</p>
                  <p class="text-[12px] text-gray-700 leading-snug whitespace-pre-line mt-2">{{ item.desc }}</p>
                </div>
              </div>
            </main>
          </div>

          <!-- ====== MINIMAL ====== -->
          <div v-else class="p-[22mm] flex flex-col flex-grow">
            <div class="mb-10">
              <h2 class="text-6xl font-serif leading-none mb-3">
                {{ profile.firstname }}<br/>{{ profile.lastname }}
              </h2>
              <div class="flex gap-4 text-[10px] text-gray-500 uppercase tracking-widest mt-4">
                <span>{{ profile.email }}</span>
                <span>·</span>
                <span>@{{ profile.username }}</span>
              </div>
            </div>
            <div v-if="cvData.summary" class="mb-10">
              <p class="text-[14px] leading-relaxed text-gray-800 font-serif italic">"{{ cvData.summary }}"</p>
            </div>
            <div class="grid grid-cols-[120px_1fr] gap-x-10 gap-y-10">
              <template v-if="cvData.experience.length">
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Pieredze</p>
                <div>
                  <div v-for="(item, index) in cvData.experience" :key="index" class="mb-5 break-inside-avoid">
                    <p class="text-sm font-bold">{{ item.role }} <span class="text-gray-400 font-normal">— {{ item.company }}</span></p>
                    <p v-if="item.years" class="text-[10px] text-gray-400 uppercase tracking-widest">{{ item.years }}</p>
                    <p class="text-[12px] text-gray-600 leading-snug whitespace-pre-line mt-1">{{ item.desc }}</p>
                  </div>
                </div>
              </template>
              <template v-if="cvData.education.length">
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Izglītība</p>
                <div>
                  <div v-for="(item, index) in cvData.education" :key="index" class="mb-4 break-inside-avoid">
                    <p class="text-sm font-bold">{{ item.school }}</p>
                    <p class="text-[12px] text-gray-600">{{ item.degree }} <span v-if="item.year" class="text-gray-400">· {{ item.year }}</span></p>
                  </div>
                </div>
              </template>
              <template v-if="cvData.skills.length">
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Prasmes</p>
                <p class="text-[12px] text-gray-700 leading-relaxed">{{ cvData.skills.join(' · ') }}</p>
              </template>
            </div>
          </div>

        </div>
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

const { success, error } = useToast();

const templates = [
  { key: "editorial", label: "Editorial", desc: "Centrēts, serif virsraksts, klasisks." },
  { key: "sidebar",   label: "Sidebar",   desc: "Tumša kreisā josla ar kontaktiem." },
  { key: "minimal",   label: "Minimal",   desc: "Liels nosaukums, divkolonnu izkārtojums." },
];

const profile = ref({ firstname: "", lastname: "", email: "", username: "" });

const cvData = ref({
  summary: "",
  experience: [{ role: "", company: "", years: "", desc: "" }],
  education: [{ school: "", degree: "", year: "" }],
  skills: [],
  template: "editorial",
});

const newSkill = ref("");

const fetchInitialData = async () => {
  const token = localStorage.getItem("token");
  if (!token) return;
  try {
    const headers = { Authorization: `Bearer ${token}` };
    const userRes = await api.get("/profile", { headers });
    profile.value = userRes.data;

    const cvRes = await api.get("/cv", { headers });
    if (cvRes.data) {
      cvData.value = {
        summary: cvRes.data.summary || "",
        experience: cvRes.data.experience?.length ? cvRes.data.experience : [{ role: "", company: "", years: "", desc: "" }],
        education: cvRes.data.education?.length ? cvRes.data.education : [{ school: "", degree: "", year: "" }],
        skills: cvRes.data.skills || [],
        template: cvRes.data.template || "editorial",
      };
    }
  } catch (err) {
    console.error("Datu ielādes kļūda:", err);
  }
};
onMounted(fetchInitialData);

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
    const token = localStorage.getItem("token");
    await api.post("/cv", cvData.value, { headers: { Authorization: `Bearer ${token}` } });
    success("CV saglabāts veiksmīgi!");
  } catch (err) {
    error("Kļūda saglabājot!");
  }
};

const exportPDF = async () => {
  const element = document.getElementById("cv-paper");
  if (!element) return;
  try {
    await document.fonts.ready;
    const dataUrl = await toPng(element, {
      quality: 1,
      pixelRatio: 2,
      backgroundColor: '#ffffff',
    });
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
.preview-section-title {
  color: #2563EB;
  font-size: 14px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 1.5rem;
  border-left: 3px solid #2563EB;
  padding-left: 10px;
}
#cv-paper { background-color: #ffffff !important; color: #111111 !important; }
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
.break-inside-avoid { page-break-inside: avoid; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>
