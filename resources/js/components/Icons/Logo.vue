<template>
  <div class="logo">
    <img
      :src="require('../../../assets/iantheloop.png').default"
      alt="lines connected by dots"
    />
    <h1 :class="theme">iaNtheLoop</h1>
  </div>
</template>

<script>
export default {
  name: "Logo",
  computed: {
    theme() {
      return this.$route.name === "Home" ? "main-dark-logo" : "main-light-logo";
    },
  },
};
</script>

<style lang="scss">
.logo {
  width: 100px;
  height: 100px;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  position: relative;

  img {
    height: 100px;
    width: 100px;
    display: block;
  }

  h1 {
    font-size: 1.4rem;
    position: absolute;
    font-family: "Secular One", sans-serif;
    top: 37px;
    left: 0;
    text-shadow: 2px 3px 10px rgb(0 0 0 / 50%);
  }
}

.main-dark-logo {
  color: $primaryBlack;
}

.main-light-logo {
  color: $primaryWhite;
}

@media (max-width: 600px) {
  .logo {
    h1 {
      color: $primaryWhite;
      top: 50px;
      left: 0;
      font-size: 1rem;
    }
  }
}
</style>