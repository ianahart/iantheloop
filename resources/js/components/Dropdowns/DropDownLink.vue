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

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    computed : {

    },

    methods: {

    },
  }

</script>


<style lang="scss">

  @import '../../../sass/general/_variables.scss';




</style>