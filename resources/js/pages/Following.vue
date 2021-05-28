<template>
  <div v-if="isDataLoaded" class="following_page__container">
    <Header
      :src="ownerProfilePic"
      :count="listCount"
      :ownerFullName="ownerFullName"
      network="Following"
    />

    <section v-if="listCount > 0" class="following_main_content__container">
      <div class="following_main_content__heading"></div>
      <NetworkList
        :networkList="networkList"
        :userId="userId"
        network="Following"
      />
      <div class="following_main_content_load_more">
        <button v-if="!endOfList" @click="loadMore">Load more</button>
      </div>
    </section>
  </div>
</template>


<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import Header from '../components/Network/Header.vue';
  import NetworkList from '../components/Network/NetworkList.vue';

  export default {

    name: 'Following',

    components: {
      Header,
      NetworkList,
    },

    props: {

    },

    data () {

      return {
        debounceID: '',
      }
    },

    created () {

      this.RESET_MODULE();
      this.setUserId(this.$route.params.id);
    },

    async mounted () {

        await this.loadFollowingList();

        if (this.error) {

          this.$router.push({ name: 'NotFound', query: { status: `${encodeURI('Could not find the user')}` } });
        }
    },

    beforeDestroy() {

      window.clearTimeout(this.bounceID);
    },



    computed: {

      ...mapState('network',
        [
          'isDataLoaded',
          'ownerProfilePic',
          'error',
          'listCount',
          'ownerFullName',
          'networkList',
          'userId',
        ]
      ),

      ...mapGetters('network',
        [
          'endOfList'
        ]
      ),
    },


    watch: {

        "$route.params.id": function() {

            this.RESET_MODULE();
            this.setUserId(this.$route.params.id);
            this.loadFollowingList();
        }
    },

    methods: {

      ...mapMutations('network',
        [
          'SET_USER_ID',
          'RESET_MODULE',
        ]
      ),

      ...mapActions('network',
        [
          'GET_FOLLOWING'
        ]
      ),

      setUserId (userId) {

        this.SET_USER_ID(userId);
      },

      loadMore () {

          this.debounce(async () => {

          await this.GET_FOLLOWING();
          }, 400);

      },

      async loadFollowingList() {


          await this.GET_FOLLOWING();


      },
      debounce(fn, delay = 1000) {

      return ((...args) => {

        clearTimeout(this.debounceID)

        this.debounceID = setTimeout(() => {

          this.debounceID = null

          fn(...args)
        }, delay)
      })()
    },
    }
  }

</script>

<style lang="scss">

  .following_page__container {
    box-sizing: border-box;
    background-color: lighten($primaryGray, 2);
    height: 100%;
    width: 100%;
  }

  .following_main_content__container {
    max-width: 900px;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid $mainInputBorder;
    border-radius: 8px;
    box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
    margin: 1rem auto 3rem auto;
    min-height: 400px;
  }

  .following_main_content__heading {
    // background-color: $themeLightBlue;
    background-color: rgba(72,74,164, 0.7);
    height: 50px;
    width: 100%;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  .following_main_content_load_more {
    display: flex;
    justify-content: center;
    margin: 2.5rem auto;

    button {
      box-shadow: 0px 3px 15px rgba(0,0,0,0.2);
      border: none;
      cursor: pointer;
      padding: 0.2rem 0.4rem;
      border-radius: 8px;
      height: 30px;
      color: $primaryWhite;
      background-color: lighten($themeLightBlue, 3);
      width: 120px;
      font-size: 0.85rem;

      &:hover {
        background-color: darken($themeLightBlue, 5);
      }
    }
  }
</style>