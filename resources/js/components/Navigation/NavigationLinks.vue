
<template>
  <ul :class="`${rootStyle} no-auth-links-list`">
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
  .no-auth-links-list {
    box-sizing: border-box;
    li {
      &:first-of-type {
        margin-left: auto;
      }
    }
  }
  @media(max-width:600px) {
    .no-auth-links-list {
      li {
        &:first-of-type {
          margin-left: 0;
        }
      }
    }
  }

</style>