<template>
  <div :class="`${footerStyles} footer`">
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

    props: {
      footerStyles: String,
    }

  }
</script>

<style lang="scss">

  .footer {
    background-color: lighten(#191919, 10);
    flex-shrink: 0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    height:50px;

    p {
      color: $primaryWhite;
      font-size: 0.8rem;
      &:first-of-type {
        color: #fff;
      }
    }

    a {
      color: #fff;
      font-size: 0.8rem;
      display: block;
      margin-bottom: 0.5rem;
    }
  }
  @media(max-width: 600px) {

    .footer {
      flex-direction: column;
      height: 17.5%;
    }
  }

</style>

