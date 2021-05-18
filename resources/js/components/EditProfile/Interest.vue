<template>
  <transition name="slide-interest" appear>
  <div :class="`single_interest__container ${backgroundColor}`">
    <p>{{ name }}</p>
    <span @click="removeInterest(id)">X</span>
  </div>
</transition>
</template>

<script>

  export default {

    name: 'Interest',

    props: {
      name: String,
      id: Number,
    },

    components: {

    },

    data () {

      return {

      }
    },

    computed: {

      backgroundColor () {

        return this.id % 2 === 0 ? 'even_indexed_interest' : 'odd_indexed_interest';
      }
    },

    methods: {

      removeInterest(id) {

        this.$emit('initdelete', id);
      }
    },
  }
</script>

<style lang="scss">

.slide-interest-enter-active, .slide-interest-leave-active {
  transition: all  0.5s;
}
.slide-interest-enter, .slide-interest-leave-to  {
  opacity: 0;
  transform: translateX(200px);
}

.single_interest__container {
    margin: 1rem;
    display: flex;
    justify-content: center;
    padding: 0.4rem 0.5rem;
    border-radius: 8px;
    box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.1);
    position: relative;
    min-height: 30px;
    height: auto;
    width: 115px;
    overflow-wrap: break-word;
    text-align: center;

    p {
      margin: 0;
      font-size: 0.8rem;
      width: 100%;
      word-break: break-all;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    span {
      cursor: pointer;
      font-size: 0.75rem;
      font-weight: bold;
      color: lighten($primaryBlack, 5);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0.1rem;
      width: 15px;
      height: 15px;
      border-radius: 50%;
      border: 2px solid lighten($primaryBlack, 5);
      position: absolute;
      top: -2px;
      left: -1px;
      transition: all 0.3s ease-out;

      &:hover {
        background-color: darken($primaryGray, 5);
      }
    }
}

.even_indexed_interest {
    background-color: rgba(72, 74, 164, 0.6);

    p {
      color: $mainInputLabel;
    }
}

.odd_indexed_interest {
  background-color: $primaryGray;
  p {
    color: $primaryBlack;
  }
}


</style>