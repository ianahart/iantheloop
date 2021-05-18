<template>
  <div class="edit_profile_general__container">
    <h3 class="profile_edit__title">General</h3>
    <div class="edit_profile_general__fields">
      <div class="edit_profile_general_displayname  edit_profile_field_spacer">
        <InputFieldMd
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.display_name.type"
          :errors="getEditGroup.display_name.errors"
          :label="getEditGroup.display_name.label"
          :value="getEditGroup.display_name.value"
          :nameAttr="getEditGroup.display_name.nameAttr"
          :field="getEditGroup.display_name.field"
        />
      </div>
      <div class="edit_profile_general_town_state edit_profile_field_spacer">
        <InputFieldLg
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.town.type"
          :errors="getEditGroup.town.errors"
          :label="getEditGroup.town.label"
          :value="getEditGroup.town.value"
          :nameAttr="getEditGroup.town.nameAttr"
          :field="getEditGroup.town.field"
        />
        <CustomSelect
          marker="state"
          @selected="handleSelection"
          className="custom_select__container custom-select_size__md"
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.state.type"
          :errors="getEditGroup.state.errors"
          :label="getEditGroup.state.label"
          :value="getEditGroup.state.value"
          :nameAttr="getEditGroup.state.nameAttr"
          :field="getEditGroup.state.field"
          :options="states"
          :selected="getEditGroup.state.value"
        />
      </div>
      <div class="edit_profile_general_phone_country edit_profile_field_spacer">
        <InputFieldMd
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.phone.type"
          :errors="getEditGroup.phone.errors"
          :label="getEditGroup.phone.label"
          :value="getEditGroup.phone.value"
          :nameAttr="getEditGroup.phone.nameAttr"
          :field="getEditGroup.phone.field"
        />
        <CustomSelect
          marker="country"
          @selected="handleSelection"
          className="custom_select__container custom-select_size__lg"
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.country.type"
          :errors="getEditGroup.country.errors"
          :label="getEditGroup.country.label"
          :value="getEditGroup.country.value"
          :nameAttr="getEditGroup.country.nameAttr"
          :field="getEditGroup.country.field"
          :options="countries"
          :selected="getEditGroup.country.value"
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
              commitPath="profileEdit/UPDATE_FIELD"
              :type="link.type"
              :errors="link.errors"
              :value="link.value"
              :nameAttr="link.nameAttr"
              :field="link.field"
              :id="link.id"
           />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import { states } from '../../data/states.js';
  import { countries } from '../../data/countries.js';


  import InputFieldMd from '../forms/inputs/InputFieldMd.vue';
  import InputFieldLg from '../forms/inputs/InputFieldLg.vue';
  import CustomSelect from '../forms/selects/CustomSelect.vue';
  import AddUrlLink from '../CreateProfile/AddUrlLink.vue';
  import InputFieldLink from '../forms/inputs/InputFieldLink.vue';
  import LinkIcon from '../Icons/LinkIcon.vue';


  export default {

    name: 'General',

    props: {

    },

    components: {
      InputFieldMd,
      InputFieldLg,
      CustomSelect,
      InputFieldLink,
      AddUrlLink,
      LinkIcon,
    },

    data () {

      return {
        states,
        countries,
      }
    },

    beforeDestroy() {

    },

    computed: {

      ...mapState('profileEdit', ['form']),

     ...mapGetters('profileEdit',
        [
          'getGeneralGroup',
          'getEditGroup',
          'getLinks',
        ]
      ),
    },

    methods: {

        ...mapMutations('profileEdit',
        [
          'UPDATE_FIELD',
          'ADD_FIELD',
          'REMOVE_LINK'
        ]
      ),

      handleSelection ({ selection }) {



        this.UPDATE_FIELD(selection);
      },

      addUrlLink () {

        this.ADD_FIELD();
      },

      handleRemoveLink({ id }) {

        this.REMOVE_LINK(id);
      }
    },
  }
</script>

<style lang="scss">
.edit_profile_general__container {
  box-sizing: border-box;
  height: 100%;
  padding: 1rem;
}

.edit_profile_general__fields {
  box-sizing: border-box;
  height: 100%;
}

.edit_profile_field_spacer {
  box-sizing: border-box;
  margin: 1rem 0 1.5rem 0;

}

.edit_profile_general_displayname {
  display: flex;
  justify-content: flex-start;
}

.edit_profile_general_town_state,
.edit_profile_general_phone_country {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

@media(max-width:650px) {

  .edit_profile_general_town_state,
  .edit_profile_general_phone_country {

    flex-direction: column;
  }
}

</style>