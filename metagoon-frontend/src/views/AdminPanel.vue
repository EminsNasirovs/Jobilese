<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-5xl mx-auto">
      <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">
        Admin Panel
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="card in cards"
          :key="card.title"
          class="p-5 rounded-2xl bg-white dark:bg-gray-800 shadow hover:shadow-lg transition"
        >
          <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">
            {{ card.title }}
          </h2>
          <p class="text-gray-500 dark:text-gray-400 mb-3">
            {{ card.description }}
          </p>
          <RouterLink
            :to="card.link"
            class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline"
          >
            Go →
          </RouterLink>
        </div>
      </div>

      <div class="mt-10">
        <button
          @click="logout"
          class="px-6 py-2 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition"
        >
          Log out
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";

const cards = [
  {
    title: "Manage Users",
    description: "View, edit or delete users in the system.",
    link: "/admin/users",
  },
  {
    title: "Manage Vacancies",
    description: "Approve, edit, or remove job vacancies.",
    link: "/admin/vacancies",
  },
  {
    title: "Reports & Logs",
    description: "View system statistics and recent activities.",
    link: "/admin/reports",
  },
];

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("role");
  window.location.href = "/";
};
</script>
