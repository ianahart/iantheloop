<template>
  <div class="create_profile_sidebar__container">
    <header>
      <p>Profile Setup</p>
      <h4>{{ userName }}</h4>
    </header>
    <SidebarSection
      sectionTitle="Identity"
      path="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
      :errorsPresent="this.$store.state.identity.errorsPresent"
    />
   <SidebarSection
      sectionTitle="General Details"
      path="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      :errorsPresent="this.$store.state.generalDetails.errorsPresent"
    />
    <SidebarSection
      sectionTitle="Customize"
      path="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
      :errorsPresent="this.$store.state.customize.errorsPresent"
    />
    <SidebarSection
      sectionTitle="About Details"
      path="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
      :errorsPresent="this.$store.state.aboutDetails.errorsPresent"
    />
    <SidebarSection
      sectionTitle="Work Details"
      path="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
      :errorsPresent="this.$store.state.workDetails.errorsPresent"

    />
    </div>
</template>

<script>


  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import SidebarSection from './SidebarSection.vue';

  export default {

    name: 'Sidebar',

    props: {

    },

    components: {

      SidebarSection,
    },

    computed: {

      ...mapGetters('user',
        [
        'userName'
        ]
      ),
    },

  }
</script>

<style lang="scss">


  .create_profile_sidebar__container {
    background-color: #fff;
    box-sizing: border-box;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
    min-width: 320px;
    grid-row: 1 / 5;

    header {
      margin: 0.5rem auto;
      color: darken($primaryGray, 15);
      font-size: 0.9rem;
      text-align: center;
      margin-bottom: 1.4rem;
      padding: 1rem;

      h4 {
        color: $primaryBlack;

      }

    }
  }


  @media(max-width: 1090px) {

    .create_profile_sidebar__container {
      grid-row: auto;
    }
  }

  @media(max-width: 862px) {

    // .create_profile_sidebar__container {
    //   grid-column: 1;
    // }

  }

</style>