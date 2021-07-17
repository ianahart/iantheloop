<template>
  <div class="messenger_header_container">
    <div class="messenger_header">
      <h4>Contacts ({{ contactsCount }})</h4>
      <div class="messenger_actions">
        <div class="filter_contacts">
          <SearchIcon
            className="icon__xsm__light"
          />
        </div>
        <div class="contacts_options">
          <HorizontalDotsIcon
            className="icon__xsm__light"
          />
        </div>
      </div>
    </div>
    <div class="messenger_header_separator"></div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import SearchIcon from '../Icons/SearchIcon.vue';
  import HorizontalDotsIcon from '../Icons/HorizontalDotsIcon.vue';

  export default {
    name: 'Header',
    props: {
      contactsCount: Number,
    },
    components: {
      SearchIcon,
      HorizontalDotsIcon,
    },

    data () {
      return {

      }
    },

    computed: {

    },

    methods: {

    },
  }
</script>

<style lang="scss">

  .messenger_header_container {
    h4 {
      font-weight: 100;
      margin:0;
      font-family: 'Open Sans', sans-serif;
    }
  }

  .messenger_header {
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .messenger_actions {
    display: flex;
    align-items: center;

    svg {
      margin: 0 0.3rem;
    }
  }

  .filter_contacts {
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .contacts_options {
    cursor: pointer;
    display:flex;
    align-items: center;
  }

  .messenger_header_separator {
    border-top: 1px solid #767676;
    margin: 0.3rem 0;
  }
</style>