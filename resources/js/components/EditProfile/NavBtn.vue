<template>
  <li
    @click="changeSection(text)"
      :class="`edit_profile_nav__btn ${isActiveStyle}`"
  >
    {{ text }}
  </li>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  export default {

    name: 'SectionNav',

    props: {
      text: String,
      id: Number,
    },

    components: {

    },

    computed: {

      ...mapState('profileEdit',
        [
          'currentWindow',
          'allWindow'
        ]
      ),

      isActiveStyle () {

          return this.currentWindow === this.text ? 'edit_profile_nav_active__btn ' : '';
      }
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'CHANGE_WINDOW'
        ]
      ),

      changeSection(section) {

        this.CHANGE_WINDOW(section);
      },
    }
  }

</script>

<style lang="scss">

.edit_profile_nav__btn {
  margin: 0 1.5rem;
  color: darken($primaryWhite, 5);
  font-family: 'Open Sans' sans-serif;
  transition: background-color, color 0.25s ease-out;
  cursor: pointer;
  display: flex;
  align-items: center;
  box-sizing: border-box;
  height: 100%;
  padding: 0 1rem;
}

.edit_profile_nav_active__btn {
  background-color: $primaryGray;
  color: $primaryBlack;
  font-weight: 600;
}

</style>