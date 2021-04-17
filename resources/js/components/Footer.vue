<template>
  <div class="footer">
    <p>All Rights Reserved &#169;</p>
    <p>LoOped 2021</p>
    <div class="footer_policies">
      <a href="https://www.freeprivacypolicy.com/live/644bb30b-ae07-4de5-a9e1-c8230ad37f58">Privacy Policy</a>
    </div>
  </div>
</template>


<script>

  export default {

    name: 'Footer',

    components: {

    },

    props: {

    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    methods: {

    },
  }
</script>

<style lang="scss">
/*
IMPORTS
*/
  @import '../sass/general/_variables.scss';
  @import '../sass/general/_base.scss';

  .footer {
    background-color: lighten(#191919, 10);
    flex-shrink: 0;
    display: flex;
    justify-content: space-around;
    align-items: center;

    p {
      color: $primaryWhite;
      font-size: 0.8rem;
      &:first-of-type {
        color: $themeGold;
      }
    }

    a {
      color: $themeGold;
      font-size: 0.8rem;
      display: block;
      margin-bottom: 0.5rem;
    }
  }

  @media(max-width: 600px) {

    .footer {
      flex-direction: column;
    }
  }

</style>