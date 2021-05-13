<template>
    <img class="user_profile__background_image" :src="getBaseProfile.background_picture" />
</template>

<script>

  import { mapGetters } from 'vuex';

  export default {

    name: 'BackgroundImage',

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

.user_profile__background_image {
  border-radius: 8px;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
}
</style>