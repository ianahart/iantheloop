<template>
  <div
    @click="goBack"
    class="previous_route__container"
    >
    <LeftArrowIcon
      className="icon__md__dark"
    />
    <p>
      <span>
        {{ routeName }}
      </span>
    </p>
  </div>
</template>

<script>

  import LeftArrowIcon from '../Icons/LeftArrowIcon.vue';

  export default {

    name: 'PreviousRoute',

    components: {

      LeftArrowIcon,
    },

    props: {

      routeName: String,
    },

    methods: {

      goBack() {

      this.$router.back();
      },

    },
  }

</script>

<style lang="scss">

.previous_route__container {
  cursor: pointer;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-left: 1rem;
  margin-top: 1rem;

  p {
    color: $primaryBlack;
    font-size: 0.85rem;
  }

  span {
    color: $themePink;
    font-size: 0.9rem;
  }
}
</style>