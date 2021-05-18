<template>
  <div class="edit_profile_pictures__container">
    <h1>Picture Fields</h1>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  export default {

    name: 'Pictures',

    props: {

    },

    components: {

    },

    mounted() {
      console.log('Pictures Tab: ', this.getEditGroup);
    },


    beforeDestroy() {

    },

    computed: {

      ...mapGetters('profileEdit',
        [
          'getEditGroup'
        ]
      ),
    },

    methods: {

    },
  }
</script>

<style lang="scss">

</style>