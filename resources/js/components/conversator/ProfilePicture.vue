<template>
  <div @click="goToProfile(userId)" class="contact_profile_picture">
    <img v-if="profilePicture" :src="profilePicture" :alt="alt" />
    <DefaultProfileIcon
      v-else
      className="default_profile_image_rel_sm"
    />
    <div v-if="status" :class="`conversator_online_indicator ${this.onlineIndicator}`"></div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {

    name: 'ProfilePicture',
    props: {
      profilePicture: String,
      alt: String,
      status: String,
      userId: Number,
    },
    components: {
      DefaultProfileIcon,
    },

    data () {
      return {

      }
    },

    mounted () {

    },

    computed: {
      onlineIndicator() {

        return this.status === 'online' ? 'conversator_online_indicator_online' : 'conversator_online_indicator_offline';
      }
    },

    methods: {

      goToProfile(userId) {
          if (this.$route.params.id !== userId) {
            this.$router.push({ name: 'Profile', params: { id: userId }});
          }
      }
    },
  }
</script>

<style lang="scss">

  .contact_profile_picture {
    position:relative;
    cursor: pointer;

    img {
      border-radius: 50%;
      width: 28px;
      height: 28px;
    }
    svg {
      border-radius: 50%;
      width: 28px;
      height: 28px;
      background-color: $themeLightBlue;
      color: $themePink;
    }

    .conversator_online_indicator {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      border: 2px solid black;
      position: absolute;
      bottom: 6px;
      right:0;
    }

    .conversator_online_indicator_online {
        background-color: limegreen;
    }
       .conversator_online_indicator_offline {
        background-color: gray;
    }
  }


</style>