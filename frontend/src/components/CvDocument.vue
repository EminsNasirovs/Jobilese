<template>
  <div :id="paperId"
       class="bg-white w-[210mm] min-h-[297mm] shadow-2xl relative flex flex-col text-[#111111] overflow-hidden cv-paper">

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
        <div v-if="cvData.experience?.length">
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
        <div v-if="cvData.education?.length">
          <h3 class="preview-section-title">Izglītība</h3>
          <div v-for="(item, index) in cvData.education" :key="index" class="mb-6 break-inside-avoid">
            <div class="flex justify-between items-baseline mb-1">
              <span class="font-bold text-base uppercase">{{ item.school || "Mācību iestāde" }}</span>
              <span class="text-xs italic font-serif text-gray-500">{{ item.year }}</span>
            </div>
            <p class="text-[12px] text-gray-600 leading-snug">{{ item.degree }}</p>
          </div>
        </div>
        <div v-if="cvData.skills?.length">
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
        <div v-if="cvData.skills?.length" class="space-y-3">
          <p class="text-[9px] uppercase font-bold tracking-widest text-blue-300">Prasmes</p>
          <ul class="space-y-1.5">
            <li v-for="skill in cvData.skills" :key="skill" class="text-[11px] flex items-center gap-2">
              <span class="h-1 w-1 bg-blue-500 rounded-full"></span>{{ skill }}
            </li>
          </ul>
        </div>
        <div v-if="cvData.education?.length" class="space-y-3">
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
        <div v-if="cvData.experience?.length">
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
        <template v-if="cvData.experience?.length">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Pieredze</p>
          <div>
            <div v-for="(item, index) in cvData.experience" :key="index" class="mb-5 break-inside-avoid">
              <p class="text-sm font-bold">{{ item.role }} <span class="text-gray-400 font-normal">— {{ item.company }}</span></p>
              <p v-if="item.years" class="text-[10px] text-gray-400 uppercase tracking-widest">{{ item.years }}</p>
              <p class="text-[12px] text-gray-600 leading-snug whitespace-pre-line mt-1">{{ item.desc }}</p>
            </div>
          </div>
        </template>
        <template v-if="cvData.education?.length">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Izglītība</p>
          <div>
            <div v-for="(item, index) in cvData.education" :key="index" class="mb-4 break-inside-avoid">
              <p class="text-sm font-bold">{{ item.school }}</p>
              <p class="text-[12px] text-gray-600">{{ item.degree }} <span v-if="item.year" class="text-gray-400">· {{ item.year }}</span></p>
            </div>
          </div>
        </template>
        <template v-if="cvData.skills?.length">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Prasmes</p>
          <p class="text-[12px] text-gray-700 leading-relaxed">{{ cvData.skills.join(' · ') }}</p>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cvData: { type: Object, required: true },
  profile: { type: Object, required: true },
  paperId: { type: String, default: 'cv-paper' },
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap");
.font-serif { font-family: "DM Serif Display", serif; }
.cv-paper { background-color: #ffffff !important; color: #111111 !important; }
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
.break-inside-avoid { page-break-inside: avoid; }
</style>
