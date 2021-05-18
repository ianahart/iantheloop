<template>
  <div class="interest__collection__container">
   <div class="interest__collection">
     <Interest
      @initdelete="sendDelete"
      v-for="interest in interests" :key="interest.id"
      :name="interest.name"
      :id="interest.id"
     />
   </div>
  </div>
</template>

<script>

  import Interest  from './Interest.vue';

  export default {

    name: 'InterestCollection',

    props: {
      interests: Array,
    },

    components: {

      Interest,
    },

    data () {

      return {

      }
    },

    computed: {

    },

    methods: {

      sendDelete(payload) {

        this.$emit('delete', payload);
      }
    },
  }
</script>

<style lang="scss">

.interest__collection__container {
    box-sizing: border-box;
    margin-top: 2rem;
    border: 1px solid $mainInputBorder;
    padding: 0.7rem;
    border-radius: 8px;
    max-width: 420px;
    min-height: 150px;
    height: auto;
}

.interest__collection {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
}

</style>