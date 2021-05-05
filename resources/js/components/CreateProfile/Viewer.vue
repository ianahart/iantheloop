<template>
  <div class="create_profile_viewer__container">
    <h3>{{ getCurrentForm }}</h3>
    <div class="create_profile_viewer__content">
      <GeneralDetails
        v-if="getCurrentForm === 'General Details'"
      />
      <AboutDetails
        v-if="getCurrentForm === 'About Details'"
      />
      <Identity
        v-if="getCurrentForm === 'Identity'"
      />
      <WorkDetails
        v-if="getCurrentForm === 'Work Details'"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import Identity from './forms/Identity.vue';
  import GeneralDetails from './forms/GeneralDetails.vue';
  import AboutDetails from './forms/AboutDetails.vue';
  import WorkDetails from './forms/WorkDetails.vue';

  export default {

    name: 'Viewer',

    props: {

    },

    components: {

      Identity,
      GeneralDetails,
      AboutDetails,
      WorkDetails,
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

      ...mapGetters('createProfile',
        [
          'getCurrentForm'
        ]
      ),
    },


    methods: {

    },
  }
</script>

<style lang="scss">

  @import '../../../sass/general/_variables.scss';

  .create_profile_viewer__container {
    grid-column:  2 / 5;
    grid-row:  1/ 4;
    box-sizing: border-box;
    height: 100%;
    width: 100%;
    max-width: 700px;
    min-width: 400px;

    h1 {
      margin: 0;
    }
    h3 {

      box-sizing: border-box;
      margin: 0;
      padding-bottom: 0.5rem;
      height: auto;
      color: lighten($mainInputLabel, 5);
      letter-spacing: 0.15px;
    }

  }

  .create_profile_viewer__content {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    box-sizing: border-box;
    padding:  1rem;
    height: 100%;
    width: 100%;

  }

  @media(max-width: 1090px) {

    .create_profile_viewer__container {
      grid-row: auto;
      grid-column: auto;
    }
  }


  @media(max-width: 862px) {

    .create_profile_viewer__container {
      grid-column: 1;
    }
  }

  @media(max-width: 600px) {
    .create_profile_viewer__container {

      min-width: 100%;
    }

    .create_profile_viewer__content {
      min-width: 100%;
    }
  }

</style>