<template>
  <div class="about_details_interests__container">
    <Interest
      v-for="interest in interests"
      :key="interest.id"
      :name="interest.name"
      :id="interest.id"
    />
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import Interest from './Interest.vue';

  export default {

    name: 'Interests',

    props: {
      interests: Array,
    },

    components: {

      Interest,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },


    computed: {

      ...mapGetters('aboutDetails',
          [
            'getInterests'
          ]
        ),
    },

    methods: {

    },
  }

</script>

<style lang="scss">

//  @import '../../../../sass/general/_variables.scss';

.about_details_interests__container {
  display: flex;
  flex-wrap: wrap;
}

</style>