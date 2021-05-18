<template>
  <div class="edit_profile_work__container">
    <h1>Work Fields</h1>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  export default {

    name: 'Work',

    props: {

    },

    components: {

    },

    // mounted() {
    //   console.log('Work Tab: ', this.getEditGroup);
    // },


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