<template>
  <div ref="settings" class="settings_page_container">
    <Sidebar />
    <div :class="`settings_showcase_container ${this.showcaseBGColor}`">
      <Showcase />
    </div>
  </div>
</template>

<script>
  import { mapState, mapMutations } from 'vuex';

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
     ...mapState('settings', ['isBlockListOpen', 'security']),
     showcaseBGColor() {
       return this.isBlockListOpen || this.security.is_proceed_modal_open ? 'settings_showcase_modal_color' : 'settings_showcase_default_color';
     }
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
    flex-grow: 2;
    height: 100%;
  }

  .settings_showcase_default_color {
    background-color: #000;
  }
  .settings_showcase_modal_color {
    background-color: rgba(225, 225, 225, 0.6);
  }

  @media(max-width:900px) {
    .settings_page_container {
      flex-direction: column;
    }
  }
</style>