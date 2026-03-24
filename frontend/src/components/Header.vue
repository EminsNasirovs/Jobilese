<template>
  <header
    v-if="!hideHeader"
    class="sticky top-0 z-50 w-full bg-[#FDFDFC]/90 backdrop-blur-md border-b border-black/[0.03]"
  >
    <div class="mx-auto max-w-7xl px-6 lg:px-12">
      <div class="flex h-20 items-center justify-between">
        
        <RouterLink to="/" class="flex items-center gap-3 group">
  <span class="text-2xl font-bold tracking-[-0.05em] uppercase italic font-serif text-[#111111] group-hover:text-black transition-colors">
    Jobilese<span class="text-blue-600">.</span>
  </span>
</RouterLink>

        <nav class="hidden md:flex items-center gap-10">
          <NavLink to="/" label="Sākums" />
          <NavLink to="/vakances" label="Vakances" />
          <NavLink to="/favoriti" label="Favorīti" />
          
          <NavLink
            v-if="isLoggedIn && userRole === 'uzņēmējs'"
            to="/applications"
            label="Pieteikumi"
          />
          <NavLink
            v-if="isLoggedIn && userRole === 'admin'"
            to="/admin"
            label="Admin"
          />
          <NavLink
            v-if="isLoggedIn"
            to="/cv"
            label="CV"
          />
        </nav>

        <div class="flex items-center gap-8">
          
          <div v-if="isLoggedIn" class="relative">
            <button
              @click="toggleNotifications"
              class="relative p-2 text-gray-400 hover:text-black transition-colors duration-500"
            >
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3V5M12 5C8.68629 5 6 7.68629 6 11V14.5L4 17H20L18 14.5V11C18 7.68629 15.3137 5 12 5Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 17C9 18.6569 10.3431 20 12 20C13.6569 20 15 18.6569 15 17" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-blue-600 text-white text-[9px] font-bold min-w-[16px] h-4 rounded-full flex items-center justify-center px-1 ring-2 ring-[#FDFDFC]"
              >{{ unreadCount }}</span>
            </button>

            <div
              v-if="showDropdown"
              class="absolute right-0 mt-4 w-80 bg-white border border-black/5 shadow-[0_20px_50px_rgba(0,0,0,0.08)] p-5 animate-in fade-in slide-in-from-top-2"
            >
              <div class="flex justify-between items-center mb-4 pb-2 border-b border-black/5">
                <span class="text-[10px] uppercase font-bold tracking-[0.2em] text-gray-400">Paziņojumi</span>
                <button
                  v-if="unreadCount > 0"
                  @click="markAllRead"
                  class="text-[9px] uppercase font-bold tracking-widest text-blue-600 hover:text-black transition-colors"
                >
                  Atzīmēt visus
                </button>
              </div>

              <div v-if="notifications.length === 0" class="py-6 text-center text-xs text-gray-400 italic">
                Nav jaunu ziņu.
              </div>

              <div class="max-h-64 overflow-y-auto space-y-3">
                <div
                  v-for="n in notifications"
                  :key="n.id"
                  class="p-2 hover:bg-gray-50 transition-colors group cursor-pointer rounded"
                  :class="{ 'border-l-2 border-blue-600 pl-3': !n.read_at }"
                >
                  <p class="text-xs font-bold text-black group-hover:text-blue-600 transition-colors">
                    {{ n.data.applicant_name || n.data.message }}
                  </p>
                  <p class="text-[9px] text-gray-400 mt-0.5">{{ n.data.message }}</p>
                  <p class="text-[9px] text-gray-300 uppercase tracking-widest mt-1">
                    {{ formatDate(n.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-6">
            <template v-if="isLoggedIn">
              <RouterLink
                to="/profile"
                class="text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-blue-600 transition-colors"
              >
                Profils
              </RouterLink>
              <button
                @click="logout"
                class="text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-red-500 transition-colors"
              >
                Iziet
              </button>
            </template>

            <template v-else>
              <RouterLink
                to="/login"
                class="text-[11px] font-bold uppercase tracking-widest hover:text-blue-600 transition-colors"
              >
                Ienākt
              </RouterLink>
              <RouterLink
                to="/registracija"
                class="bg-black text-white px-8 py-3 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-blue-600 transition-all duration-700"
              >
                Reģistrēties
              </RouterLink>
            </template>
          </div>

        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { RouterLink, useRoute } from "vue-router";
import { ref, computed, onMounted, defineComponent, h } from "vue";
import api from "@/services/api";

const notifications = ref([]);
const showDropdown = ref(false);
const route = useRoute();
const hideHeader = computed(() => route.meta.hideHeader);

const isLoggedIn = ref(false);
const userRole = ref(null);

const unreadCount = computed(() =>
  notifications.value.filter((n) => !n.read_at).length
);

onMounted(async () => {
  isLoggedIn.value = !!localStorage.getItem("token");
  userRole.value = localStorage.getItem("role");
  if (isLoggedIn.value) fetchNotifications();
});

const fetchNotifications = async () => {
  try {
    const response = await api.get("/notifications");
    notifications.value = response.data;
  } catch (e) {
    // silent
  }
};

const markAllRead = async () => {
  try {
    await api.post("/notifications/read");
    notifications.value = notifications.value.map((n) => ({
      ...n,
      read_at: new Date().toISOString(),
    }));
  } catch (e) {
    // silent
  }
};

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("role");
  isLoggedIn.value = false;
  window.location.href = "/";
};

const toggleNotifications = () => {
  showDropdown.value = !showDropdown.value;
};

const formatDate = (date) =>
  new Date(date).toLocaleString("lv-LV", {
    day: "2-digit",
    month: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });

const NavLink = defineComponent({
  props: ["to", "label"],
  setup(props) {
    const route = useRoute();
    const active = computed(() => route.path === props.to);
    return () =>
      h(
        RouterLink,
        {
          to: props.to,
          class: [
            "text-[11px] font-bold uppercase tracking-[0.2em] transition-all duration-500 relative py-2",
            active.value ? "text-black" : "text-gray-400 hover:text-black",
          ],
        },
        {
          default: () => [
            props.label,
            h("span", {
              class: [
                "absolute bottom-0 left-0 h-[1.5px] bg-blue-600 transition-all duration-700",
                active.value ? "w-full" : "w-0",
              ],
            }),
          ],
        }
      );
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@500;700&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

nav, button, a {
  font-family: 'Inter', sans-serif;
}
</style>