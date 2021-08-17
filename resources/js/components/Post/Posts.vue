<template>
  <div
    v-if="postsLoaded && posts.length"
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
      :postsOrigin="postsOrigin"
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
      created() {
        this.setupObserver();
      },

    beforeDestroy() {
      this.observer.disconnect();
      clearTimeout(this.debounceID);
      this.SET_POSTS_LOADED(false);
    },

    computed: {

      ...mapState('posts',
        [
          'postsLoaded',
          'morePosts',
          'posts',
          'lastPostItem',
          'postsOrigin',
        ]
      ),
      ...mapState('profileWallSettings', ['filtersShowing']),
    },

    methods: {
      ...mapActions('posts',
        [
          'LOAD_POSTS',
        ]
      ),
      ...mapMutations('posts',
        [
          'SET_POST_SEEN',
          'SET_POSTS_LOADED',
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
              this.loadSubsequent();
            }, 400)
            }
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

      loadSubsequent() {
        this.$emit('loadsubsequent', this.subjectUserId);
      },
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