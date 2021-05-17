<template>
  <div class="profile__navigation">
    <ProfileLink
      v-for="link in getProfileLinks" :key="link.id"
      :name="link.name"
      :text="link.text"
      :params="link.params"
    />
  </div>
</template>

<script>

  import { mapState, mapGetters } from 'vuex';

  import ProfileLink from './ProfileLink.vue';

  export default {

    name: 'SubNavigation',

    components: {

      ProfileLink,
    },
    computed: {

      ...mapGetters('profile',
        [
          'getProfileLinks'
        ]
      ),
    },
  }
</script>

<style lang="scss">
  .profile__navigation {
    display: flex;
    justify-content: space-evenly;
  }

  @media(max-width:600px) {
    .profile__navigation {
      justify-content: flex-start;
    }
  }
</style>