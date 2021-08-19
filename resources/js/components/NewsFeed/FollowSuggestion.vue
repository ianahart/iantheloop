<template>
  <div class="follow_suggestion_container">
    <p>{{ capitalizedName }}</p>
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'FollowSuggestion',
    props: {
      followSuggestion: Object,
    },
    components: {

    },

    computed: {
      capitalizedName() {
        return this.followSuggestion.prospect.full_name.split(' ').map((word) => {
          return word.slice(0, 1).toUpperCase() + word.slice(1).toLowerCase();
        }).join(' ');
      },
    },
    methods: {

    },
  }


</script>

<style lang="scss">

.follow_suggestion_container {
  box-sizing: border-box;
}

</style>