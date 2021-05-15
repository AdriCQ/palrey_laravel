<template>
  <div class="contact-us-buttons">
    <h5 class="mb-1">Contáctenos</h5>
    <a
      v-for="(btn, key) in buttons"
      :key="`button-${key}`"
      :href="btn.link"
      class="btn btn-outline-primary mx-1 my-1 w-40"
    >
      <i :class="`fa fa-fw ${btn.icon}`" />
      {{ btn.label }}
    </a>
  </div>
</template>

<script>
export default {
  data() {
    return {
      buttons: [
        {
          label: "Telegram",
          icon: "fa-send-o",
          link: "#",
        },
        {
          label: "Whatsapp",
          icon: "fa-whatsapp",
          link: "#",
        },
        {
          label: "Facebook",
          icon: "fa-facebook",
          link: "#",
        },
        {
          label: "Correo",
          icon: "fa-envelope-o",
          link: "#",
        },
      ],
    };
  },
};
</script>

<style>
</style>