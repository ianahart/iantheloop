<template>
 <div class="settings_sidebar_container">
   <header>
     <h1>Settings</h1>
     <div class="settings_header_underline"></div>
   </header>
   <section class.stop="settings_sidebar_options">
    <div @click="changeCurrentSidebarOption('general')" :class="`settings_sidebar_option ${this.optionClass('general')}`">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <h2>General</h2>
    </div>
    <div @click.stop="changeCurrentSidebarOption('security')" :class="`settings_sidebar_option ${this.optionClass('security')}`">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
    </svg>
    <h2>Security &#38; Login</h2>
    </div>
    <div @click.stop="changeCurrentSidebarOption('privacy')" :class="`settings_sidebar_option ${this.optionClass('privacy')}`">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
      <h2>Privacy</h2>
    </div>
  </section>
 </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';

  export default {
    name: 'Sidebar',


    computed: {
      ...mapState('settings',
        [
          'currentSidebarOption',
        ]
      ),
    },

    methods: {
      ...mapMutations('settings',
        [
          'SET_CURRENT_SIDEBAR_OPTION'
        ]
      ),

       changeCurrentSidebarOption(option) {
         this.SET_CURRENT_SIDEBAR_OPTION(option);
       },

       optionClass(option) {
          return option.toLowerCase() === this.currentSidebarOption ? 'settings_active_sidebar_option' : 'settings_non_active_sidebar_option';
       },
    },
  }

</script>

<style lang="scss">
  .settings_sidebar_container {
    box-sizing: border-box;
    background-color: #3a3a3a;
    flex-grow: 1;
    height: 100%;
    width: 350px;
    max-width: 350px;
    min-width: 350px;
    header {
      h1 {
        color: $primaryWhite;
        margin: 0.17rem 0;
        margin-top: 2rem;
        font-family: 'Secular One',sans-serif;
        text-align: left;
        padding-left: 0.5rem;
        font-size: 1.3rem;
      }
    }
    svg {
      color: $primaryWhite;
      width: 28px;
      height: 28px;
      background-color: transparent;
    }
  }

  .settings_header_underline {
    width: 100%;
    background-color: #323131;
    height: 1px;
  }

  .settings_sidebar_options {
    box-sizing: border-box;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .settings_sidebar_option {
    cursor: pointer;
    border-bottom: 1px solid #444343;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin: 0.5rem 0;
    width:100%;
    h2 {
      font-size:1rem;
      margin-left: 0.5rem;
      font-family: 'Open Sans',sans-serif;
    }
  }

  .settings_active_sidebar_option {
     h2 {
      color: darken(gray, 5);
    }
    svg {
      color: darken(gray, 5);
    }
    &:hover{
      svg {
        color: darken(gray, 5);
      }
    }
  }
  .settings_non_active_sidebar_option {
    h2 {
      color: darken($primaryWhite, 5);
    }
    &:hover{
      svg {
        color: lighten($themePink, 5);
      }
    }
  }
  @media(max-width:900px) and (min-width: 601px) {
    .settings_sidebar_container{
      max-width: 95%;
      min-width: 95%;
      width: 95%;
      margin: 0 auto;
      flex-grow: 0;
    }
  }

  @media(max-width: 600px) {
    .settings_sidebar_container {
      box-sizing: border-box;
      max-width: 100%;
      min-width: 100%;
      width:100%;
    }
  }
</style>
