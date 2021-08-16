<template>

  <div class="edit_profile_form__controls">

    <div class="edit_profile_form_control__container">
      <button @click="updateProfile">Save Changes</button>
      <button @click="clearProfile">Clear</button>
    </div>
  </div>
</template>


<script>

  import { mapGetters, mapState, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'FormControls',

    computed: {

        ...mapState('profileEdit',
          [
            'formErrors',
            'isUpdated',
            'userId',
          ]
        ),
    },

    beforeDestroy() {

        this.RESET_MODULE();
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'CLEAR_ERRORS',
          'CLEAR_FORM',
          'RESET_MODULE',
        ]
      ),

      ...mapActions('profileEdit',
        [
          'UPDATE_PROFILE'
        ]
      ),

      async updateProfile () {

        try {
          this.CLEAR_ERRORS();

          await this.UPDATE_PROFILE();

          if (this.isUpdated) {
            this.$router.push(`/profile/${this.userId}`);
          }

        } catch (e) {


        }
      },

      clearProfile () {
        this.CLEAR_FORM();

      },
    }
  }
</script>

<style lang="scss">

.edit_profile_form__controls {
  box-sizing: border-box;
  width: 400px;
  background-color: darken($primaryGray, 2);
  border-radius: 8px;
  padding : 2rem;
  margin: 2rem auto;
  display: flex;
  justify-content: center;
}

.edit_profile_form_control__container {
  display: flex;
  justify-content: space-evenly;
  button {
    border: none;
    border-radius: 8px;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: all 0.3s ease-out;
    height: 35px;
    margin: 0 2rem;
    padding: 0.2rem 0.3rem;
    width: 120px;

    &:first-of-type {
      background-color: $themeBlue;
      color: darken($primaryWhite ,4);

      &:hover {
        background-color: lighten($themeBlue, 5);
      }
    }
      &:last-of-type {
      background-color: $primaryGray;
      color: darken($primaryBlack ,4);

      &:hover {
        background-color: darken($primaryGray, 5);
      }
    }
  }
}

@media (max-width: 600px) {

  .edit_profile_form_control__container  {
    display: flex;
    justify-content: space-evenly;
  }

  .edit_profile_form__controls {

    width: 90%;
    margin: 0 auto;
  }
}

</style>