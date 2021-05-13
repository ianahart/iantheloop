<template>
  <div class="profile__navigation">
    <ProfileLink
      v-for="link in profileNavigation" :key="link.id"
      :name="link.name"
      :text="link.text"
    />
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations } from 'vuex';

  import ProfileLink from './ProfileLink.vue';

  export default {

    name: 'SubNavigation',

    props: {

    },

    components: {

      ProfileLink,
    },

    computed: {
      ...mapState('profile',
        [
          'profileNavigation'
        ]
      ),
    },

    methods: {

    }
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