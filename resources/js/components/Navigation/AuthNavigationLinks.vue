
<template>
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
        :className="
          !isMenuVisible ? 'messages__icon_dark' : 'messages__icon_light'"
      />
    </li>
    <li class="navbar_notificatons_listitem" @click.stop="toggleNotifications">
      <NotificationsIcon
        :className="
          !isMenuVisible
            ? 'notifications__icon_dark'
            : 'notifications__icon_light'"
      />
    </li>
    <li>

      <ProfileIcon location="nav" />
    </li>
  </ul>
</template>






<script>
import { mapState, mapMutations } from "vuex";

import MessagesIcon from "./LinkIcons/MessagesIcon";
import NotificationsIcon from "./LinkIcons/NotificationsIcon";
import ProfileIcon from "./LinkIcons/ProfileIcon";

export default {
  name: "NavigationLinks",

  components: {
    MessagesIcon,
    NotificationsIcon,
    ProfileIcon,
  },

  props: {
    rootStyle: String,
  },

  computed: {
    ...mapState("navigation", ["authNavigationLinks", "notificationsAreOpen"]),
    ...mapState("notifications", ["messageNotificationsAreOpen"]),
    ...mapState("hamburgerMenu", ["isMenuVisible"]),
    ...mapState("profileDropdown", ['isProfileDropdownOpen']),
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

    test() {

    },
  },
};
</script>

<style lang="scss">
.navbar_notificatons_listitem {
  cursor: pointer;
}
</style>