<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans selection:bg-blue-50">
    <main class="max-w-5xl mx-auto px-6 lg:px-12 py-20">
      
      <section class="mb-16 space-y-6">
        <div class="flex items-center gap-4 overflow-hidden">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Lietotāja iestatījumi</span>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-end gap-8">
          <h1 class="text-6xl md:text-8xl font-medium tracking-tight leading-none uppercase italic font-serif">
            Mans <br />
            <span class="text-blue-600">Profils.</span>
          </h1>
          <button
            v-if="!isEditing"
            @click="toggleEdit"
            class="group flex items-center gap-3 bg-black text-white px-8 py-4 text-[10px] font-bold uppercase tracking-widest hover:bg-blue-600 transition-all duration-500 shadow-xl"
          >
            Rediģēt datus
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
        </div>
      </section>

      <div v-if="user" class="grid grid-cols-1 lg:grid-cols-12 gap-16 border-t border-black/[0.05] pt-16">
        
        <aside class="lg:col-span-4 space-y-12">
          <div class="p-10 border border-black/[0.05] bg-gray-50/50 space-y-8">
            <div class="space-y-1">
              <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Sistēmas loma</p>
              <p class="text-2xl font-medium capitalize">{{ user.role }}</p>
            </div>
            <div class="space-y-1">
              <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Dalībnieks kopš</p>
              <p class="text-xl font-light italic font-serif">2024. gada janvāra</p>
            </div>
          </div>

          <div v-if="!isEditing" class="space-y-4">
            <button @click="showDeletePopup = true" class="text-[10px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600 transition-colors">
              Dzēst kontu un datus
            </button>
          </div>
        </aside>

        <div class="lg:col-span-8">
          <div v-if="!isEditing" class="grid grid-cols-1 md:grid-cols-2 gap-y-12 gap-x-8">
            <div v-for="(label, key) in displayFields" :key="key" class="space-y-2 border-b border-black/[0.03] pb-4">
              <p class="text-[9px] uppercase font-bold text-gray-300 tracking-widest">{{ label }}</p>
              <p class="text-lg text-gray-800">{{ user[key] || '–' }}</p>
            </div>
            
            <template v-if="user.role === 'uzņēmējs'">
              <div class="col-span-full pt-8">
                <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-blue-600 mb-8">Uzņēmuma informācija</h3>
              </div>
              <div v-for="(label, key) in companyFields" :key="key" class="space-y-2 border-b border-black/[0.03] pb-4">
                <p class="text-[9px] uppercase font-bold text-gray-300 tracking-widest">{{ label }}</p>
                <p class="text-lg text-gray-800">{{ user[key] || '–' }}</p>
              </div>
            </template>
          </div>

          <div v-else class="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div v-for="(label, key) in editFields" :key="key" class="space-y-2">
                <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest">{{ label }}</label>
                <input v-model="form[key]" type="text" class="edit-input" />
              </div>

              <div class="space-y-2">
                <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest">Dzimums</label>
                <select v-model="form.gender" class="edit-input bg-transparent">
                  <option value="vīrietis">Vīrietis</option>
                  <option value="sieviete">Sieviete</option>
                  <option value="cits">Cits</option>
                </select>
              </div>
            </div>

            <div v-if="form.role === 'uzņēmējs'" class="space-y-8 pt-8 border-t border-black/[0.05]">
              <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-blue-600">Uzņēmuma dati</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div v-for="(label, key) in companyFields" :key="key" class="space-y-2">
                  <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest">{{ label }}</label>
                  <input v-model="form[key]" type="text" class="edit-input" />
                </div>
              </div>
            </div>

            <div class="flex gap-4 pt-8">
              <button @click="saveChanges" class="bg-blue-600 text-white px-10 py-5 text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all">Saglabāt izmaiņas</button>
              <button @click="toggleEdit" class="border border-black px-10 py-5 text-[10px] font-bold uppercase tracking-widest hover:bg-gray-50 transition-all">Atcelt</button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="py-40 text-center font-serif italic text-2xl text-gray-300">
        Ielādējam jūsu datus...
      </div>
    </main>

    <div v-if="showDeletePopup" class="fixed inset-0 z-[100] bg-white/90 backdrop-blur-md flex items-center justify-center p-6">
      <div class="bg-white border border-red-100 p-12 max-w-md w-full shadow-2xl space-y-8">
        <h3 class="text-3xl font-serif italic text-red-600">Bīstama zona</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Šī darbība ir neatgriezeniska. Lūdzu, ievadiet savu paroli, lai apstiprinātu konta dzēšanu.</p>
        <input v-model="deletePassword" type="password" placeholder="Tava parole" class="edit-input border-red-100 focus:border-red-600" />
        <div class="flex gap-4">
          <button @click="confirmDelete" class="flex-1 bg-red-600 text-white py-4 text-[10px] font-bold uppercase tracking-widest hover:bg-red-700 transition-colors">Dzēst kontu</button>
          <button @click="showDeletePopup = false" class="flex-1 border border-gray-200 py-4 text-[10px] font-bold uppercase tracking-widest">Atcelt</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useToast } from "@/composables/useToast";

const { success, error } = useToast();

const user = ref(null);
const isEditing = ref(false);
const form = ref({});
const showDeletePopup = ref(false);
const deletePassword = ref("");
const token = localStorage.getItem("token");

const displayFields = {
  firstname: "Vārds",
  lastname: "Uzvārds",
  username: "Lietotājvārds",
  email: "E-pasta adrese",
  birth_date: "Dzimšanas datums",
  gender: "Dzimums"
};

const editFields = {
  firstname: "Vārds",
  lastname: "Uzvārds",
  username: "Lietotājvārds",
  email: "E-pasts",
  birth_date: "Dzimšanas datums"
};

const companyFields = {
  company_name: "Uzņēmuma nosaukums",
  company_number: "Reģistrācijas numurs",
  company_address: "Juridiskā adrese"
};

const fetchProfile = async () => {
  try {
    const res = await api.get("/profile", { headers: { Authorization: `Bearer ${token}` } });
    user.value = res.data;
    form.value = { ...res.data };
  } catch (err) { console.error(err); }
};

const toggleEdit = () => {
  isEditing.value = !isEditing.value;
  if (isEditing.value && user.value) form.value = { ...user.value };
};

const saveChanges = async () => {
  try {
    const res = await api.put("/profile", form.value, { headers: { Authorization: `Bearer ${token}` } });
    user.value = res.data;
    isEditing.value = false;
    success("Profils veiksmīgi atjaunināts!");
  } catch (err) { error("Neizdevās atjaunināt profilu."); }
};

const confirmDelete = async () => {
  if (!deletePassword.value) return;
  try {
    await api.delete("/profile/delete", {
      headers: { Authorization: `Bearer ${token}` },
      data: { password: deletePassword.value },
    });
    localStorage.removeItem("token");
    window.location.href = "/";
  } catch (err) { error(err.response?.data?.message || "Kļūda dzēšanā."); }
};

onMounted(fetchProfile);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap');

.font-serif { font-family: 'DM Serif Display', serif; }
main { font-family: 'Inter', sans-serif; }

.edit-input {
  /* Nomainīts border-black/10 uz border-gray-200, lai izvairītos no spraudņa kļūdas */
  width: 100%;
  background-color: transparent;
  border-bottom: 1px solid #E5E7EB;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  font-size: 1.125rem;
  outline: none;
  transition: border-color 0.2s ease, color 0.2s ease;
  font-weight: 300;
}

.edit-input:focus {
  border-bottom-color: #2563EB;
}

.edit-input::placeholder {
  color: #F3F4F6;
}
</style>