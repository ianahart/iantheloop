<template>
  <nav :class="`navbar__desktop ${navBGC}`">
    <div class="logo__container">
      <LogoName :theme="isLoggedIn ? 'dark' : 'light'" />
      <HamburgerIcon
        :className="
          isMenuIconVisible && isLoggedIn
            ? 'hamburger__menu__icon_dark'
            : 'hamburger__menu__icon_light'"
      />
    </div>
    <p v-if="isMenuIconVisible">&nbsp;</p>
    <InteractionAlert
      v-if="isLoggedIn && isCurrentInteractionAlertActive"
    />
    <NavigationLinks
      v-if="!isMenuIconVisible && !isLoggedIn"
      rootStyle="nav__links__desktop"
    />
    <AuthNavigationLinks
      v-if="!isMenuIconVisible && isLoggedIn"
      rootStyle="nav__links__desktop"
    />
    <Notifications v-if="isLoggedIn && notificationsAreOpen" />
    <MessageNotifications v-if="isLoggedIn && messageNotificationsAreOpen" />
  </nav>
</template>


<script>

import { mapState, mapGetters, mapActions } from "vuex";

import AuthNavigationLinks from "./AuthNavigationLinks";
import HamburgerIcon from "../Icons/HamburgerIcon";
import LogoName from "../Icons/LogoName";
import NavigationLinks from "./NavigationLinks";
import Notifications from "../Notifications/Notifications.vue";
import MessageNotifications from '../Notifications/MessageNotifications.vue';
import InteractionAlert from '../Notifications/InteractionAlert.vue';

export default {
  name: "NavbarDesktop",

  props: {},

  components: {
    AuthNavigationLinks,
    HamburgerIcon,
    LogoName,
    NavigationLinks,
    Notifications,
    MessageNotifications,
    InteractionAlert,
  },

  async mounted() {
    if (this.isLoggedIn) {
      await this.fetchFollowRequests();
    }
  },

  watch: {
    isLoggedIn() {

      if (this.isLoggedIn) {
        this.fetchFollowRequests();
      }
    },
  },

  computed: {
    ...mapGetters("user", ["isLoggedIn", "getUserId"]),

    ...mapState("navigation", ["navigationLinks", "notificationsAreOpen"]),
    ...mapState("notifications", ["messageNotificationsAreOpen", 'isCurrentInteractionAlertActive', 'processQueue']),
    ...mapState("hamburgerMenu", ["isMenuIconVisible"]),

    navBGC() {
      return this.isLoggedIn ? "logged_in_nav " : "default_nav";
    },
  },

  methods: {
    ...mapActions("notifications", ["FETCH_FOLLOW_REQUESTS"]),

    async fetchFollowRequests() {
      await this.FETCH_FOLLOW_REQUESTS(this.getUserId);
    },
  },
};
</script>



<style lang="scss">
.logged_in_nav {
  background-color: transparent;

  a {
    color: $primaryBlack;
  }
}

.default_nav {
  background-color: $primaryBlack;

  a {
    color: $primaryWhite;
  }
}

.navbar__desktop {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.1rem 0.7rem;

  p {
    margin: 1rem auto;
    text-align: center;
  }
}

.logo__container {
  display: flex;
  justify-content: space-between;
}

.nav__links__desktop {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0;
  width: 80%;
  margin-bottom: 0;

  li {
    margin: 0 0.5rem;
    list-style-type: none;

    &:first-of-type {
      margin-right: auto;
    }
  }

  a {
    font-size: 0.85rem;
    font-family: "Secular One", sans-serif;
    letter-spacing: 0.1rem;
    font-weight: 100;
    text-decoration: none;
    text-transform: uppercase;
    transition: all 0.25s ease-in-out;
    &:hover {
      border-bottom: 3px solid $themePink;
    }
    &:first-of-type {
      margin-right: auto;
    }
  }
}

@media (max-width: 600px) {
  .logo__container {
    width: 90%;
  }
}
</style>