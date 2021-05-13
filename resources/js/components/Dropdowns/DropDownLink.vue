<template>
      <router-link :to="{name: `${name}`, params: {[`${param}`]: `${paramValue}` }}">{{ linkText }}</router-link>
</template>

<script>

  export default {

    name: 'DropDownLink',

    components: {

    },

    props: {
      name: String,
      linkText: String,
      param: String,
      paramValue: String,
    },
  }

</script>


<style lang="scss">






</style>