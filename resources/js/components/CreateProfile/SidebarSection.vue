<template>
  <div
    :class="`create_profile_sidebar__section ${currentForm === sectionTitle.toLowerCase() ? 'sidebar_current__form': ''}`"
    @click="selectForm(sectionTitle)"
   >
     <span class="sidebar_section__error" v-if="errorsPresent">*</span>
     <SidebarIcon
        :path="path"
     />
    <h3>{{ sectionTitle }}</h3>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import SidebarIcon from './SidebarIcon.vue';

  export default {

    name: 'Sidebar',

    props: {

      sectionTitle: String,
      path: String,
      errorsPresent: Boolean,
    },

    components: {

      SidebarIcon,
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

      ...mapState('createProfile',
        [
          'currentForm'
        ]
      ),

    },

    methods: {
      ...mapMutations('createProfile',
        [
          'SET_CURRENT_FORM'
        ]
      ),


      selectForm (section) {

        this.SET_CURRENT_FORM(section.toLowerCase());
      },
    },
  }
</script>

<style lang="scss">

  .create_profile_sidebar__section {
    cursor: pointer;
    text-align: center;
    margin: 0.2rem auto;


    h3 {
      color: $primaryBlack;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: inline-block;
      text-align: left;
      font-weight: 200;
      width: 170px;
      font-size: 1rem;

    }
  }

  .sidebar_current__form {
    background-color: $themeLightBlue;

    h3 {
      color: #fff;
    }

    svg {
      color: #fff;
    }
  }

  .sidebar_section__error {
    color: $error;
    font-size: 1.2rem;
    font-weight: bold;
  }

</style>