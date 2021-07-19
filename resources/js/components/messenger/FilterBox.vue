<template>
  <div class="messenger_filter_box">
    <input
      @keyup="handleFilter"
      :value="filterBoxValue"
      type="text"
      name="filter_contacts"
      placeholder="Filter..."
    />
  </div>
</template>

<script>


  //input event
    // change
    // keyup
    // keydown

    import { mapState, mapMutations } from 'vuex';

  export default {
    name: 'FilterBox',
    props: {

    },
    components: {

    },
    data() {
      return {

      }
    },
    computed: {
      ...mapState('messenger',
        [
          'filterBoxValue'
        ]
      ),
    },
    methods: {
      ...mapMutations('messenger',
        [
          'UPDATE_FILTER_BOX_VALUE'
        ]
      ),
      handleFilter(e) {
        this.UPDATE_FILTER_BOX_VALUE(e.target.value);
      }
    },
  }
</script>

<style lang="scss">

.messenger_filter_box {
  margin: 0.5rem 0;
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  align-items: center;

  input {
    border-radius: 8px;
    height: 20px;
    border: none;
    border: 1px solid gray;
  }
}
</style>