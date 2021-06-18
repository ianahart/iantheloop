<template>
   <div class="profile_stats_follow_btn">
     <button @click="updateFollowStats">Follow</button>
   </div>
</template>


<script>

  import { mapState,  mapActions } from 'vuex';

  export default {

    name: 'FollowBtn',

    props: {

    },

    components: {

    },

    computed: {

    },

    methods: {

      ...mapActions('profile',
        [
          'UPDATE_FOLLOW_STATS'
        ]
      ),

      async updateFollowStats () {

        await this.UPDATE_FOLLOW_STATS();
      } ,
    }
  }
</script>

<style lang="scss">
  .profile_stats_follow_btn {
    button {
      background-color: transparent;
      border: 1px solid darken($primaryGray, 18);
      cursor: pointer;
      height: 30px;
      padding: 0.3rem 0.5rem;
      border-radius: 8px;
      color: gray;
      transition: 0.5s ease-out;

      &:hover {
        background-color: darken($primaryWhite, 5);
      }
    }
  }

</style>