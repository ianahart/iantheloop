<template>
  <div v-if="isDataLoaded" class="followers_page__container">
    <Header
      :src="ownerProfilePic"
      :count="listCount"
      :ownerFullName="ownerFullName"
      network="Followers"
    />
    <p v-if="userError.length" class="no_followers_msg">{{ userError }}</p>
    <section v-if="listCount > 0" class="followers_main_content__container">
      <div class="followers_main_content__heading"></div>
      <NetworkList
        :networkList="networkList"
        :userId="userId"
        network="Following"
      />
      <div class="followers_main_content_load_more">
        <button v-if="!endOfList" @click="loadMore">Load more</button>
      </div>
    </section>
  </div>
</template>


<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";
import { debounce } from '../helpers/moduleHelpers.js';

import Header from "../components/Network/Header.vue";
import NetworkList from "../components/Network/NetworkList.vue";

export default {
  name: "Followers",

  components: {
    Header,
    NetworkList,
  },

  created() {
    this.loadMore = debounce(this.loadMore,400);
    this.RESET_MODULE();
    const userId = this.$route.params.id;
    this.setUserId(userId);
  },

  async mounted() {
    await this.loadFollowersList();

    if (this.error) {
      this.$router.push({
        name: "NotFound",
        query: { status: `${encodeURI("Could not find the user")}` },
      });
    }
  },

  watch: {
    $route: function () {
      this.RESET_MODULE();
      this.setUserId(this.$route.params.id);
      this.loadFollowersList();
    },
  },

  computed: {
    ...mapState("network", [
      "isDataLoaded",
      "ownerProfilePic",
      "error",
      "listCount",
      "ownerFullName",
      "networkList",
      "userId",
      "userError",
    ]),

    ...mapGetters("network", ["endOfList"]),
  },

  methods: {
    ...mapMutations("network", ["RESET_MODULE", "SET_USER_ID"]),

    ...mapActions("network", ["GET_FOLLOWERS"]),

    setUserId(userId) {
      this.SET_USER_ID(userId);
    },

    async loadMore() {
      try {
        await this.GET_FOLLOWERS();
      }  catch(e) {
      }
    },

    async loadFollowersList() {
      try {
        await this.GET_FOLLOWERS();
      } catch (e) {

      }
    },
  },
};
</script>

<style lang="scss">
.followers_page__container {
  box-sizing: border-box;
  background-color: lighten($primaryGray, 2);
  height: 100%;
  width: 100%;
}

.followers_main_content__container {
  max-width: 900px;
  width: 100%;
  box-sizing: border-box;
  border: 1px solid $mainInputBorder;
  border-radius: 8px;
  box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.2);
  margin: 1rem auto 3rem auto;
  min-height: 400px;
}

.followers_main_content__heading {
  background-color: rgba(72, 74, 164, 0.7);
  height: 50px;
  width: 100%;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

.followers_main_content_load_more {
  display: flex;
  justify-content: center;
  margin: 2.5rem auto;

  button {
    box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.2);
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

.no_followers_msg {
  text-align: center;
  font-size: 1rem;
  margin-bottom: 3rem;
  color: gray;
  font-style: italic;
}
</style>