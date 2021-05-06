<template>
    <svg @click="closeMenu" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 close__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</template>




<script>


  export default {

    name: 'CloseIcon',



    methods: {

      closeMenu () {

        this.$store.commit('hamburgerMenu/HIDE_MENU', false);
      }
    },
  }

</script>


<style lang="scss">
/*
  IMPORTS
*/



  .close__icon {
    width: 24px;
    cursor: pointer;
    color: $primaryWhite;
  }
</style>

