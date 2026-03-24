<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] font-sans">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">

      <!-- Header -->
      <section class="mb-12 space-y-4">
        <div class="flex items-center gap-4">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400">Sistēmas pārvaldība</span>
        </div>
        <h1 class="text-6xl md:text-8xl font-medium tracking-tight leading-none uppercase italic font-serif">
          Admin<span class="text-blue-600">.</span>
        </h1>
      </section>

      <!-- Tabs -->
      <div class="flex gap-0 border-b border-black/[0.06] mb-12">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest transition-all relative"
          :class="activeTab === tab.key
            ? 'text-black'
            : 'text-gray-400 hover:text-black'"
        >
          {{ tab.label }}
          <span
            v-if="activeTab === tab.key"
            class="absolute bottom-0 left-0 w-full h-[2px] bg-blue-600"
          ></span>
        </button>
      </div>

      <!-- ===================== DASHBOARD ===================== -->
      <div v-if="activeTab === 'dashboard'">
        <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Lietotāji</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.users.total }}</p>
            <div class="text-[10px] text-gray-400 space-y-1 pt-2">
              <p>Kandidāti: <span class="font-bold text-black">{{ stats.users.candidates }}</span></p>
              <p>Uzņēmēji: <span class="font-bold text-black">{{ stats.users.employers }}</span></p>
              <p>Admini: <span class="font-bold text-black">{{ stats.users.admins }}</span></p>
            </div>
          </div>
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Vakances</p>
            <p class="text-5xl font-bold tracking-tighter text-blue-600">{{ stats.vacancies }}</p>
          </div>
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Pieteikumi</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.applications }}</p>
          </div>
          <div class="p-8 border-2 border-black bg-black text-white space-y-2 shadow-[6px_6px_0px_#2563eb]">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Aktivitāte</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.users.total + stats.vacancies }}</p>
            <p class="text-[10px] text-gray-400">Kopā ieraksti</p>
          </div>
        </div>

        <div v-else class="py-20 text-center text-gray-300 italic font-serif text-2xl">
          Ielādē datus...
        </div>

        <!-- Quick actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <button
            @click="activeTab = 'users'"
            class="p-8 border border-black/[0.05] bg-white text-left hover:border-black transition-all group"
          >
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400 mb-3">Pārvaldīt</p>
            <p class="text-2xl font-medium group-hover:text-blue-600 transition-colors">Lietotāji →</p>
          </button>
          <button
            @click="activeTab = 'vacancies'"
            class="p-8 border border-black/[0.05] bg-white text-left hover:border-black transition-all group"
          >
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400 mb-3">Pārvaldīt</p>
            <p class="text-2xl font-medium group-hover:text-blue-600 transition-colors">Vakances →</p>
          </button>
          <button
            @click="activeTab = 'applications'"
            class="p-8 border border-black/[0.05] bg-white text-left hover:border-black transition-all group"
          >
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400 mb-3">Pārskatīt</p>
            <p class="text-2xl font-medium group-hover:text-blue-600 transition-colors">Pieteikumi →</p>
          </button>
        </div>
      </div>

      <!-- ===================== USERS ===================== -->
      <div v-if="activeTab === 'users'">
        <div class="flex justify-between items-center mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
            {{ users.length }} lietotāji
          </p>
          <input
            v-model="userSearch"
            placeholder="Meklēt..."
            class="border-b border-gray-200 py-2 px-0 text-sm outline-none focus:border-black transition-colors w-48 bg-transparent"
          />
        </div>

        <div v-if="users.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">
          Ielādē...
        </div>

        <div v-else class="space-y-1">
          <!-- Table header -->
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-3">Vārds</div>
            <div class="col-span-3">Lietotājvārds / E-pasts</div>
            <div class="col-span-2">Loma</div>
            <div class="col-span-3">Uzņēmums</div>
            <div class="col-span-1 text-right">Darbība</div>
          </div>

          <div
            v-for="u in filteredUsers"
            :key="u.id"
            class="grid grid-cols-12 items-center px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all"
          >
            <div class="col-span-3">
              <p class="text-sm font-medium">{{ u.firstname }} {{ u.lastname }}</p>
              <p class="text-[9px] text-gray-400 mt-0.5">ID: {{ u.id }}</p>
            </div>
            <div class="col-span-3">
              <p class="text-sm">{{ u.username }}</p>
              <p class="text-[10px] text-gray-400">{{ u.email }}</p>
            </div>
            <div class="col-span-2">
              <span
                class="text-[9px] font-bold uppercase tracking-widest px-2 py-1"
                :class="{
                  'bg-blue-50 text-blue-600': u.role === 'uzņēmējs',
                  'bg-gray-100 text-gray-600': u.role === 'bezdarbnieks',
                  'bg-black text-white': u.role === 'admin',
                  'bg-emerald-50 text-emerald-600': u.role === 'darbinieks',
                }"
              >
                {{ u.role }}
              </span>
            </div>
            <div class="col-span-3">
              <p class="text-sm text-gray-500">{{ u.company_name || '–' }}</p>
            </div>
            <div class="col-span-1 text-right">
              <button
                @click="deleteUser(u)"
                class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600 transition-colors"
              >
                Dzēst
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================== VACANCIES ===================== -->
      <div v-if="activeTab === 'vacancies'">
        <div class="flex justify-between items-center mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">
            {{ vacancies.length }} vakances
          </p>
          <input
            v-model="vacancySearch"
            placeholder="Meklēt..."
            class="border-b border-gray-200 py-2 px-0 text-sm outline-none focus:border-black transition-colors w-48 bg-transparent"
          />
        </div>

        <div v-if="vacancies.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">
          Ielādē...
        </div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-4">Amats / Uzņēmums</div>
            <div class="col-span-2">Kategorija</div>
            <div class="col-span-2">Reģions</div>
            <div class="col-span-2">Alga</div>
            <div class="col-span-1">Darba devējs</div>
            <div class="col-span-1 text-right">Darbība</div>
          </div>

          <div
            v-for="v in filteredVacancies"
            :key="v.id"
            class="grid grid-cols-12 items-center px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all"
          >
            <div class="col-span-4">
              <p class="text-sm font-medium">{{ v.title }}</p>
              <p class="text-[10px] text-gray-400">{{ v.company }}</p>
            </div>
            <div class="col-span-2">
              <p class="text-[10px] text-gray-600">{{ v.category }}</p>
            </div>
            <div class="col-span-2">
              <p class="text-[10px] text-gray-600">{{ v.county }}</p>
            </div>
            <div class="col-span-2">
              <p class="text-sm font-bold">€{{ v.salary }}</p>
              <p class="text-[9px] text-gray-400">{{ v.salary_type }}</p>
            </div>
            <div class="col-span-1">
              <p class="text-[10px] text-gray-500">{{ v.user?.firstname }} {{ v.user?.lastname }}</p>
            </div>
            <div class="col-span-1 text-right">
              <button
                @click="deleteVacancy(v)"
                class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600 transition-colors"
              >
                Dzēst
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================== APPLICATIONS ===================== -->
      <div v-if="activeTab === 'applications'">
        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-8">
          {{ applications.length }} pieteikumi
        </p>

        <div v-if="applications.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">
          Ielādē...
        </div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-3">Kandidāts</div>
            <div class="col-span-4">Vakance</div>
            <div class="col-span-4">Pavadvēstule</div>
            <div class="col-span-1 text-right">CV</div>
          </div>

          <div
            v-for="app in applications"
            :key="app.id"
            class="grid grid-cols-12 items-start px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all"
          >
            <div class="col-span-3">
              <p class="text-sm font-medium">{{ app.user?.firstname }} {{ app.user?.lastname }}</p>
              <p class="text-[10px] text-gray-400">{{ app.user?.email }}</p>
            </div>
            <div class="col-span-4">
              <p class="text-sm">{{ app.vacancy?.title || '–' }}</p>
              <p class="text-[10px] text-gray-400">{{ app.vacancy?.company }}</p>
            </div>
            <div class="col-span-4 pr-6">
              <p class="text-xs text-gray-500 italic leading-relaxed line-clamp-2">
                "{{ app.cover_letter || 'Nav pievienota.' }}"
              </p>
            </div>
            <div class="col-span-1 text-right">
              <span v-if="app.cv_path" class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Ir</span>
              <span v-else class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Nav</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/services/api";
import { useToast } from "@/composables/useToast";

const { success, error } = useToast();

const activeTab = ref("dashboard");
const tabs = [
  { key: "dashboard", label: "Pārskats" },
  { key: "users", label: "Lietotāji" },
  { key: "vacancies", label: "Vakances" },
  { key: "applications", label: "Pieteikumi" },
];

const stats = ref(null);
const users = ref([]);
const vacancies = ref([]);
const applications = ref([]);
const userSearch = ref("");
const vacancySearch = ref("");

const filteredUsers = computed(() => {
  const q = userSearch.value.toLowerCase();
  if (!q) return users.value;
  return users.value.filter(
    (u) =>
      u.firstname?.toLowerCase().includes(q) ||
      u.lastname?.toLowerCase().includes(q) ||
      u.username?.toLowerCase().includes(q) ||
      u.email?.toLowerCase().includes(q)
  );
});

const filteredVacancies = computed(() => {
  const q = vacancySearch.value.toLowerCase();
  if (!q) return vacancies.value;
  return vacancies.value.filter(
    (v) =>
      v.title?.toLowerCase().includes(q) ||
      v.company?.toLowerCase().includes(q) ||
      v.category?.toLowerCase().includes(q)
  );
});

const fetchAll = async () => {
  try {
    const [dashRes, usersRes, vacRes, appRes] = await Promise.all([
      api.get("/admin/dashboard"),
      api.get("/admin/users"),
      api.get("/admin/vacancies"),
      api.get("/admin/applications"),
    ]);
    stats.value = dashRes.data;
    users.value = usersRes.data;
    vacancies.value = vacRes.data;
    applications.value = appRes.data;
  } catch (err) {
    error("Neizdevās ielādēt admin datus.");
  }
};

const deleteUser = async (u) => {
  if (!confirm(`Dzēst lietotāju "${u.firstname} ${u.lastname}"?`)) return;
  try {
    await api.delete(`/admin/users/${u.id}`);
    users.value = users.value.filter((x) => x.id !== u.id);
    if (stats.value) stats.value.users.total--;
    success("Lietotājs dzēsts.");
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās dzēst lietotāju.");
  }
};

const deleteVacancy = async (v) => {
  if (!confirm(`Dzēst vakanci "${v.title}"?`)) return;
  try {
    await api.delete(`/admin/vacancies/${v.id}`);
    vacancies.value = vacancies.value.filter((x) => x.id !== v.id);
    if (stats.value) stats.value.vacancies--;
    success("Vakance dzēsta.");
  } catch (err) {
    error("Neizdevās dzēst vakanci.");
  }
};

onMounted(fetchAll);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@400;500;700&display=swap');

.font-serif { font-family: 'DM Serif Display', serif; }
div { font-family: 'Inter', sans-serif; }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
