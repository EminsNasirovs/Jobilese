<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] py-20 font-sans selection:bg-blue-50">
    <div class="max-w-3xl mx-auto px-6">
      
      <div class="mb-16 space-y-4">
        <div class="flex items-center gap-4 overflow-hidden mb-6">
          <span class="h-[1px] w-12 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.4em] text-gray-400">Jauna dalībnieka reģistrācija</span>
        </div>
        
        <h1 class="text-6xl md:text-8xl font-medium tracking-tighter leading-none italic font-serif">
          Pievienoties<span class="text-blue-600">.</span>
        </h1>
        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold max-w-md">
          Izveidojiet profilu, lai pilnvērtīgi izmantotu platformas sniegtās iespējas.
        </p>
      </div>

      <form class="space-y-12" @submit.prevent="register">
        
        <div class="space-y-8">
          <h3 class="section-title">Personas informācija</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="group space-y-1">
              <label class="input-label">Vārds</label>
              <input v-model="firstname" type="text" placeholder="Ievadiet vārdu" class="input-editorial" />
            </div>
            <div class="group space-y-1">
              <label class="input-label">Uzvārds</label>
              <input v-model="lastname" type="text" placeholder="Ievadiet uzvārdu" class="input-editorial" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="group space-y-1">
              <label class="input-label">E-pasts</label>
              <input v-model="email" type="email" placeholder="piemērs@pasts.lv" class="input-editorial" />
            </div>
            <div class="group space-y-1">
              <label class="input-label">Lietotājvārds</label>
              <input v-model="username" type="text" placeholder="lietotajvards123" class="input-editorial" />
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <h3 class="section-title">Identitāte</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="group space-y-1">
              <label class="input-label">Parole</label>
              <input v-model="password" type="password" placeholder="••••••••" class="input-editorial" />
            </div>
            <div class="group space-y-1">
              <label class="input-label">Dzimšanas datums</label>
              <input v-model="birth_date" type="date" class="input-editorial" />
            </div>
          </div>

          <div class="space-y-4">
            <label class="input-label">Dzimums</label>
            <div class="flex gap-8">
              <label v-for="opt in ['vīrietis', 'sieviete', 'cits']" :key="opt" class="flex items-center cursor-pointer group">
                <input type="radio" :value="opt" v-model="gender" class="sr-only" />
                <span class="radio-dot" :class="{ 'radio-dot-active': gender === opt }">
                  <span v-if="gender === opt" class="radio-inner"></span>
                </span>
                <span class="text-[10px] uppercase font-bold tracking-widest" :class="gender === opt ? 'text-black' : 'text-gray-300'">{{ opt }}</span>
              </label>
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <h3 class="section-title">Platformas loma</h3>
          <div class="group space-y-1">
            <label class="input-label">Jūsu statuss</label>
            <select v-model="role" class="input-editorial bg-transparent appearance-none cursor-pointer">
              <option value="" disabled>Izvēlieties statusu</option>
              <option value="bezdarbnieks">Kandidāts (Meklēju darbu)</option>
              <option value="uzņēmējs">Uzņēmējs (Piedāvāju darbu)</option>
            </select>
          </div>
        </div>

        <div v-if="role === 'uzņēmējs'" class="space-y-8 pt-4">
          <h3 class="section-title">Uzņēmuma informācija</h3>
          <div class="space-y-8">
            <div class="group space-y-1">
              <label class="input-label">Nosaukums</label>
              <input v-model="company_name" type="text" placeholder="SIA Uzņēmums" class="input-editorial" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
              <div class="group space-y-1">
                <label class="input-label">Reģistrācijas numurs</label>
                <input v-model="company_number" type="text" placeholder="4000..." class="input-editorial" />
              </div>
              <div class="group space-y-1">
                <label class="input-label">Juridiskā adrese</label>
                <input v-model="company_address" type="text" placeholder="Rīga, Brīvības iela..." class="input-editorial" />
              </div>
            </div>
          </div>
        </div>

        <div class="pt-12 space-y-6">
          <button type="submit" class="submit-btn group">
            Izveidot profilu
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="group-hover:translate-x-2 transition-transform duration-500">
              <path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <RouterLink to="/login" class="block w-full text-center text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 hover:text-black transition-colors">
            Esmu jau reģistrējies • Pieslēgties
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { RouterLink } from "vue-router";
import api from "../services/api.js";
import { useToast } from "@/composables/useToast";

const { error } = useToast();

const firstname = ref("");
const lastname = ref("");
const email = ref("");
const username = ref("");
const password = ref("");
const gender = ref("");
const birth_date = ref("");
const role = ref("");
const company_number = ref("");
const company_address = ref("");
const company_name = ref("");

const register = async () => {
  try {
    const response = await api.post("/register", {
      firstname: firstname.value,
      lastname: lastname.value,
      email: email.value,
      username: username.value,
      password: password.value,
      gender: gender.value,
      birth_date: birth_date.value,
      role: role.value,
      company_name: role.value === "uzņēmējs" ? company_name.value : null,
      company_number: role.value === "uzņēmējs" ? company_number.value : null,
      company_address: role.value === "uzņēmējs" ? company_address.value : null,
    });
    localStorage.setItem("token", response.data.token);
    localStorage.setItem("role", response.data.user.role);
    localStorage.setItem("user_id", response.data.user.id);
    window.location.href = "/profile";
  } catch (err: any) {
    error(err.response?.data?.message || "Neizdevās reģistrēties");
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap');

.font-serif { font-family: 'DM Serif Display', serif; }

.section-title {
  font-size: 10px;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.3em;
  color: #2563EB;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  padding-bottom: 0.5rem;
}

.input-label {
  display: block;
  font-size: 9px;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: #9CA3AF;
  transition: color 150ms ease;
}

.group:focus-within .input-label {
  color: #2563EB;
}

.input-editorial {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(0,0,0,0.1);
  padding-top: 1rem;
  padding-bottom: 1rem;
  font-size: 1.125rem;
  outline: none;
  transition: all 150ms ease;
  font-weight: 300;
}

.input-editorial:focus {
  border-bottom-color: #000000;
}

.radio-dot {
  width: 1rem;
  height: 1rem;
  border: 1px solid black;
  border-radius: 9999px;
  margin-right: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 150ms ease;
}

.radio-dot-active {
  background-color: black;
}

.radio-inner {
  width: 0.25rem;
  height: 0.25rem;
  background-color: white;
  border-radius: 9999px;
}

.submit-btn {
  width: 100%;
  background-color: black;
  color: white;
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  transition: all 500ms ease;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.submit-btn:hover {
  background-color: #2563EB;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0.2;
  cursor: pointer;
}
</style>