<template>
  <router-link
    :to="{
      name: `${name}`,
      params:{
        [`${Object.keys(this.params).toString()}`]: `${Object.values(this.params).toString()}`}
        }"
    class="profile__link"
  >
     {{ text }}
  </router-link>
</template>

<script>

  export default {

    name: 'ProfileLink',

    props: {

      name: String,
      text: String,
      params: Object,
    },

    mounted() {
    }
  }
</script>

<style lang="scss">

.profile__link {
  color: $themeLightBlue;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: bold;
  background-color: darken($primaryGray, 7);
  height: 35px;
  padding: 0.1 0.2rem;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  width: 200px;
  margin: 0 1rem;
  border-radius: 8px;
  transition: all 0.5s ease-out;

  &:first-of-type {
    background-color: $themeLightBlue;
    color: $primaryWhite;
  }

  &:hover {
    background-color: darken($primaryGray, 15);
    color: $primaryBlack;
  }
}


</style>