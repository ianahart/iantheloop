<template>
  <div v-if="followRequests.length" id="nav_notifications_container">
    <div @click="closeNotifications" class="nav_notifications_close">
      <CloseIcon />
    </div>
    <FollowRequest
      v-for="followRequest in followRequests"
      :key="followRequest.id"
      :followRequest="followRequest"
      @acceptrequest="handleAcceptRequest"
      @denyrequest="handleDenyRequest"
    />
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
  background-color: rgba(50, 50, 50, 0.9);
  box-shadow: 0px 0px 11px 1px black;
  border-radius: 8px;
  position: absolute;
  top: 50px;
  right: 60px;
  z-index: 19;
  box-sizing: border-box;
  padding: 0.7rem;
}

.nav_notifications_close {
  display: flex;
  justify-content: flex-end;
  margin-right: 0.3rem;
  margin-top: 0.5rem;
  svg {
    width: 25px;
    height: 25px;
    color: #fff;
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


