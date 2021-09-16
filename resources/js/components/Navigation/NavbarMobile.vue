<template>
  <transition name="fade" appear>
    <nav class="navbar__mobile">
      <div id="navbar__mobile_close_btn" @click="closeMenu">
        <CloseIcon />
      </div>

      <div class="navigation__links_container">
        <Logo />
        <NavigationLinks v-if="!isLoggedIn" rootStyle="nav__links__mobile" />
        <AuthNavigationLinks v-if="isLoggedIn" rootStyle="nav__links__mobile" />
        <Notifications v-if="isLoggedIn && notificationsAreOpen" />
        <MessageNotifications
          v-if="isLoggedIn && messageNotificationsAreOpen"
        />
      </div>
    </nav>
  </transition>
</template>


<script>
import { mapState, mapGetters, mapMutations } from "vuex";

import AuthNavigationLinks from "./AuthNavigationLinks";
import NavigationLinks from "./NavigationLinks.vue";
import CloseIcon from "../Icons/CloseIcon";
import HamburgerIcon from "../Icons/HamburgerIcon";
import Logo from "../Icons/Logo";
import Notifications from "../Notifications/Notifications.vue";
import MessageNotifications from "../Notifications/MessageNotifications.vue";

export default {
  name: "NavbarMobile",

  props: {},

  components: {
    AuthNavigationLinks,
    NavigationLinks,
    CloseIcon,
    HamburgerIcon,
    Logo,
    Notifications,
    MessageNotifications,
  },

  computed: {
    ...mapState("navigation", ["notificationsAreOpen"]),
    ...mapState("notifications", ["messageNotificationsAreOpen"]),
    ...mapGetters("user", ["isLoggedIn", "getUserId"]),
  },

  methods: {
    ...mapMutations("navigation", ["CLOSE_NOTIFICATIONS"]),
    ...mapMutations("notifications", ["CLOSE_MESSAGE_NOTIFICATIONS"]),
    closeMenu() {
      this.$store.commit("hamburgerMenu/HIDE_MENU", false);
      this.CLOSE_NOTIFICATIONS();
      this.CLOSE_MESSAGE_NOTIFICATIONS();
    },
  },
};
</script>



<style lang="scss">
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
.navbar__mobile {
  background-color: $primaryBlack;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 17;
  box-shadow: 0px 0px 9px 3px rgba(41, 41, 41, 0.25);
}

.nav__links__mobile {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0;
  width: 80%;

  li {
    margin-top: 0.5rem;
    list-style-type: none;
    transition: all 0.25s ease-in-out;
    &:hover {
      border-bottom: 3px solid $themePink;
    }
  }

  a {
    font-size: 0.85rem;
    color: $primaryWhite;
    font-family: "Secular One", sans-serif;
    letter-spacing: 0.1rem;
    font-weight: 100;
    text-decoration: none;
    text-transform: uppercase;
  }
}

.navigation__links_container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

#navbar__mobile_close_btn {
  display: flex;
  justify-content: flex-start;
  margin-left: 0.5rem;
}
</style>