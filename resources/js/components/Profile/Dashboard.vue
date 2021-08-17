<template>
  <div class="profile_dashboard__container">
    <div class="profile_dashboard__row">
      <WorkPreview />
      <ProfileNavigation />
    </div>
  </div>
</template>

<script>

  import ProfileNavigation from './ProfileNavigation.vue';
  import WorkPreview from './WorkPreview.vue';

  export default {

    name: 'Dashboard',

    components: {
      ProfileNavigation,
      WorkPreview,
    },
  }
</script>

<style lang="scss">

  .profile_dashboard__container {
    max-width: 880px;
    border-radius: 8px;
    margin: 0 auto;
    margin-top: 1rem;
    margin-bottom: 2.2rem;
    background-color: $primaryGray;
    box-sizing: border-box;
  }

  .profile_dashboard__row {
    box-sizing: border-box;
    padding: 1rem;
    max-width: 400px;
    margin-right: auto;
    margin-left: 8rem;
  }

  @media(max-width: 600px) {

    .profile_dashboard__row {
      margin-right:  auto;
      margin-left:1rem;
    }
  }

</style>