<template>
  <div class="create_profile_triggers__container">
    <div class="create_profile_triggers__flex">
      <button @click="createProfile" class="button__lg">Create</button>
      <button @click="clearValues" class="button__lg">Reset Values</button>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'Triggers',


    computed: {
      ...mapGetters('user',
          [
            'getProfileStatus'
          ]
        ),
    },

    methods: {

      ...mapMutations('createProfile',
        [
          'RESET_MODULE',
          'SET_LOADER',
        ]
      ),
      ...mapActions('createProfile',
        [
          'CREATE_PROFILE'
        ]
      ),

    async createProfile() {
        this.$store.commit('generalDetails/CLEAR_ERROR_MSGS');
        this.$store.commit('aboutDetails/CLEAR_ERROR_MSGS');
        this.$store.commit('workDetails/CLEAR_ERROR_MSGS');
        this.$store.commit('identity/CLEAR_ERROR_MSGS');
        this.$store.commit('customize/CLEAR_ERROR_MSGS');

        this.SET_LOADER(true);
        await this.CREATE_PROFILE();



        if (this.getProfileStatus) {

          this.$router.push('/');
          this.SET_LOADER(false);
        }
    },

    clearValues () {

      this.RESET_MODULE();

        this.$store.commit('generalDetails/CLEAR_VALUES');
        this.$store.commit('aboutDetails/CLEAR_VALUES');
        this.$store.commit('workDetails/CLEAR_VALUES');
        this.$store.commit('identity/CLEAR_VALUES');
        this.$store.commit('customize/CLEAR_VALUES');

    }
    },
  }
</script>

<style lang="scss">

  @import '../../../sass/general/_variables.scss';


// Had to put class inside due to specificity
.create_profile_triggers__container {
    grid-row: 1 /2;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;

  .create_profile_triggers__flex {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    button {
      text-transform: capitalize;
      box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
      font-size: 1rem;
      &:last-of-type {
        background-color: $primaryGray;
        color: $primaryBlack;
      }
    }
  }
}



   @media(max-width: 1204px) {

    .create_profile_triggers__container {
      grid-row: auto;
      grid-column: 2;
    }
  }


  @media(max-width: 862px) {

    .create_profile_triggers__container {
      grid-column:  1;
      grid-row: 5;

      margin: 0 auto;
    }
  }

</style>