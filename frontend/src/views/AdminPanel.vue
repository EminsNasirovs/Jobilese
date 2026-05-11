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
      <div class="flex gap-0 border-b border-black/[0.06] mb-12 overflow-x-auto">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest transition-all relative whitespace-nowrap"
          :class="activeTab === tab.key ? 'text-black' : 'text-gray-400 hover:text-black'"
        >
          {{ tab.label }}
          <span v-if="activeTab === tab.key" class="absolute bottom-0 left-0 w-full h-[2px] bg-blue-600"></span>
        </button>
      </div>

      <!-- ===================== DASHBOARD ===================== -->
      <div v-if="activeTab === 'dashboard'">
        <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Lietotāji</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.users.total }}</p>
            <div class="text-[10px] text-gray-400 space-y-1 pt-2">
              <p>Kandidāti: <span class="font-bold text-black">{{ stats.users.candidates }}</span></p>
              <p>Uzņēmēji: <span class="font-bold text-black">{{ stats.users.employers }}</span></p>
              <p>Admini: <span class="font-bold text-black">{{ stats.users.admins }}</span></p>
              <p class="text-emerald-500">+{{ stats.users.new_week }} nedēļā</p>
            </div>
          </div>
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Vakances</p>
            <p class="text-5xl font-bold tracking-tighter text-blue-600">{{ stats.vacancies.total }}</p>
            <p class="text-[10px] text-emerald-500 pt-2">+{{ stats.vacancies.new_week }} nedēļā</p>
          </div>
          <div class="p-8 border border-black/[0.05] bg-white space-y-2">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Pieteikumi</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.applications.total }}</p>
            <div class="text-[10px] text-gray-400 space-y-1 pt-2">
              <p>Gaida: <span class="font-bold text-amber-500">{{ stats.applications.pending }}</span></p>
              <p>Pieņemti: <span class="font-bold text-emerald-500">{{ stats.applications.accepted }}</span></p>
              <p>Noraidīti: <span class="font-bold text-red-500">{{ stats.applications.denied }}</span></p>
            </div>
          </div>
          <div class="p-8 border-2 border-black bg-black text-white space-y-2 shadow-[6px_6px_0px_#2563eb]">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400">Sarunas</p>
            <p class="text-5xl font-bold tracking-tighter">{{ stats.chats.conversations }}</p>
            <p class="text-[10px] text-gray-400">{{ stats.chats.messages }} ziņas · {{ stats.comments }} komentāri</p>
          </div>
        </div>

        <div v-else class="py-20 text-center text-gray-300 italic font-serif text-2xl">Ielādē datus...</div>

        <!-- Quick actions -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
          <button v-for="t in tabs.filter(x => x.key !== 'dashboard')" :key="t.key"
                  @click="activeTab = t.key"
                  class="p-6 border border-black/[0.05] bg-white text-left hover:border-black transition-all group">
            <p class="text-[9px] uppercase font-bold tracking-widest text-gray-400 mb-2">Pārvaldīt</p>
            <p class="text-lg font-medium group-hover:text-blue-600 transition-colors">{{ t.label }} →</p>
          </button>
        </div>
      </div>

      <!-- ===================== USERS ===================== -->
      <div v-if="activeTab === 'users'">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ filteredUsers.length }} lietotāji</p>
          <div class="flex gap-4 items-center">
            <select v-model="userRoleFilter" class="text-xs border-b border-gray-200 py-2 outline-none bg-transparent">
              <option value="">Visas lomas</option>
              <option value="bezdarbnieks">Bezdarbnieks</option>
              <option value="darbinieks">Darbinieks</option>
              <option value="uzņēmējs">Uzņēmējs</option>
              <option value="admin">Admin</option>
            </select>
            <input v-model="userSearch" placeholder="Meklēt..."
                   class="border-b border-gray-200 py-2 px-0 text-sm outline-none focus:border-black w-48 bg-transparent" />
          </div>
        </div>

        <div v-if="users.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">Ielādē...</div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-3">Vārds</div>
            <div class="col-span-3">Lietotājvārds / E-pasts</div>
            <div class="col-span-2">Loma</div>
            <div class="col-span-2">Uzņēmums</div>
            <div class="col-span-2 text-right">Darbības</div>
          </div>

          <div v-for="u in filteredUsers" :key="u.id"
               class="grid grid-cols-12 items-center px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all">
            <div class="col-span-3">
              <p class="text-sm font-medium">{{ u.firstname }} {{ u.lastname }}</p>
              <p class="text-[9px] text-gray-400 mt-0.5">ID: {{ u.id }}</p>
            </div>
            <div class="col-span-3">
              <p class="text-sm">{{ u.username }}</p>
              <p class="text-[10px] text-gray-400">{{ u.email }}</p>
            </div>
            <div class="col-span-2">
              <select :value="u.role" @change="changeRole(u, $event.target.value)"
                      class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 bg-transparent border border-black/10 cursor-pointer">
                <option value="bezdarbnieks">bezdarbnieks</option>
                <option value="darbinieks">darbinieks</option>
                <option value="uzņēmējs">uzņēmējs</option>
                <option value="admin">admin</option>
              </select>
            </div>
            <div class="col-span-2">
              <p class="text-sm text-gray-500 truncate">{{ u.company_name || '–' }}</p>
            </div>
            <div class="col-span-2 text-right">
              <button @click="deleteUser(u)" class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600">
                Dzēst
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================== VACANCIES ===================== -->
      <div v-if="activeTab === 'vacancies'">
        <div class="flex justify-between items-center mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ filteredVacancies.length }} vakances</p>
          <input v-model="vacancySearch" placeholder="Meklēt..."
                 class="border-b border-gray-200 py-2 px-0 text-sm outline-none focus:border-black w-48 bg-transparent" />
        </div>

        <div v-if="vacancies.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">Ielādē...</div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-4">Amats / Uzņēmums</div>
            <div class="col-span-2">Kategorija</div>
            <div class="col-span-2">Reģions</div>
            <div class="col-span-2">Alga</div>
            <div class="col-span-1">Devējs</div>
            <div class="col-span-1 text-right">Darbība</div>
          </div>

          <div v-for="v in filteredVacancies" :key="v.id"
               class="grid grid-cols-12 items-center px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all">
            <div class="col-span-4">
              <p class="text-sm font-medium">{{ v.title }}</p>
              <p class="text-[10px] text-gray-400">{{ v.company }}</p>
            </div>
            <div class="col-span-2"><p class="text-[10px] text-gray-600">{{ v.category }}</p></div>
            <div class="col-span-2"><p class="text-[10px] text-gray-600">{{ v.county }}</p></div>
            <div class="col-span-2">
              <p class="text-sm font-bold">€{{ v.salary }}</p>
              <p class="text-[9px] text-gray-400">{{ v.salary_type }}</p>
            </div>
            <div class="col-span-1"><p class="text-[10px] text-gray-500 truncate">{{ v.user?.firstname }} {{ v.user?.lastname }}</p></div>
            <div class="col-span-1 text-right">
              <button @click="deleteVacancy(v)" class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600">Dzēst</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================== APPLICATIONS ===================== -->
      <div v-if="activeTab === 'applications'">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ filteredApplications.length }} pieteikumi</p>
          <select v-model="appStatusFilter" class="text-xs border-b border-gray-200 py-2 outline-none bg-transparent">
            <option value="">Visi statusi</option>
            <option value="pending">Gaida</option>
            <option value="accepted">Pieņemti</option>
            <option value="denied">Noraidīti</option>
          </select>
        </div>

        <div v-if="applications.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">Ielādē...</div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-3">Kandidāts</div>
            <div class="col-span-3">Vakance</div>
            <div class="col-span-3">Pavadvēstule</div>
            <div class="col-span-1">Status</div>
            <div class="col-span-1 text-center">CV</div>
            <div class="col-span-1 text-right">Darbība</div>
          </div>

          <div v-for="app in filteredApplications" :key="app.id"
               class="grid grid-cols-12 items-start px-5 py-5 bg-white border border-black/[0.03] hover:border-black/20 transition-all">
            <div class="col-span-3">
              <p class="text-sm font-medium">{{ app.user?.firstname }} {{ app.user?.lastname }}</p>
              <p class="text-[10px] text-gray-400">{{ app.user?.email }}</p>
            </div>
            <div class="col-span-3">
              <p class="text-sm">{{ app.vacancy?.title || '–' }}</p>
              <p class="text-[10px] text-gray-400">{{ app.vacancy?.company }}</p>
            </div>
            <div class="col-span-3 pr-6">
              <p class="text-xs text-gray-500 italic leading-relaxed line-clamp-2">"{{ app.cover_letter || 'Nav pievienota.' }}"</p>
            </div>
            <div class="col-span-1">
              <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-1"
                    :class="{
                      'bg-amber-50 text-amber-600': app.status === 'pending' || !app.status,
                      'bg-emerald-50 text-emerald-600': app.status === 'accepted',
                      'bg-red-50 text-red-600': app.status === 'denied',
                    }">{{ app.status || 'pending' }}</span>
            </div>
            <div class="col-span-1 text-center">
              <span v-if="app.cv_path" class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Ir</span>
              <span v-else class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Nav</span>
            </div>
            <div class="col-span-1 text-right">
              <button @click="deleteApplication(app)" class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600">Dzēst</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================== COMMENTS ===================== -->
      <div v-if="activeTab === 'comments'">
        <div class="flex justify-between items-center mb-8">
          <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ filteredComments.length }} komentāri</p>
          <input v-model="commentSearch" placeholder="Meklēt tekstā..."
                 class="border-b border-gray-200 py-2 px-0 text-sm outline-none focus:border-black w-48 bg-transparent" />
        </div>

        <div v-if="comments.length === 0" class="py-20 text-center text-gray-300 italic font-serif text-2xl">Nav komentāru.</div>

        <div v-else class="space-y-1">
          <div class="grid grid-cols-12 px-5 py-3 text-[9px] font-bold uppercase tracking-widest text-gray-400 border-b border-black/[0.05]">
            <div class="col-span-2">Autors</div>
            <div class="col-span-7">Komentārs</div>
            <div class="col-span-2">Datums</div>
            <div class="col-span-1 text-right">Darbība</div>
          </div>

          <div v-for="c in filteredComments" :key="c.id"
               class="grid grid-cols-12 items-start px-5 py-4 bg-white border border-black/[0.03] hover:border-black/20 transition-all">
            <div class="col-span-2">
              <p class="text-sm font-medium">{{ c.user?.username || '–' }}</p>
              <p class="text-[9px] text-gray-400">{{ c.user?.firstname }} {{ c.user?.lastname }}</p>
            </div>
            <div class="col-span-7 pr-4">
              <p class="text-xs leading-relaxed">{{ c.comment_text }}</p>
              <p v-if="c.parent_id" class="text-[9px] text-blue-500 mt-1">↳ atbilde</p>
            </div>
            <div class="col-span-2">
              <p class="text-[10px] text-gray-400">{{ formatDate(c.created_at) }}</p>
            </div>
            <div class="col-span-1 text-right">
              <button @click="deleteComment(c)" class="text-[9px] font-bold uppercase tracking-widest text-red-400 hover:text-red-600">Dzēst</button>
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
  { key: "comments", label: "Komentāri" },
];

const stats = ref(null);
const users = ref([]);
const vacancies = ref([]);
const applications = ref([]);
const comments = ref([]);

const userSearch = ref("");
const userRoleFilter = ref("");
const vacancySearch = ref("");
const appStatusFilter = ref("");
const commentSearch = ref("");

const filteredUsers = computed(() => {
  const q = userSearch.value.toLowerCase();
  return users.value.filter(u => {
    if (userRoleFilter.value && u.role !== userRoleFilter.value) return false;
    if (!q) return true;
    return (
      u.firstname?.toLowerCase().includes(q) ||
      u.lastname?.toLowerCase().includes(q) ||
      u.username?.toLowerCase().includes(q) ||
      u.email?.toLowerCase().includes(q)
    );
  });
});

const filteredVacancies = computed(() => {
  const q = vacancySearch.value.toLowerCase();
  if (!q) return vacancies.value;
  return vacancies.value.filter(v =>
    v.title?.toLowerCase().includes(q) ||
    v.company?.toLowerCase().includes(q) ||
    v.category?.toLowerCase().includes(q)
  );
});

const filteredApplications = computed(() => {
  if (!appStatusFilter.value) return applications.value;
  return applications.value.filter(a => (a.status || 'pending') === appStatusFilter.value);
});

const filteredComments = computed(() => {
  const q = commentSearch.value.toLowerCase();
  if (!q) return comments.value;
  return comments.value.filter(c => c.comment_text?.toLowerCase().includes(q));
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('lv-LV') : '';

const fetchAll = async () => {
  try {
    const [dashRes, usersRes, vacRes, appRes, comRes] = await Promise.all([
      api.get("/admin/dashboard"),
      api.get("/admin/users"),
      api.get("/admin/vacancies"),
      api.get("/admin/applications"),
      api.get("/admin/comments"),
    ]);
    stats.value = dashRes.data;
    users.value = usersRes.data;
    vacancies.value = vacRes.data;
    applications.value = appRes.data;
    comments.value = comRes.data;
  } catch (err) {
    error("Neizdevās ielādēt admin datus.");
  }
};

const deleteUser = async (u) => {
  if (!confirm(`Dzēst lietotāju "${u.firstname} ${u.lastname}"?`)) return;
  try {
    await api.delete(`/admin/users/${u.id}`);
    users.value = users.value.filter(x => x.id !== u.id);
    if (stats.value) stats.value.users.total--;
    success("Lietotājs dzēsts.");
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās dzēst lietotāju.");
  }
};

const changeRole = async (u, role) => {
  if (u.role === role) return;
  if (!confirm(`Mainīt lomu lietotājam "${u.username}" uz "${role}"?`)) return;
  try {
    await api.patch(`/admin/users/${u.id}/role`, { role });
    u.role = role;
    success("Loma atjaunota.");
  } catch (err) {
    error(err.response?.data?.message || "Neizdevās mainīt lomu.");
  }
};

const deleteVacancy = async (v) => {
  if (!confirm(`Dzēst vakanci "${v.title}"?`)) return;
  try {
    await api.delete(`/admin/vacancies/${v.id}`);
    vacancies.value = vacancies.value.filter(x => x.id !== v.id);
    if (stats.value) stats.value.vacancies.total--;
    success("Vakance dzēsta.");
  } catch (err) {
    error("Neizdevās dzēst vakanci.");
  }
};

const deleteApplication = async (app) => {
  if (!confirm(`Dzēst pieteikumu #${app.id}?`)) return;
  try {
    await api.delete(`/admin/applications/${app.id}`);
    applications.value = applications.value.filter(x => x.id !== app.id);
    if (stats.value) stats.value.applications.total--;
    success("Pieteikums dzēsts.");
  } catch (err) {
    error("Neizdevās dzēst pieteikumu.");
  }
};

const deleteComment = async (c) => {
  if (!confirm(`Dzēst komentāru?`)) return;
  try {
    await api.delete(`/admin/comments/${c.id}`);
    comments.value = comments.value.filter(x => x.id !== c.id);
    if (stats.value) stats.value.comments--;
    success("Komentārs dzēsts.");
  } catch (err) {
    error("Neizdevās dzēst komentāru.");
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
