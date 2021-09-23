<template>
  <div id="nav_notifications_container">
    <header class="nav_notifications_header">
      <h4>Notifications</h4>
      <div @click="closeNotifications" class="nav_notifications_close">
        <CloseIcon />
      </div>
    </header>
    <div class="notifications_divider"></div>
    <div class="follow_requested_container">
      <h5>Follow Requests</h5>
      <FollowRequest
        v-for="followRequest in followRequests"
        :key="followRequest.id"
        :followRequest="followRequest"
        @acceptrequest="handleAcceptRequest"
        @denyrequest="handleDenyRequest"
      />
    </div>
    <div class="notifications_divider"></div>
    <div v-if="interactionNotificationsLoaded" class="interaction_notifications_container">
      <h5>Interactions</h5>
      <Interaction
        v-for="interaction in interactions"
        :key="interaction.id"
        :interaction="interaction"
      />
      <div
    class="load_older_interactions_btn" v-if="interactionCursor !== null"
      >
        <button @click="refillInteractions('App/Notifications/Interaction')">More notifications...</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";
import { debounce } from '../../helpers/moduleHelpers.js';

import FollowRequest from "./FollowRequest.vue";
import CloseIcon from "../Icons/CloseIcon.vue";
import Interaction from './Interaction.vue';

export default {
  name: "Notifications",

  props: {},

  components: {
    FollowRequest,
    CloseIcon,
    Interaction,
  },

  created() {
    this.refillInteractions = debounce(this.refillInteractions, 350);
  },

  async mounted() {
    await this.fetchInteractionNotifications('App/Notifications/Interaction');
  },

  beforeDestroy() {
    this.RESET_INTERACTION_NOTIFICATIONS();
    this.RESET_INTERACTION_CURSOR();
  },

  computed: {
    ...mapState("notifications",
      [
        "followRequests",
        'interactionNotificationsLoaded',
        'interactions',
        'interactionPagination',
        'interactionCursor',
        ]
      ),
  },

  methods: {
    ...mapMutations("navigation", ["CLOSE_NOTIFICATIONS"]),
    ...mapMutations('notifications', ['RESET_INTERACTION_NOTIFICATIONS', 'RESET_INTERACTION_CURSOR']),
    ...mapActions("profile", ["UPDATE_FOLLOW_STATS"]),
    ...mapActions("notifications", ["REMOVE_REQUEST", 'FETCH_INTERACTION_NOTIFICATIONS']),

    closeNotifications() {
      this.RESET_INTERACTION_NOTIFICATIONS();
      this.RESET_INTERACTION_CURSOR();
      this.CLOSE_NOTIFICATIONS();
    },

    async handleAcceptRequest(payload) {
      await this.UPDATE_FOLLOW_STATS(payload);
      this.$store.commit("notifications/SET_REMOVE_REQUEST", payload);
    },

    async handleDenyRequest(payload) {
      await this.REMOVE_REQUEST(payload);
    },

    async fetchInteractionNotifications(type) {
      await this.FETCH_INTERACTION_NOTIFICATIONS(type);
    },

    async refillInteractions(type) {
      try {
         await this.FETCH_INTERACTION_NOTIFICATIONS(type);
      } catch(e) {
      }
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
  max-width: 350px;
  min-height: 200px;

  h5 {
    text-align: center;
    margin: 0.1rem 0;
    color: $themePink;
    font-family: 'Secular One', sans-serif;
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

.follow_requested_container {
  box-sizing: border-box;
  min-height: 100px;
  max-height: 185px;
  overflow-y: auto;

      &::-webkit-scrollbar {
        width: 12px;
      }
      &::-webkit-scrollbar-track {
        background: darken($primaryBlack, 10);
        border-radius: 8px;
    }
    &::-webkit-scrollbar-thumb {
        background-color: $themePink;
        border-radius: 20px;
        border: 3px solid darken($primaryBlack, 10);
    }
}

.notifications_divider {
  box-sizing: border-box;
  width: 100%;
  margin: 0.25rem auto;
  border-bottom: 2px solid #323232;
}

.interaction_notifications_container {
  padding: 0.5rem;
  box-sizing: border-box;
  min-height: 170px;
  max-height: 250px;
  overflow-y: auto;

  &::-webkit-scrollbar {
      width: 12px;
    }
  &::-webkit-scrollbar-track {
      background: darken($primaryBlack, 10);
      border-radius: 8px;
  }
  &::-webkit-scrollbar-thumb {
      background-color: $themePink;
      border-radius: 20px;
      border: 3px solid darken($primaryBlack, 10);
  }
}

.load_older_interactions_btn {
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  margin: 1.2rem 0 1.5rem 0;

  button {
    border: none;
    background-color: transparent;
    color: #fb4d70;
    font-style: italic;
    letter-spacing: 0.03rem;
    border: none;
    background-color: transparent;
    cursor: pointer;
    font-family: "Open Sans", sans-serif;
  }
}


@media (max-width:600px) {
  #nav_notifications_container {
    right:0;
    left:0;
    width:95%;
    margin:0 auto;
  }
}

</style>


