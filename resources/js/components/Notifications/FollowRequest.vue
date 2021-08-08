<template>
  <div class="follow_request_container">
    <p id="follow_request_sent_date">{{ followRequest.request_date_sent }}</p>
    <div class="follow_request_inner_content">
      <div class="follow_request_profile_picture_container">
        <img
          v-if="followRequest.requester_profile_picture"
          :src="followRequest.requester_profile_picture"
          :alt="followRequest.full_name"
        />
        <DefaultProfileIcon
          v-if="!followRequest.requester_profile_picture"
          className="default_profile_image_rel_sm"
        />
      </div>
      <div class="follow_request_name">
        <p>
          {{ followRequest.full_name }} <em>has requested to follow you</em>
        </p>
      </div>
      <div class="follow_request_actions">
        <button @click="emitAcceptRequest(followRequest)">Accept</button>
        <button @click="emitDenyRequest(followRequest)">Deny</button>
      </div>
    </div>
  </div>
</template>


<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";
import DefaultProfileIcon from "../Icons/DefaultProfileIcon.vue";

export default {
  name: "FollowRequest",

  props: {
    followRequest: Object,
  },

  components: {
    DefaultProfileIcon,
  },

  computed: {},

  methods: {
    emitAcceptRequest(request) {
      const data = {
        viewingUserId: request.receiver_user_id,
        currentUserId: request.requester_user_id,
        requestId: request.id,
      };

      this.$emit("acceptrequest", data);
    },

    emitDenyRequest(request) {
      const data = {
        viewingUserId: request.receiver_user_id,
        requestId: request.id,
      };
      this.$emit("denyrequest", data);
    },
  },
};
</script>


<style lang="scss">
.follow_request_container {
  margin: 0.7rem 0;
  margin-bottom: 0;
  padding: 0.5rem;
}

.follow_request_inner_content {
  display: flex;
  align-items: center;
}

.follow_request_profile_picture_container {
  margin-right: 0.3rem;
  img {
    width: 28px;
    height: 28px;
    border-radius: 50%;
  }

  svg {
    color: $themePink;
    background-color: lighten($themeLightBlue, 3);
    height: 28px;
    width: 28px;
  }
}

.follow_request_name {
  p {
    font-size: 0.75rem;
    color: darken($primaryWhite, 3);
    em {
      font-size: 0.65rem;
      color: darken($primaryWhite, 3);
    }
  }
}

#follow_request_sent_date {
  font-size: 0.7rem;
  color: darkgray;
  margin: 0.1rem 0;
  display: flex;
  justify-content: flex-start;
}

.follow_request_actions {
  display: flex;
  align-items: center;

  button {
    margin: 0 0.4rem;
    cursor: pointer;
    transition: all 0.3s ease-out;
    border: none;
    border-radius: 8px;

    &:first-of-type {
      color: $primaryGray;
      background-color: transparent;
      border: 1px solid $themeLightBlue;
    }
    &:last-of-type {
      color: $themePink;
      background-color: transparent;
      border: 1px solid darken($themePink, 4);
    }
  }
}

@media (max-width: 600px) {
  .follow_request_inner_content {
    flex-direction: column;
    align-items: normal;
  }
}
</style>