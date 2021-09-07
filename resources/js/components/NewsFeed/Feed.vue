<template>
  <div class="feed_main_container">
    <div class="feed_form_container">
      <FormTrigger
        :baseProfile="{user_id: userId, current_user_first_name: userName ? userName.split(' ')[0] : '', view_user_first_name: userName ? userName.split(' ')[0] : '', current_user_full_name: userName}"
        :currentUserId="getUserId"
        :currentUserFirstName="userName ? userName.split(' ')[0] : ''"

        :currentUserProfilePic="getProfilePic"
        />
    </div>
    <div class="newsfeed_posts_container">
      <Posts
        :subjectUserId="userId"
        :currentUserProfilePic="getProfilePic"
        @loadsubsequent="emitRefill"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import FormTrigger from '../Post/FormTrigger.vue';
  import Posts from '../Post/Posts.vue';

  export default {
    name: 'Feed',
    props: {

    },
    components: {
      FormTrigger,
      Posts,

    },
    data () {
      return {

      }
    },

    created() {
      this.initialPostText();
    },

    beforeDestroy() {

    },

    computed: {
      userId() {
        if (this.getUserId) {
          return this.getUserId.toString();
        }
      },

      ...mapGetters('user',
        [
          'getUserId',
          'getProfilePic',
          'userName',
        ]
      ),
      ...mapState('posts', ['postsLoaded']),
    },
    methods: {

    ...mapMutations('posts',
      [
        'SET_INITIAL_POST_INPUT_TEXT',
        'CURRENT_USER_NAME'
      ]
    ),

    emitRefill() {
      this.$emit('refillfeed');
    },

    initialPostText() {
       this.SET_INITIAL_POST_INPUT_TEXT(
          {
            baseProfileUserId: this.userId,
            currentUserId:this.getUserId,
            currentUserFirstName: this.userName? this.userName.split(' ')[0] : '',
            viewUserFirstName: this.userName? this.userName.split(' ')[0] : '',
          }
        );
    },

    },
  }


</script>

<style lang="scss">
  .feed_main_container {
    box-sizing: border-box;
    background-color: #3b3b441f;
    border-radius: 0.25rem;
    margin: 0.5rem;
    flex-grow: 2;
    padding: 0.5rem;
  }

  .feed_form_container {
    box-sizing: border-box;
    max-width: 1000px;
    margin: 0 auto;
    width: 85%;
  }

  .newsfeed_posts_container {
    box-sizing: border-box;
    margin: 0 auto;
    max-width: 1000px;
  }

  @media (max-width:600px) {
    .feed_main_container {
      flex-grow: 1;
      width: 100%;
      max-width: 100%;
      margin: 0;
    }
    .feed_form_container {
      width: 100%;
    }
  }
</style>