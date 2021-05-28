<template>
  <div @click="goToFollowers" class="profile_stats_followers">
      <p>{{ followersCount }}<p>
      <p>followers</p>
    </div>
</template>

<script>

  import { mapState } from 'vuex';

  export default {

    name: 'Followers',

    props: {
      followersCount: Number,
    },

    computed: {
      ...mapState('profile',
        [
          'viewingUserId'
        ]
      ),
    },

    methods: {
     goToFollowers () {

        this.$router.push({ name: 'Followers', params: { id: this.viewingUserId.toString() } })
      },
    }
  }

</script>

<style lang="scss">


  .profile_stats_followers {
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