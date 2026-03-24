<script setup>
import Header from './components/Header.vue'
import Toast from './components/Toast.vue'
</script>

<template>
  <Header v-if="!$route.meta.hideHeader" />
  <router-view/>
  <Toast />
</template>

