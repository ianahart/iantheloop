<template>
  <div class="network_list__container">
      <NetworkListItem
        v-for="networkListItem in networkList"
        :key="networkListItem.id"
        :networkListItem="networkListItem"
        :userId="userId"
        :network="network"
      />
  </div>
</template>


<script>

  import NetworkListItem from './NetworkListItem.vue';

  export default {

    name: 'NetworkList',

    props: {
      networkList: Array,
      userId: String,
      network: String,
    },

    components: {

      NetworkListItem,
    },
  }

</script>

<style lang="scss">

  .network_list__container {
    width: 100%;
    box-sizing: border-box;
    padding: 0.5rem;
  }

</style>