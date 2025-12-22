<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen">

      <!-- ================= EDITOR ================= -->
      <div class="p-8 lg:p-16 border-r border-gray-100 overflow-y-auto max-h-screen custom-scrollbar">
        <header class="mb-12">
          <h1 class="text-5xl font-serif italic mb-4">CV Redaktors.</h1>
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
            Izveidojiet profesionālu eksportu
          </p>
        </header>

        <div class="space-y-12">

          <section class="space-y-4">
            <label class="section-label">Par mani</label>
            <textarea
              v-model="cvData.summary"
              class="input-editorial h-32"
              placeholder="Īss apraksts..."
            />
          </section>

          <section class="space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
              <label class="section-label text-blue-600">Darba Pieredze</label>
              <button
                @click="addExperience"
                class="text-[10px] font-bold uppercase hover:text-blue-600 transition"
              >
                + Pievienot
              </button>
            </div>

            <div
              v-for="(item, index) in cvData.experience"
              :key="index"
              class="p-6 bg-gray-50 space-y-4 relative group"
            >
              <button
                @click="removeExperience(index)"
                class="absolute top-4 right-4 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition"
              >
                ✕
              </button>

              <input v-model="item.role" class="input-editorial" placeholder="Amats" />
              <input v-model="item.company" class="input-editorial" placeholder="Uzņēmums" />
              <textarea v-model="item.desc" class="input-editorial h-20" placeholder="Apraksts..." />
            </div>
          </section>

          <section class="space-y-4">
            <label class="section-label">Prasmes</label>

            <div class="flex flex-wrap gap-2 mb-3">
              <span
                v-for="(skill, i) in cvData.skills"
                :key="i"
                class="bg-black text-white text-[9px] px-2 py-1 uppercase font-bold flex items-center gap-2"
              >
                {{ skill }}
                <button @click="cvData.skills.splice(i, 1)" class="hover:text-red-400 font-normal">
                  ✕
                </button>
              </span>
            </div>

            <input
              v-model="newSkill"
              @keyup.enter="addSkill"
              class="input-editorial"
              placeholder="Ieraksti prasmi un spied Enter"
            />
          </section>

          <button
            @click="saveCV"
            class="w-full bg-black text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-gray-800 transition-all shadow-xl"
          >
            Saglabāt Sistēmā
          </button>
        </div>
      </div>

      <!-- ================= PREVIEW ================= -->
      <div class="bg-gray-200 p-8 lg:p-16 flex justify-center">
        <div
          id="cv-paper"
          class="bg-white w-[210mm] h-[297mm] p-[20mm] shadow-2xl relative flex flex-col text-[#111111]"
        >
          <!-- HEADER -->
          <div class="text-center border-b-2 border-black pb-10 mb-10">
            <h2 class="text-5xl font-serif uppercase tracking-tighter mb-4">
              {{ profile.firstname }} {{ profile.lastname }}
            </h2>
            <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">
              {{ profile.email }} <span class="mx-2">•</span> {{ profile.username }}
            </div>
          </div>

          <!-- CONTENT -->
          <div class="space-y-10 flex-grow">
            <div v-if="cvData.summary">
              <h3 class="preview-section-title">Profils</h3>
              <p class="text-[13px] leading-relaxed italic text-gray-800">
                {{ cvData.summary }}
              </p>
            </div>

            <div v-if="cvData.experience.length">
              <h3 class="preview-section-title">Darba Pieredze</h3>

              <div
                v-for="(item, index) in cvData.experience"
                :key="index"
                class="mb-6 break-inside-avoid"
              >
                <div class="flex justify-between items-baseline mb-1">
                  <span class="font-bold text-base uppercase">
                    {{ item.role || "Amats" }}
                  </span>
                  <span class="text-xs italic font-serif text-gray-500">
                    {{ item.company || "Uzņēmums" }}
                  </span>
                </div>
                <p class="text-[12px] text-gray-600 leading-snug">
                  {{ item.desc }}
                </p>
              </div>
            </div>

            <div v-if="cvData.skills.length">
              <h3 class="preview-section-title">Prasmes</h3>
              <div class="flex flex-wrap gap-x-6 gap-y-2">
                <span
                  v-for="skill in cvData.skills"
                  :key="skill"
                  class="text-[12px] font-bold uppercase tracking-widest italic"
                >
                  / {{ skill }}
                </span>
              </div>
            </div>
          </div>

          <!-- EXPORT BUTTON -->
          <button
            @click="exportPDF"
            class="export-btn print:hidden"
            data-no-export
          >
            Eksportēt PDF
          </button>
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

const profile = ref({
  firstname: "",
  lastname: "",
  email: "",
  username: ""
});

const cvData = ref({
  summary: "",
  experience: [{ role: "", company: "", desc: "" }],
  skills: []
});

const newSkill = ref("");

const fetchInitialData = async () => {
  const token = localStorage.getItem("token");
  if (!token) return;

  const headers = { Authorization: `Bearer ${token}` };

  const userRes = await api.get("/profile", { headers });
  profile.value = userRes.data;

  const cvRes = await api.get("/cv", { headers });
  if (cvRes.data) cvData.value = cvRes.data;
};

onMounted(fetchInitialData);

const addExperience = () =>
  cvData.value.experience.push({ role: "", company: "", desc: "" });

const removeExperience = i =>
  cvData.value.experience.splice(i, 1);

const addSkill = () => {
  if (!newSkill.value.trim()) return;
  cvData.value.skills.push(newSkill.value.trim());
  newSkill.value = "";
};

const saveCV = async () => {
  const token = localStorage.getItem("token");
  await api.post("/cv", cvData.value, {
    headers: { Authorization: `Bearer ${token}` }
  });
  alert("CV saglabāts!");
};

const exportPDF = async () => {
  const element = document.getElementById("cv-paper");
  if (!element) return;

  try {
    // 1. Wait for fonts
    await document.fonts.ready;

    // 2. Convert HTML to a High-Res PNG
    // This bypasses the OKLCH parsing error because it renders to a canvas first
    const dataUrl = await toPng(element, {
      quality: 1,
      pixelRatio: 2, // Keeps it sharp
      filter: (node) => {
        // Exclude the export button from the image
        return !node.classList?.contains('export-btn');
      }
    });

    // 3. Create PDF
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4'
    });

    const imgProps = pdf.getImageProperties(dataUrl);
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

    // 4. Add the image to the PDF and save
    pdf.addImage(dataUrl, 'PNG', 0, 0, pdfWidth, pdfHeight);
    pdf.save(`${profile.value.firstname || "CV"}.pdf`);

  } catch (error) {
    console.error("Export failed:", error);
    alert("Kļūda eksportējot PDF. Lūdzu, mēģiniet vēlreiz.");
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

.preview-section-title { color: #2563EB; } /* Use Hex, not oklch() */

/* Ensure the paper has a forced background for the "camera" to see it */
#cv-paper {
  background-color: #ffffff !important;
  color: #111111 !important;
  print-color-adjust: exact;
  -webkit-print-color-adjust: exact;
}

.input-editorial {
  width: 100%;
  background: transparent;
  border-bottom: 1px solid rgba(0,0,0,0.1);
  padding: 10px 0;
  font-size: 0.95rem;
  outline: none;
}

.export-btn {
  position: absolute;
  bottom: 2rem;
  right: 2rem;
  background-color: #2563EB;
  color: white;
  padding: 1rem 2rem;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  border-radius: 999px;
}

.break-inside-avoid {
  page-break-inside: avoid;
}
</style>
