<template>
  <div class="messenger_header_container">
    <div v-if="getServerErrors.length === 0" class="messenger_header">
      <h4>Contacts ({{ contactsCount }})</h4>
      <div class="messenger_actions">
        <div
          class="filter_contacts"
          @click="toggleFilterBox"
        >
          <SearchIcon
            :className="`icon__xsm__light ${searchClassName}`"
          />
        </div>
        <div class="contacts_options">
          <HorizontalDotsIcon
            className="icon__xsm__light"
          />
        </div>
      </div>
    </div>
    <p class="messenger_header_no_contacts" v-if="getServerErrors.length">{{ this.getServerErrors[0].msg }}</p>
    <FilterBox
      v-if="isFilterBoxVisible"
    />
    <div class="messenger_header_separator"></div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import SearchIcon from '../Icons/SearchIcon.vue';
  import HorizontalDotsIcon from '../Icons/HorizontalDotsIcon.vue';
  import FilterBox from './FilterBox.vue';

  export default {
    name: 'Header',
    props: {
      contactsCount: Number,
    },
    components: {
      SearchIcon,
      HorizontalDotsIcon,
      FilterBox,
    },

    data () {
      return {

      }
    },


    computed: {
        searchClassName() {
          return this.isFilterBoxVisible ? 'messenger_active_search' : 'messenger_non_active_search';
        },

        ...mapState('messenger',
        [
          'isFilterBoxVisible',
        ]
      ),
      ...mapGetters('messenger',
        [
          'getServerErrors'
        ]
      ),
    },

    methods: {
      ...mapMutations('messenger',
        [
          'TOGGLE_FILTER_BOX_VISIBILITY'
        ]
      ),
      toggleFilterBox () {
        this.TOGGLE_FILTER_BOX_VISIBILITY();
      }
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

  .messenger_header_no_contacts {
    margin: 0.1rem 0;
    color: darken($primaryWhite, 5);
    text-align: center;
    font-size:0.9rem;
    font-family: 'Open Sans', sans-serif;
  }

  .messenger_active_search {
    color: $themePink;
  }
  .messenger_non_active_search {
    color: $primaryWhite;
  }
</style>