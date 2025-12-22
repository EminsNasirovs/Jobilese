<template>
  <div class="min-h-screen bg-[#FDFDFC] text-[#111111] flex items-center justify-center font-sans selection:bg-blue-50">
    <div class="w-full max-w-lg px-6">
      
      <div class="mb-12 text-center space-y-4">
        <div class="flex items-center justify-center gap-3 overflow-hidden mb-6">
          <span class="h-[1px] w-8 bg-black"></span>
          <span class="text-[10px] font-bold uppercase tracking-[0.4em] text-gray-400">Piekļuve portālam</span>
          <span class="h-[1px] w-8 bg-black"></span>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-medium tracking-tighter leading-none italic font-serif">
          Pieslēgties<span class="text-blue-600">.</span>
        </h1>
        <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">
          Ievadiet datus, lai turpinātu darbu
        </p>
      </div>

      <form class="space-y-8" @submit.prevent="login">
        
        <div class="space-y-2 group">
          <label for="username" class="block text-[10px] uppercase font-bold tracking-widest text-gray-400 group-focus-within:text-blue-600 transition-colors">
            Lietotājvārds
          </label>
          <input
            id="username"
            type="text"
            v-model="username"
            placeholder="ievadiet vārdu"
            class="w-full bg-transparent border-b border-black/[0.08] py-4 text-xl outline-none focus:border-black transition-all placeholder:text-gray-100 font-light"
            required
          />
        </div>

        <div class="space-y-2 group">
          <label for="password" class="block text-[10px] uppercase font-bold tracking-widest text-gray-400 group-focus-within:text-blue-600 transition-colors">
            Parole
          </label>
          <input
            id="password"
            type="password"
            v-model="password"
            placeholder="••••••••"
            class="w-full bg-transparent border-b border-black/[0.08] py-4 text-xl outline-none focus:border-black transition-all placeholder:text-gray-100 font-light"
            required
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center cursor-pointer group">
            <input
              type="checkbox"
              class="h-4 w-4 border-black transition cursor-pointer accent-black"
            />
            <span class="ml-2 text-[10px] uppercase font-bold tracking-widest text-gray-400 group-hover:text-black">Atcerēties mani</span>
          </label>
          
          <RouterLink
            to="/forgot-password"
            class="text-[10px] uppercase font-bold tracking-widest text-gray-400 hover:text-blue-600 transition underline underline-offset-4"
          >
            Aizmirsi paroli?
          </RouterLink>
        </div>

        <div class="pt-6">
          <button
            type="submit"
            class="group w-full bg-black text-white py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-blue-600 transition-all duration-500 shadow-2xl flex items-center justify-center gap-4"
          >
            Turpināt
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="group-hover:translate-x-2 transition-transform duration-500">
              <path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>

        <p class="text-center pt-4">
          <span class="text-[10px] uppercase font-bold tracking-widest text-gray-300">Tev nav konta? </span>
          <RouterLink
            to="/registracija"
            class="text-[10px] uppercase font-bold tracking-widest text-black hover:text-blue-600 transition-colors border-b border-black"
          >
            Izveidot profilu
          </RouterLink>
        </p>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { RouterLink } from "vue-router";
import api from "../services/api.js";

const username = ref("");
const password = ref("");

const login = async () => {
  try {
    const response = await api.post("/login", {
      username: username.value,
      password: password.value,
    });
    localStorage.setItem("role", response.data.user.role);
    localStorage.setItem("token", response.data.token);
    window.location.href = "/profile";
  } catch (err) {
    console.error(err.response?.data || err);
    alert("Nepareizs lietotājvārds vai parole");
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&family=Inter:wght@300;400;700&display=swap');

.font-serif {
  font-family: 'DM Serif Display', serif;
}

div {
  font-family: 'Inter', sans-serif;
}

input::placeholder {
  opacity: 1;
}
</style>