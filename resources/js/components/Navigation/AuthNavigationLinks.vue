
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
      <MessagesIcon />
    </li>
    <li>
      <NotificationsIcon />
    </li>
    <li>
      <!-- If user has profile pic show <img> of user profile pic -->
      <ProfileIcon />
    </li>
  </ul>
</template>






<script>

  import { mapState } from 'vuex';

  import ProfileIcon from './LinkIcons/ProfileIcon';
  import MessagesIcon from './LinkIcons/MessagesIcon';
  import NotificationsIcon from './LinkIcons/NotificationsIcon';

  export default {

    name: 'NavigationLinks',

    components: {

      ProfileIcon,
      MessagesIcon,
      NotificationsIcon,
    },

    props: {
      rootStyle: String,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

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

/*
  IMPORTS
*/

   @import '../../../sass/general/_variables.scss';
   @import '../../../sass/general/_base.scss';
</style>