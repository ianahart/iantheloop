<template>
  <div class="profile_stats_requested_btn">
    <button @click="openCancelRequest">Requested...</button>
  </div>
</template>


<script>

  export default {

    name: 'RequestedBtn',

    props: {

    },

    components: {

    },

    computed: {

    },

    methods: {

      openCancelRequest () {

        console.log('Canceling Request: ');
      },
    },
  }

</script>



<style lang="scss">
  .profile_stats_requested_btn {
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