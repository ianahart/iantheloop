
<template>
  <div class="login__header">
    <h2>Login</h2>
    <Logo />
    <div class="login__signup__link">
      <div>
        <p>Sign in to continue</p>
      </div>
      <div>
        <p>Don't have an account?</p>
        <router-link :to="{ name: 'CreateAccount' }">Sign Up</router-link>
      </div>
    </div>
  </div>
</template>


<script>
import Logo from "../Icons/Logo.vue";

export default {
  name: "Header",

  components: {
    Logo,
  },

  props: {},

  data() {
    return {};
  },

  created() {},

  mounted() {},

  methods: {},
};
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Secular+One&display=swap");

.login__header {
  display: flex;
  align-items: center;
  flex-direction: column;

  h2 {
    font-size: 3rem;
    font-weight: 500;
    margin-bottom: 0;
    font-family: "Secular One", sans-serif;
    letter-spacing: -3px;
    color: $themeBlue;
  }
  h1 {
    color: transparent;
    visibility: hidden;
  }
}

.login__signup__link {
  display: flex;
  justify-content: space-between;
  width: 100%;

  div {
    &:last-of-type {
      display: flex;

      align-items: center;
    }
  }

  p {
    margin-bottom: 0;
    margin-top: 0;
    margin-right: 0.2rem;
    font-size: 0.8rem;
    &:first-of-type {
      font-weight: bold;
      color: $themeBlue;
    }
  }

  a {
    text-decoration: none;
    text-transform: uppercase;
    font-weight: bold;
    color: $themeBlue;
    font-size: 0.8rem;
    color: darken($themeDarkGreen, 10);
  }
}
</style>