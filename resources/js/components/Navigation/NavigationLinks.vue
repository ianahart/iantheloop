
<template>
  <ul :class="rootStyle">
    <li
      v-for="(navigationLink, index) in navigationLinks"
      :key="index"
    >
      <router-link
       @click.native="hideMenu"
      :to="{name: navigationLink.component}"
      >
        {{ navigationLink.linkText }}
      </router-link>
    </li>
  </ul>
</template>






<script>

  import { mapState } from 'vuex';

  export default {

    name: 'NavigationLinks',

    props: {
      rootStyle: String,
    },

    computed: {
      ...mapState('navigation',
        [
          'navigationLinks'
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