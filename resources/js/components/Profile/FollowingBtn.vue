

<template>
   <div @click="toggleModal" class="profile_stats_following_btn">
     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
    </svg>
     <p>Following</p>
   </div>
</template>


<script>

  import { mapState,  mapMutations } from 'vuex';

  export default {

    name: 'FollowBtn',

    props: {

    },

    components: {

    },

    computed: {

    },

    methods: {

      ...mapMutations('profile',
        [
          'TOGGLE_MODAL'
        ]
      ),

      toggleModal () {

        this.TOGGLE_MODAL();
      } ,
    }
  }
</script>

<style lang="scss">
  .profile_stats_following_btn {
    box-sizing: border-box;
    border: 1px solid darken($primaryGray, 18);
    padding: 0.3rem 0.5rem;
    border-radius: 8px;
    transition: 0.5s ease-out;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 30px;

    &:hover {
      background-color: darken($primaryWhite, 5);
    }

    p {
      color: gray;
      font-size:0.8rem;
      margin: 0;
      pointer-events: none;
    }

    svg {
      height: 18px;
      width: 18px;
      pointer-events: none;
      color: darken($primaryGray, 10);
    }
  }

</style>