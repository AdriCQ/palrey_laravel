<template>
  <base-layout>
    <div class="text-center app-container">
      <div class="app-title">PalRey</div>
      <!-- <div class="text-content">Nueva version de la aplicación disponible</div> -->

      <div class="mt-3">
        <a
          :href="`${url}/storage/PalReyClient-v1.0.5.apk`"
          class="btn btn-outline-primary mx-1 my-1"
        >
          <!-- <i class="fa fa-fw fa-android" /> -->
          DESCARGAR
        </a>
      </div>
    </div>
  </base-layout>
</template>

<script>
import BaseLayout from "@/Layouts/BaseLayout.vue";
import ContactUsButtons from "@/Components/Widgets/ContactUsButtons.vue";

export default {
  components: {
    BaseLayout,
    ContactUsButtons,
  },
  computed: {
    url() {
      return "https://palrey.nat.cu";
    },
    appToken() {
      return "2|$2y$10$xhx.eDmAyd.NuMsTHkfQYuycp4YC1dxIGHWXN/2RjZvu0AGbHRltu";
    },
  },
};
</script>

<style>
.app-container {
  top: 50%;
  left: 50%;
  position: absolute;
  transform: translate(-50%, -50%);
  color: rgba(0, 0, 0, 0.7);
}
.app-title {
  font-size: 2.5rem;
}
.app-title2 {
  font-size: 1.5rem;
}
</style>
