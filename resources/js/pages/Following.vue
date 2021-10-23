<template>
  <div v-if="isDataLoaded" class="following_page__container">
    <Header
      :src="ownerUser.owner_profile_picture"
      :count="listCount"
      :ownerFullName="ownerUser.owner_full_name"
      network="Following"
    />
    <p v-if="userError.length" class="no_following_msg">{{ userError }}</p>
    <section v-if="listCount > 0" class="following_main_content__container">
      <div class="following_main_content__heading"></div>
      <NetworkList
        :networkList="networkList"
        :userId="userId"
        network="Following"
      />
      <div class="following_main_content_load_more">
        <button v-if="pagination.next_page_url !== null" @click="loadMore">Load more</button>
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
  name: "Following",

  components: {
    Header,
    NetworkList,
  },

  created() {
    this.loadMore = debounce(this.loadMore, 400);
    this.RESET_MODULE();
    this.setUserId(this.$route.params.id);
  },

  async mounted() {
    await this.loadFollowingList();

    if (this.error) {
      this.$router.push({
        name: "NotFound",
        query: { status: `${encodeURI("Could not find the user")}` },
      });
    }
  },

  beforeDestroy() {
    window.clearTimeout(this.bounceID);

  },

  computed: {
    ...mapState("network", [
      "isDataLoaded",
      "networkList",
      'listCount',
      "userId",
      'pagination',
      'ownerUser',
      "userError",
    ]),
  },

  watch: {
    "$route.params.id": function () {
      this.RESET_MODULE();
      this.setUserId(this.$route.params.id);
      this.loadFollowingList();
    },
  },

  methods: {
    ...mapMutations("network", ["SET_USER_ID", "RESET_MODULE"]),

    ...mapActions("network", ["GET_FOLLOWING"]),

    setUserId(userId) {
      this.SET_USER_ID(userId);
    },

    async loadMore() {
      try {
       await this.GET_FOLLOWING();
      } catch(e) {
      }
    },

    async loadFollowingList() {
      await this.GET_FOLLOWING();
    },
  },
};
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
  box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.2);
  margin: 1rem auto 3rem auto;
  min-height: 400px;
}

.following_main_content__heading {
  // background-color: $themeLightBlue;
  background-color: rgba(72, 74, 164, 0.7);
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

.no_following_msg {
  text-align: center;
  font-size: 1rem;
  margin-bottom: 3rem;
  color: gray;
  font-style: italic;
}
</style>