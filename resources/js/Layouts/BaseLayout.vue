<template>
  <div>
    <header>
      <!-- <app-navbar /> -->
    </header>

    <main class="container">
      <slot></slot>
    </main>
  </div>
</template>

<script>
import AppNavbar from "@/Components/Layout/Navbar.vue";
export default {
  components: {
    AppNavbar,
  },
};
</script>

<style>
</style>