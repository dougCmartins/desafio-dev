<template>
  <main>
    <router-view/>
  </main>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'App',
  components: {}
});
</script>

<style lang="scss">
@import "@/assets/scss/_main.scss";
</style>
