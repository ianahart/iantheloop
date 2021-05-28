<template>
  <div @click="goToFollowing" class="profile_stats_following">
      <p>{{ followingCount }}<p>
      <p>following</p>
    </div>
</template>

<script>

  import { mapState } from 'vuex';

  export default {

    name: 'Following',

    props: {
      followingCount: Number,
    },

    components: {

    },

    computed: {

      ...mapState('profile',
        [
          'viewingUserId'
        ]
      ),
    },

    methods: {
      goToFollowing () {

        this.$router.push({ name: 'Following', params: { id: this.viewingUserId.toString() } })
      },
    }

  }

</script>

<style lang="scss">


  .profile_stats_following {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    cursor: pointer;

    p {
      margin: 0;
      color: $primaryBlack;
      font-weight: bold;

      &:last-of-type {
        margin-top: 0.3rem;
        font-size: 0.8rem;
        color: darken($primaryGray, 18);
        font-weight: normal;
      }
    }
  }

</style>