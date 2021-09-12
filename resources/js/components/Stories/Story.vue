<template>
  <div @click="retrieveUserStory(userId)" class="story_container">
    <StoryProPic
      :src="profilePicture"
      :alt="name"
    />
    <h3>{{ capitalizedName }}</h3>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import StoryProPic from './StoryProPic.vue';

  export default {
    name: 'Story',
    props: {
     name: String,
     profilePicture: String,
     profileId: Number,
     storyId: Number,
     userId: Number,
    },
    components: {
       StoryProPic,
    },

    data() {
      return {

      }
    },

    mounted() {

    },

    beforeDestroy() {

    },

    computed: {
      capitalizedName() {
        return this.name.split(' ')
        .map(word => word.toUpperCase().slice(0, 1) + word.toLowerCase().slice(1))
        .join(' ');
      }
    },

    methods: {
      ...mapMutations('stories',
        [
          'SET_USER_ID_CLICKED'
        ]
      ),
      ...mapActions('stories',
        [
          'RETRIEVE_STORY'
        ]
      ),

      async retrieveUserStory(userId) {

        this.SET_USER_ID_CLICKED(userId);
        await this.RETRIEVE_STORY(userId);
      }
    },
  }

</script>


<style lang="scss">
  .story_container {
    box-sizing: border-box;
    margin: 1.5rem 0;
    display: flex;
    cursor: pointer;
    transition: all 0.2 ease-in;
    width: 100%;

    &:hover {
      background-color: rgba(0,0,0,0.4);
    }

    h3 {
      color: #fcfcfc;
      padding-left: 1rem;
      font-weight: 100;
      margin-left: 0.5rem;
      font-size: 0.85rem;
      margin-top: auto;
    }
  }
</style>