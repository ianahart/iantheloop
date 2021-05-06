<template>
      <router-link :to="{name: `${name}`}">{{ linkText }}</router-link>
</template>

<script>

  export default {

    name: 'DropDownLink',

    components: {

    },

    props: {
      name: String,
      linkText: String,
    },
  }

</script>


<style lang="scss">






</style>