<template>
  <div
    ref="postsContainer"
    class="profile_posts__container"
  >
    <Post
      v-for="post in posts"
      :key="post.id"
      :post="post"
      :observer="observer"
      :lastPostItem="lastPostItem"
      :currentUserProfilePic="currentUserProfilePic"
    />
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  import Post from './Post.vue';

  export default {

    name: 'Posts',

    props: {
      subjectUserId: String,
      currentUserProfilePic: String,
    },

    components: {
      Post,
    },

    data () {

      return {
        observer: null,
        debounceID: '',
        threshold: document.documentElement.clientWidth <= 700 ? '0.1' : '0.7',
      }
    },

    async created () {

       await this.loadPosts(this.subjectUserId);
    },

    mounted () {



      this.setupObserver();


    },

    beforeDestroy() {

      this.observer.disconnect();
      clearTimeout(this.debounceID);
    },

    computed: {

      ...mapState('profileWall',
        [
          'postsLoaded',
          'morePosts',
          'posts',
          'lastPostItem',
        ]
      ),
    },

    methods: {

      ...mapActions('profileWall',
        [
          'LOAD_POSTS',
        ]
      ),

      ...mapMutations('profileWall',
        [
          'SET_POST_SEEN'
        ]
      ),

      handlePostSeen(payload) {

        this.SET_POST_SEEN(payload);
      },

      setupObserver() {

        const options = {
           root: null,
           rootMargin: '0px',
           threshold: this.threshold,
        };

        this.observer = new IntersectionObserver(this.onElementObserved, options);

      },

    onElementObserved(entries) {

        entries.forEach((entry) => {




          if (entry.intersectionRatio >= this.threshold || entry.isIntersecting) {
             let seenId = entry.target.attributes['data-id'].value;

            this.SET_POST_SEEN(parseInt(seenId));

            this.debounce(() => {

              this.loadMore();
            }, 400);


  }
          // if (entry.isIntersecting) {

          //   let seenId = entry.target.attributes['data-id'].value;

          //   this.SET_POST_SEEN(parseInt(seenId));

          //   this.debounce(() => {

          //     this.loadMore();
          //   }, 400);

          // }
        });
      },

      debounce(fn, delay = 400) {

      return ((...args) => {

        clearTimeout(this.debounceID)

        this.debounceID = setTimeout(() => {

          this.debounceID = null

          fn(...args)
        }, delay)
      })()
      },

      async loadMore () {
        try {

          await this.LOAD_POSTS(this.subjectUserId);
        } catch(e) {

        }
      },

      async loadPosts (subjectUserId) {
        try {

          await this.LOAD_POSTS(subjectUserId);
        } catch (e) {

        }
      }
    },
  }


</script>

<style lang="scss">

.profile_posts__container {
  box-sizing: border-box;
  padding:0.5rem;
  width: 100%;
  margin-top: 2rem;
}

</style>