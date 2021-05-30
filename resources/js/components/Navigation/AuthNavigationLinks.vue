
<template>
  <ul :class="rootStyle">
    <li
      v-for="(authNavigationLink, index) in authNavigationLinks"
      :key="index"
    >
      <router-link
       @click.native="hideMenu"
      :to="{name: authNavigationLink.component}"
      >
        {{ authNavigationLink.linkText }}
      </router-link>
    </li>
    <li>
      <MessagesIcon
        :className="!isMenuVisible ? 'messages__icon_dark' : 'messages__icon_light'"
      />
    </li>
    <li>
      <NotificationsIcon
        :className="!isMenuVisible ? 'notifications__icon_dark' : 'notifications__icon_light'"
      />
    </li>
    <li>
      <!-- If user has profile pic show <img> of user profile pic -->
      <ProfileIcon
      location="nav"/>
    </li>
  </ul>
</template>






<script>

  import { mapState } from 'vuex';

  import MessagesIcon from './LinkIcons/MessagesIcon';
  import NotificationsIcon from './LinkIcons/NotificationsIcon';
  import ProfileIcon from './LinkIcons/ProfileIcon';

  export default {

    name: 'NavigationLinks',

    components: {
      MessagesIcon,
      NotificationsIcon,
      ProfileIcon,
    },

    props: {
      rootStyle: String,
    },

    computed: {
      ...mapState('navigation',
        [
          'authNavigationLinks'
        ]
      ),
     ...mapState('hamburgerMenu',['isMenuVisible']),
    },

    methods: {

      hideMenu () {

        if (this.isMenuVisible) {

          this.$store.commit('hamburgerMenu/HIDE_MENU', false);
        }
      },
    }
  }


</script>

<style lang="scss">

</style>