<template>
    <img :class="className" :src="src" />
</template>

<script>

  import { mapGetters } from 'vuex';

  export default {

    name: 'ProfileImage',

    props: {
      className: String,
      src:String,
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


 .default_profile_image {

   background-color: $primaryGray;
 }

</style>