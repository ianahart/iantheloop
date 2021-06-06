<template>
  <div v-if="modalFormIsOpen" class="profile_modal_form__modal">
  </div>
</template>


<script>

  import { mapState, mapGetters,  mapMutations  ,mapActions } from 'vuex';

  export default {

    name: 'ModalForm',


    components: {


    },

    mounted () {

    },
    beforeDestroy () {

    },

    computed: {

      ...mapState('profileWall',
        [
          'modalFormIsOpen'
        ]
      ),
    },

    methods : {

      resetModalForm () {
      },
    },
  }
</script>

<style lang="scss">

  .profile_modal_form__modal {
    background-color: rgba(0, 0, 0, 0.8);
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 15;
  }
</style>
