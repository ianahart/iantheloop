<template>
  <div class="edit_profile_work__container">
    <h3 class="profile_edit__title">Work</h3>
    <div class="edit_profile_work__fields">
      <div class="edit_profile_work__row">
        <div class="edit_profile_work_field__container">
          <InputFieldLg
          :commitPath="'profileEdit/UPDATE_FIELD'"
          :field="getEditGroup.company.field"
          :type="getEditGroup.company.type"
          :errors="getEditGroup.company.errors"
          :label="getEditGroup.company.label"
          :value="getEditGroup.company.value"
          :nameAttr="getEditGroup.company.nameAttr"
          />
        </div>
        <div class="edit_profile_work_field__container">
          <InputFieldLg
          :commitPath="'profileEdit/UPDATE_FIELD'"
          :field="getEditGroup.position.field"
          :type="getEditGroup.position.type"
          :errors="getEditGroup.position.errors"
          :label="getEditGroup.position.label"
          :value="getEditGroup.position.value"
          :nameAttr="getEditGroup.position.nameAttr"
          />
        </div>
      </div>
      <div class="edit_profile_work__row">
        <div class="edit_profile_work_field__container">
          <InputFieldLg
          :commitPath="'profileEdit/UPDATE_FIELD'"
          :field="getEditGroup.work_city.field"
          :type="getEditGroup.work_city.type"
          :errors="getEditGroup.work_city.errors"
          :label="getEditGroup.work_city.label"
          :value="getEditGroup.work_city.value"
          :nameAttr="getEditGroup.work_city.nameAttr"
          />
        </div>
        <div class="edit_profile_work_field__container">
          <TextArea
            :field="getEditGroup.description.field"
            maxLength="300"
            :type="getEditGroup.description.type"
            :errors="getEditGroup.description.errors"
            :label="getEditGroup.description.label"
            :value="getEditGroup.description.value"
            :nameAttr="getEditGroup.description.nameAttr"
            commitPath="profileEdit/UPDATE_FIELD"
          />
        </div>
      </div>
      <div class="edit_profile_work__column">
         <p class="time_period__label">Time Period:</p>
        <div class="work_time_period">
          <p>From</p>
          <div>
            <CustomSelect
              marker="yearfrom"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              commitPath="profileEdit/UPDATE_FIELD"
              :type="getEditGroup.year_from.type"
              :errors="getEditGroup.year_from.errors"
              :label="getEditGroup.year_from.label"
              :value="getEditGroup.year_from.value"
              :nameAttr="getEditGroup.year_from.nameAttr"
              :field="getEditGroup.year_from.field"
              :options="years"
              :selected="getEditGroup.year_from.value"
              />
            <CustomSelect
              marker="monthfrom"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              commitPath="profileEdit/UPDATE_FIELD"
              :type="getEditGroup.month_from.type"
              :errors="getEditGroup.month_from.errors"
              :label="getEditGroup.month_from.label"
              :value="getEditGroup.month_from.value"
              :nameAttr="getEditGroup.month_from.nameAttr"
              :field="getEditGroup.month_from.field"
              :options="months"
              :selected="getEditGroup.month_from.value"
            />
          </div>
          <p v-if="!isCurrentlyChecked && getEditGroup.year_from.value !== 'Year'">To</p>
          <div v-if="!isCurrentlyChecked  && getEditGroup.year_from.value !== 'Year'">
            <CustomSelect
              marker="yearto"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              commitPath="profileEdit/UPDATE_FIELD"
              :type="getEditGroup.year_to.type"
              :errors="getEditGroup.year_to.errors"
              :label="getEditGroup.year_to.label"
              :value="getEditGroup.year_to.value"
              :nameAttr="getEditGroup.year_to.nameAttr"
              :field="getEditGroup.year_to.field"
              :options="yearsTo"
              :selected="getEditGroup.year_to.value"
            />
            <CustomSelect
              marker="monthto"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              commitPath="profileEdit/UPDATE_FIELD"
              :type="getEditGroup.month_to.type"
              :errors="getEditGroup.month_to.errors"
              :label="getEditGroup.month_to.label"
              :value="getEditGroup.month_to.value"
              :nameAttr="getEditGroup.month_to.nameAttr"
              :field="getEditGroup.month_to.field"
              :options="months"
              :selected="getEditGroup.month_to.value"
            />
        </div>
        <div class="work_checkbox__container">
          <CheckBox
            v-if="getEditGroup.year_from.value !== 'Year'"
            @checkbox="handleCheckBox"
            text="Currently"
            :checked="isCurrentlyChecked"
          />
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import TextArea from '../forms/inputs/TextArea.vue';
  import CustomSelect from '../forms/selects/CustomSelect.vue';
  import InputFieldLg from '../forms/inputs/InputFieldLg.vue';
  import CheckBox from '../forms/checkboxes/CheckBox.vue';

  import { workSelectYears } from '../../data/selectYears.js';


  export default {

    name: 'Work',

    props: {

    },

    components: {
      TextArea,
      CustomSelect,
      InputFieldLg,
      CheckBox,
    },

    data () {

      return {
        years: workSelectYears,
        yearfrom: {
          marker: 'yearfrom',
          selected: 'Year',
        },
        monthfrom: {
          marker: 'monthfrom',
          selected: 'Month',
        },
        yearto: {
          marker: 'yearto',
          selected: 'Year',
        },
        monthto: {
          marker: 'monthto',
          selected: 'Month',
        },
      }
    },

    beforeDestroy() {

    },

    computed: {

      yearsTo () {

        const startIndex = this.years.findIndex((year) => year.abbrv === this.getEditGroup.year_from.value);

        return this.years.slice(startIndex);
      },

      ...mapState('profileEdit',
        [
          'isCurrentlyChecked',
          'months',
        ]
      ),

      ...mapGetters('profileEdit',
        [
          'getEditGroup'
        ]
      ),
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'UPDATE_FIELD',
          'TOGGLE_CURRENTLY'
        ]
      ),


      handleSelection ({ selection }) {



        this.UPDATE_FIELD(selection);
      },

      handleCheckBox () {

        this.TOGGLE_CURRENTLY();
      }
    }
  }
</script>

<style lang="scss">

.edit_profile_work__fields {
  width: 100%;
  margin-top: 2rem;
  box-sizing: border-box;
}

.edit_profile_work__container {
  width: 100%;
  box-sizing: border-box;
}
.edit_profile_work__row {
  width: 100%;
  display: flex;
  box-sizing: border-box;
  justify-content: space-evenly;
}

.edit_profile_work_field__container {
  width: 100%;
  box-sizing: border-box;
  padding: 0.5rem;
  display: flex;
  justify-content: center;
}

  .work_time_period {
    display: flex;
    align-items: center;
    flex-wrap: wrap-reverse;
  }

  .work_time_period > p {
    margin-left: 0.3rem;
    margin-right: 0.3rem;
    color: $themeLightBlue;
    font-size: 0.8rem;
    font-weight: bold;
  }

  .time_period__label {
    color: $mainInputLabel;
    font-size: 0.85rem;
    font-weight: bold;
    margin-bottom: 0;
  }

.work_checkbox__container {
  display: flex;
  justify-content: flex-end;
}

.edit_profile_work__column {
  display: flex;
  margin-top: 3rem;
  flex-direction: column;
  align-items: flex-start;
  padding: 0.5rem;
}





@media(max-width:600px) {

  .edit_profile_work__row {
    flex-direction: column;
  }

  .edit_profile_work__column {
    margin-top: 1.5rem;
  }
}




</style>