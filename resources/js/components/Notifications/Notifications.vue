<template>
  <div id="nav_notifications_container">
    <header class="nav_notifications_header">
      <h4>Notifications</h4>
      <div @click="closeNotifications" class="nav_notifications_close">
        <CloseIcon />
      </div>
    </header>
    <div class="notifications_divider"></div>
    <h5>Follow Requests</h5>
    <FollowRequest
      v-for="followRequest in followRequests"
      :key="followRequest.id"
      :followRequest="followRequest"
      @acceptrequest="handleAcceptRequest"
      @denyrequest="handleDenyRequest"
    />
    <div class="notifications_divider"></div>
    <h5>Interactions</h5>
  </div>
</template>

<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";
import FollowRequest from "./FollowRequest.vue";
import CloseIcon from "../Icons/CloseIcon.vue";

export default {
  name: "Notifications",

  props: {},

  components: {
    FollowRequest,
    CloseIcon,
  },

  computed: {
    ...mapState("notifications", ["followRequests"]),
  },

  methods: {
    ...mapMutations("navigation", ["CLOSE_NOTIFICATIONS"]),
    ...mapActions("profile", ["UPDATE_FOLLOW_STATS"]),
    ...mapActions("notifications", ["REMOVE_REQUEST"]),

    closeNotifications() {
      this.CLOSE_NOTIFICATIONS();
    },

    async handleAcceptRequest(payload) {
      await this.UPDATE_FOLLOW_STATS(payload);
      this.$store.commit("notifications/SET_REMOVE_REQUEST", payload);
    },

    async handleDenyRequest(payload) {
      await this.REMOVE_REQUEST(payload);
    },
  },
};
</script>

<style lang="scss">
#nav_notifications_container {
  background-color: $primaryBlack;
  box-shadow: 0px 0px 11px 1px black;
  border-radius: 8px;
  position: absolute;
  top: 50px;
  right: 60px;
  z-index: 19;
  box-sizing: border-box;
  min-width: 250px;

  h5 {
    text-align: center;
    margin: 0.1rem 0;
    color: $primaryWhite;
    letter-spacing: 0rem;
  }
}

.nav_notifications_close {
  margin-right: 0.3rem;
  margin-top: 0.5rem;
  svg {
    width: 25px;
    height: 25px;
    color: #fff;
  }
}

.nav_notifications_header {
    display: flex;
    align-items: center;

    h4 {
      flex-grow: 1;
      text-align: center;
      margin: 0.1rem 0;
      color: $primaryWhite;
    }
}

.notifications_divider {
  box-sizing: border-box;
  width: 100%;
  margin: 0.5rem auto;
  border-bottom: 2px solid #323232;
}


@media (max-width:600px) {
  #nav_notifications_container {
    right: 20px;
  }
}


@media (max-width: 600px) {
  .nav_notifications_container {
    width: 95%;
    margin: 0 auto;
    top: 350x;
    right: 1px;
  }
}
</style>


