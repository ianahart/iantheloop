<template>
  <div @click="handleClick" class="add_url_link__container">
    <PlusIcon
      className="link_icon__md-dark"
    />
    <p>{{ text }}</p>
  </div>
</template>


<script>

  import PlusIcon from '../Icons/PlusIcon.vue';

  export default {

    name: 'AddUrlLink',

    props: {
      text: String,
    },

    components: {

      PlusIcon,
    },

    methods: {

      handleClick () {

        this.$emit('linkclicked');
      }
    }
  }


</script>

<style lang="scss">

  // @import '../../../sass/general/_variables.scss';

  .add_url_link__container {
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.25 ease-out;

    &:hover {
      opacity: 0.8;
    }

    p {
      margin-left: 0.2rem;
      color: $mainInputLabel;
    }

  }

</style>