<template>
  <div class="profile_stats_following">
      <p>107<p>
      <p>following</p>
    </div>
</template>

<script>

  export default {

    name: 'Following',

    props: {

    },

    components: {

    }
  }

</script>

<style lang="scss">


  .profile_stats_following {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

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