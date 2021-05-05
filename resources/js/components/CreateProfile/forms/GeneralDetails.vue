<template>
  <div class="general_details__container">
    <form class="general_details__form">
      <InputFieldMd
        commitPath="generalDetails/UPDATE_FIELD"
        :type="getDisplayName.type"
        :errors="getDisplayName.errors"
        :label="getDisplayName.label"
        :value="getDisplayName.value"
        :nameAttr="getDisplayName.nameAttr"
        :field="getDisplayName.field"
      />
      <div class="general_details_form__row">
        <InputFieldLg
          commitPath="generalDetails/UPDATE_FIELD"
          :type="getTown.type"
          :errors="getTown.errors"
          :label="getTown.label"
          :value="getTown.value"
          :nameAttr="getTown.nameAttr"
          :field="getTown.field"
        />
        <CustomSelect
          :marker="state.marker"
          @selected="handleSelection"
          className="custom_select__container custom-select_size__md"
          defaultOption="State"
          commitPath="generalDetails/UPDATE_FIELD"
          :type="getState.type"
          :errors="getState.errors"
          :label="getState.label"
          :value="getState.value"
          :nameAttr="getState.nameAttr"
          :field="getState.field"
          :options="states"
          :selected="state.selected"
        />
      </div>
      <div class="general_details_form__row">
        <InputFieldMd
          commitPath="generalDetails/UPDATE_FIELD"
          :type="getPhone.type"
          :errors="getPhone.errors"
          :label="getPhone.label"
          :value="getPhone.value"
          :nameAttr="getPhone.nameAttr"
          :field="getPhone.field"
        />
        <CustomSelect
          :marker="country.marker"
          @selected="handleSelection"
          className="custom_select__container custom-select_size__lg"
          defaultOption="Country"
          commitPath="generalDetails/UPDATE_FIELD"
          :type="getCountry.type"
          :errors="getCountry.errors"
          :label="getCountry.label"
          :value="getCountry.value"
          :nameAttr="getCountry.nameAttr"
          :field="getCountry.field"
          :options="countries"
          :selected="country.selected"
        />
      </div>
      <div class="general_details_urls__container">
        <div class="general_details_urls__heading">
         <LinkIcon
           className="link_icon__md-dark"
         />
         <h4>Websites and Social Links</h4>
        </div>
        <div class="general_details__urls">
          <p v-if="getLinks.length === 5" class="max_urls">Please remove one to add another (limit reached)</p>
          <AddUrlLink
            v-if="getLinks.length < 5"
            text="Add a Link"
            @linkclicked="addUrlLink"
          />
        </div>
        <div class="geneneral_details_generated_urls">
           <InputFieldLink
              v-for="link in getLinks" :key="link.id"
              @removelink="handleRemoveLink"
              commitPath="generalDetails/UPDATE_FIELD"
              :type="link.type"
              :errors="link.errors"
              :value="link.value"
              :nameAttr="link.nameAttr"
              :field="link.field"
              :id="link.id"
           />
        </div>
      </div>
    </form>
  </div>
</template>


<script>


  import { mapState, mapGetters, mapMutations } from 'vuex';

  import InputFieldSm from '../../forms/inputs/InputFieldSm.vue';
  import InputFieldMd from '../../forms/inputs/InputFieldMd.vue';
  import InputFieldLg from '../../forms/inputs/InputFieldLg.vue';
  import CustomSelect from '../../forms/selects/CustomSelect.vue';
  import LinkIcon from '../../Icons/LinkIcon.vue';
  import AddUrlLink from '../AddUrlLink.vue';
  import InputFieldLink from '../../forms/inputs/InputFieldLink.vue';

  import { states } from '../../../data/states.js';
  import { countries } from '../../../data/countries.js';

  export default {

    name: 'GeneralDetails',

    props: {

    },

    components: {

      InputFieldSm,
      InputFieldMd,
      InputFieldLg,
      CustomSelect,
      LinkIcon,
      AddUrlLink,
      InputFieldLink,
    },

    data () {

      return {
        states,
        countries,
        state: {
          marker: 'state',
          selected: 'State',
        },
        country: {
          marker: 'country',
          selected: 'Country',
        },
      }
    },

    created () {

    },

    mounted () {

    },

    computed : {

      ...mapState('generalDetails',
        [
          'hasErrors',
        ]
      ),
      ...mapGetters('generalDetails',
        [
          'getDisplayName',
          'getTown',
          'getState',
          'getCountry',
          'getPhone',
          'getLinks'
        ]
      ),
    },

    methods: {

      ...mapMutations('generalDetails',
        [
          'UPDATE_FIELD',
          'ADD_FIELD',
          'REMOVE_LINK'
        ]
      ),

      handleSelection ({ selection }) {

        this[selection.marker].selected = selection.selected;

        this.UPDATE_FIELD(selection);
      },

      addUrlLink () {

        this.ADD_FIELD();
      },

      handleRemoveLink({ id }) {

        this.REMOVE_LINK(id);
      }
    }
  }
</script>

<style lang="scss">

  @import '../../../../sass/general/_variables.scss';

  .general_details_form__row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 2rem 0;

    input {

      &:first-of-type {

        margin-right: 1rem;
      }
    }
  }

  .general_details_form__row {

    select {
      margin-top: 1rem;
    }
  }

  .general_details_urls__container {
    margin-top: 1.2rem;
  }

  .general_details_urls__heading {
    display: flex;
    justify-content: center;
    align-items: center;

    h4 {
        padding: 0;
        color: $mainInputLabel;
        letter-spacing: 0;
        margin-left: 0.2rem;
    }
  }

  .max_urls {
    text-align: center;
    color: $error;
    font-size: 0.85rem;
  }

</style>