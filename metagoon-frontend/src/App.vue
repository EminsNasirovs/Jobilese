<script setup>

import Header from './components/Header.vue'
import Home from './views/Home.vue';
import Vakances from './views/Vakances.vue';
import Login from './views/Login.vue';
</script>

<template>
  <Header v-if="!$route.meta.hideHeader" />
  <router-view/>

</template>

