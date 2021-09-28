
<template>
  <div class="auth_nav_wrapper">
    <div class="auth_nav_home">
      <router-link :to="{name: 'Home'}">Home</router-link>
      <UserSearch v-if="$route.name !== 'Explore'" />
    </div>
    <ul :class="rootStyle">
      <li v-for="(authNavigationLink, index) in authNavigationLinks" :key="index">
        <router-link
          @click.native="hideMenu"
          :to="{ name: authNavigationLink.component }"
        >
          {{ authNavigationLink.linkText }}
        </router-link>
      </li>
      <li class="navbar_notificatons_listitem" @click.stop="toggleMessageNotifications">
        <MessagesIcon
          :className="iconStyle"
        />
        <span v-if="navMessageAlerts" class="navbar_notification_messages_alerts">&#43;</span>
      </li>
      <li class="navbar_notificatons_listitem" @click.stop="toggleNotifications">
        <NotificationsIcon
          :className="iconStyle"
        />
        <span v-if="navInteractionAlerts > 0" class="navbar_notification_interactions_alerts">{{ formattedInteractionAlert }}</span>
      </li>
      <li>
        <ProfileIcon location="nav" />
      </li>
    </ul>
  </div>
</template>






<script>
import { mapState, mapMutations, mapGetters } from "vuex";

import MessagesIcon from "./LinkIcons/MessagesIcon";
import NotificationsIcon from "./LinkIcons/NotificationsIcon";
import ProfileIcon from "./LinkIcons/ProfileIcon";
import UserSearch from '../UserSearch/UserSearch.vue';

export default {
  name: "NavigationLinks",

  components: {
    MessagesIcon,
    NotificationsIcon,
    ProfileIcon,
    UserSearch,
  },

  props: {
    rootStyle: String,
  },

  computed: {
    ...mapState("navigation", ["authNavigationLinks", "notificationsAreOpen"]),
    ...mapState("notifications", ["messageNotificationsAreOpen", 'navInteractionAlerts', 'navMessageAlerts']),
    ...mapState("hamburgerMenu", ["isMenuVisible"]),
    ...mapState("profileDropdown", ['isProfileDropdownOpen']),
    iconStyle() {
      return this.$route.name === 'Home' && !this.isMenuVisible ? 'messages__icon_dark' : 'messages__icon_light';
    },

    formattedInteractionAlert() {
      return this.navInteractionAlerts >= 9 ? `9+` : this.navInteractionAlerts;
    }
  },

  methods: {
    ...mapMutations("navigation",
      [
        "TOGGLE_NOTIFICATIONS",
        'CLOSE_NOTIFICATIONS',
      ]
    ),
    ...mapMutations("profileDropdown",
      [
        "CLOSE_PROFILE_DROPDOWN",
      ]
    ),
    ...mapMutations('notifications',
      [
        'TOGGLE_MESSAGE_NOTIFICATIONS',
        'CLOSE_MESSAGE_NOTIFICATIONS',

      ]
    ),

    hideMenu() {
      if (this.isMenuVisible) {
        this.$store.commit("hamburgerMenu/HIDE_MENU", false);
      }
    },

    toggleNotifications() {
      if (this.messageNotificationsAreOpen) {
          this.CLOSE_MESSAGE_NOTIFICATIONS();
      }
      if (this.isProfileDropdownOpen) {
          this.CLOSE_PROFILE_DROPDOWN(false);
      }

      this.TOGGLE_NOTIFICATIONS();
    },

    toggleMessageNotifications() {
      if (this.notificationsAreOpen) {
          this.CLOSE_NOTIFICATIONS();
      }
      if (this.isProfileDropdownOpen) {
          this.CLOSE_PROFILE_DROPDOWN(false);
      }
      this.TOGGLE_MESSAGE_NOTIFICATIONS();
    },
  },
};
</script>

<style lang="scss">

  .auth_nav_wrapper {
    box-sizing: border-box;
    width: 85%;
    display: flex;
    justify-content: flex-end;
    ul {
      justify-content: flex-end;
    }
  }
  .auth_nav_home {
    box-sizing: border-box;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    width: 60%;
  }

  .auth_nav_home > a {
        a {
      margin: 0 0.5rem;
      margin-right: 2rem;
    }
  }

  .navbar_notificatons_listitem {
    cursor: pointer;
    position: relative;
  }


  .navbar_notification_messages_alerts,
  .navbar_notification_interactions_alerts {
    position: absolute;
    height: 14px;
    width: 14px;
    border-radius: 50%;
    background-color: $themePink;
    color: $primaryWhite;
    top: -7px;
    right:0;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .navbar_notification_interactions_alerts {
    font-size: 0.7rem;
    font-weight: bold;
    height: 18px;
    width: 18px;
    padding: 0.015rem;
    top: -11px;
    font-family: 'Secular One', sans-serif;
  }

  @media(max-width:600px) {
    .auth_nav_wrapper {
      flex-direction: column;
      align-items: center;
      width: 100%;
      ul {
        align-items: center;
      }
    }
    .auth_nav_home {
      margin-top: 0.1rem;
      align-items: center;
      flex-direction: column-reverse;
      width: 100%;
    }
  }
</style>