<template>
    <img class="user_profile__profile_image" :src="getBaseProfile.profile_picture" />
</template>

<script>

  import { mapGetters } from 'vuex';

  export default {

    name: 'ProfileImage',

    props: {

    },

    components: {

    },

    computed: {

    ...mapGetters('profile',
        [
          'getBaseProfile'
        ]
      ),
    }
  }
</script>


<style lang="scss">

  .user_profile__profile_image {
      border-radius: 50%;
      position: absolute;
      height: 200px;
      width: 200px;
      bottom: -120px;
      display: inline-block;
    }

</style>