<template>
  <div ref="settings" class="settings_page_container">
    <Sidebar />
    <div class="settings_showcase_container">
      <Showcase />
    </div>
  </div>
</template>

<script>
  import { mapMutations, mapActions, mapGetters } from 'vuex';

  import Sidebar from '../components/Settings/Sidebar.vue';
  import Showcase from '../components/Settings/Showcase.vue';


 export default {
   name: 'Settings',
   components: {
     Sidebar,
     Showcase,
   },

   beforeDestroy() {
      this.RESET_SETTINGS_MODULE();
   },

   methods: {
     ...mapMutations('settings',['RESET_SETTINGS_MODULE']),
   },
   computed: {
     ...mapGetters('user', ['getUserSettingsId']),
   },

}
</script>

<style lang="scss">
  .settings_page_container {
    box-sizing: border-box;
    background-color: #3a3a3a;
    height: 100%;
    min-height: 100%;
    display: flex;
  }
  .settings_showcase_container {
    box-sizing: border-box;
    background-color: #000;
    flex-grow: 2;
    height: 100%;
  }

  @media(max-width:900px) {
    .settings_page_container {
      flex-direction: column;
    }
  }
</style>