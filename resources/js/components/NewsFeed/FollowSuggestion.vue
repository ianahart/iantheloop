<template>
  <div class="follow_suggestion_container">
    <div class="follow_suggestion_profile_picture_container">
      <UserPicture
        :src="followSuggestion.prospect.profile.profile_picture"
        :alt="followSuggestion.prospect.full_name"
      />
    </div>
    <div class="follow_suggestion_name_container">
      <router-link :to="{name:'Profile', params: { id: `${followSuggestion.prospect.id.toString()}` }}"> {{ capitalizedName }}</router-link>
      <p>{{ followSuggestion.mutual_follows }} mutual following</p>
    </div>
    <div class="follow_suggestion_actions_container">
      <button @click="emitFollow(followSuggestion)">Follow</button>
      <button @click="emitIgnore(followSuggestion)">Ignore</button>
    </div>
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import UserPicture from '../Notifications/UserPicture.vue';

  export default {
    name: 'FollowSuggestion',
    props: {
      followSuggestion: Object,
    },
    components: {
      UserPicture,
    },

    computed: {
      capitalizedName() {
        return this.followSuggestion.prospect.full_name.split(' ').map((word) => {
          return word.slice(0, 1).toUpperCase() + word.slice(1).toLowerCase();
        }).join(' ');
      },
    },

    methods: {
      emitFollow(followSuggestion) {
        console.log('Follow Suggestion Id: ', followSuggestion.id);
        console.log('Sending Follow Request to: ', followSuggestion.prospect.id);
      },
      emitIgnore(followSuggestion) {
        console.log('Follow Suggestion Id: ', followSuggestion.id);
        console.log('Ignoring Follow Suggeston from: ', followSuggestion.prospect.id);
      }
    },
  }


</script>

<style lang="scss">

.follow_suggestion_container {
  box-sizing: border-box;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  margin: 0.5rem;
}

.follow_suggestion_profile_picture_container {
  img {
    width: 44px;
    height: 44px;
  }
  svg {
    width: 44px;
    height: 44px;
  }
}

.follow_suggestion_actions_container {
  display: flex;
  flex-direction: column;

  button {
    cursor: pointer;
    transition: all 0.25s ease-in-out;
    border: none;
    border-radius: 6px;
    color: lighten(#3a3b3c, 3);
    border: 1px solid $mainInputBorder;
    font-family: 'Open Sans', sans-serif;
    margin: 0.2rem;
    padding: 0.1rem 0.2rem;
  }

}



.follow_suggestion_name_container {
  p {
    color: lighten(#3a3b3c, 3);
    font-weight: bold;
    margin: 0.1rem;
    &:last-of-type {
      font-size:0.7rem;
      color: darken(darkgrey, 7);
    }
  }

  a {
      text-decoration: none;
      color: lighten(#3a3b3c, 3);
      font-weight: bold;
      margin: 0.1rem;
      font-size: 0.85rem;
    }
}

</style>