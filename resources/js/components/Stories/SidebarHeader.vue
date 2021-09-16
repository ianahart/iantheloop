<template>
   <div>
    <div @click="toNewsFeed" class="newsfeed_return">
      <CloseIcon />
    </div>
    <div class="create_story_settings">
      <h1>{{ firstName }}'s Story</h1>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>
    <div @click=" page === 'StoriesDashboard' ? getCurrentUserStories(getUserId) : goToStoriesDashboard()" class="current_users_story">
      <StoryProPic
        :src="getProfilePic"
        :alt="userName"
        :userId="getUserId"
      />
      <h3>{{ capitalizedName }}</h3>
    </div>
   </div>
</template>

<script>
 import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

 import CloseIcon from '../Icons/CloseIcon.vue';
 import StoryProPic from './StoryProPic.vue';

  export default {
    name: 'SidebarHeader',
    props: {
      page: String,
    },
    components: {
      CloseIcon,
      StoryProPic,
    },

    computed: {
      ...mapGetters('user',
        [
          'userName',
          'getProfilePic',
          'getUserSlug',
          'getUserId',
        ]
      ),
      ...mapState('stories',
        [
          'currentUserHasStories',
          'currentUserStories',
        ]
      ),
      firstName() {
        if (this.userName) {
          const name = this.userName.split(' ')[0];
          return name.toUpperCase().slice(0, 1) + name.toLowerCase().slice(1)
        }
      },

      capitalizedName() {
        if (this.userName) {
          return this.userName.split(' ').map(word => word.toUpperCase().slice(0, 1) + word.toLowerCase().slice(1)).join(' ');
        }

      }
    },

    methods: {
      ...mapMutations('stories',
        [
          'SET_IS_LIGHTBOX_ACTIVE',
          'SET_USER_ID_CLICKED',
          'CLEAR_LIGHTBOX_STORY',
          'CLEAR_CURRENT_USER_STORIES',
        ]
      ),
      ...mapActions('stories',
        [
          'RETRIEVE_STORY',
        ]
      ),
      async getCurrentUserStories(userId) {
        this.CLEAR_LIGHTBOX_STORY();
        this.CLEAR_CURRENT_USER_STORIES();

        if (!this.currentUserStories.length) {
           this.SET_USER_ID_CLICKED(userId);
           await this.RETRIEVE_STORY(userId);
        }
          if (this.currentUserHasStories) {
            this.SET_USER_ID_CLICKED(userId);
            this.SET_IS_LIGHTBOX_ACTIVE(true);
          }

      },

      goToStoriesDashboard() {
        this.$router.push({name: 'StoriesDashboard', params:{userId: `${this.getUserId}`}});
      },

      toNewsFeed() {
        this.$router.push({name: 'NewsFeed', params:{slug: `${this.getUserSlug}`}});
      },
    },
  }
</script>
<style lang="scss">
  .newsfeed_return {
    box-sizing: border-box;
    display: flex;
    padding: 1rem;
    justify-content: flex-start;
    svg {
       height: 35px;
       width: 35px;
       color: $primaryWhite;
       background-color: #232222;
       border-radius: 50%;
    }
  }

 .create_story_settings {
    border-top:  1px solid #464447;
    padding: 0.5rem;
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    svg {
      color: darken(darkgrey,4);
      height: 48px;
      width: 48px;
      border-radius: 50%;
      background-color: #232222;
    }
    h1 {
      color: $primaryWhite;
      font-family: 'Secular One', sans-serif;
    }
  }

  .current_users_story {
    box-sizing: border-box;
    display: flex;
    justify-content: flex-start;
    padding: 0.7rem 0.5rem 1.3rem 0.5rem;
    border-bottom:  1px solid #464444;
    img {
      cursor: pointer;
    }
    svg {
      cursor: pointer;
    }
    h3 {
      color: $primaryWhite;
      padding-left: 1rem;
      font-weight: 100;
    }
  }
</style>

