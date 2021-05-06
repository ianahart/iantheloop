<template>
  <div class="work_details__container">
    <form class="work_details__form">
      <div class="work_details__form_row">
        <InputFieldLg
          commitPath="workDetails/UPDATE_FIELD"
          :type="getCompany.type"
          :errors="getCompany.errors"
          :label="getCompany.label"
          :value="getCompany.value"
          :nameAttr="getCompany.nameAttr"
          :field="getCompany.field"
        />
        <InputFieldLg
          commitPath="workDetails/UPDATE_FIELD"
          :type="getPosition.type"
          :errors="getPosition.errors"
          :label="getPosition.label"
          :value="getPosition.value"
          :nameAttr="getPosition.nameAttr"
          :field="getPosition.field"
        />
        <InputFieldLg
          commitPath="workDetails/UPDATE_FIELD"
          :type="getCity.type"
          :errors="getCity.errors"
          :label="getCity.label"
          :value="getCity.value"
          :nameAttr="getCity.nameAttr"
          :field="getCity.field"
        />
      </div>
      <div class="work_details__form_row">
        <TextArea
          :field="getDescription.field"
          maxLength="300"
          :type="getDescription.type"
          :errors="getDescription.errors"
          :label="getDescription.label"
          :value="getDescription.value"
          :nameAttr="getDescription.nameAttr"
          commitPath="workDetails/UPDATE_FIELD"
        />
      </div>
      <div class="work_details__form_row">
        <p class="time_period__label">Time Period:</p>
        <div class="work_details_time_period">
          <p>From</p>
          <div>
            <CustomSelect
              :marker="yearfrom.marker"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              defaultOption="Year"
              commitPath="workDetails/UPDATE_FIELD"
              :type="getYearFrom.type"
              :errors="getYearFrom.errors"
              :label="getYearFrom.label"
              :value="getYearFrom.value"
              :nameAttr="getYearFrom.nameAttr"
              :field="getYearFrom.field"
              :options="years"
              :selected="yearfrom.selected"
              />
            <CustomSelect
              :marker="monthfrom.marker"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              defaultOption="Month"
              commitPath="workDetails/UPDATE_FIELD"
              :type="getMonthFrom.type"
              :errors="getMonthFrom.errors"
              :label="getMonthFrom.label"
              :value="getMonthFrom.value"
              :nameAttr="getMonthFrom.nameAttr"
              :field="getMonthFrom.field"
              :options="months"
              :selected="monthfrom.selected"
            />
          </div>
          <p v-if="!timePeriodChecked && yearfrom.selected !== 'Year'">To</p>
          <div v-if="!timePeriodChecked  && yearfrom.selected !== 'Year'">
            <CustomSelect
              :marker="yearto.marker"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              defaultOption="Year"
              commitPath="workDetails/UPDATE_FIELD"
              :type="getYearTo.type"
              :errors="getYearTo.errors"
              :label="getYearTo.label"
              :value="getYearTo.value"
              :nameAttr="getYearTo.nameAttr"
              :field="getYearTo.field"
              :options="yearsTo"
              :selected="yearto.selected"
            />
            <CustomSelect
              :marker="monthto.marker"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              defaultOption="Month"
              commitPath="workDetails/UPDATE_FIELD"
              :type="getMonthTo.type"
              :errors="getMonthTo.errors"
              :label="getMonthTo.label"
              :value="getMonthTo.value"
              :nameAttr="getMonthTo.nameAttr"
              :field="getMonthTo.field"
              :options="months"
              :selected="monthto.selected"
            />
        </div>
        <div class="work_details_checkbox__container">
          <CheckBox
            v-if="yearfrom.selected !== 'Year'"
            @checkbox="handleCheckBox"
            text="Currently"
          />
        </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations } from 'vuex';

  import InputFieldLg from '../../forms/inputs/InputFieldLg.vue';
  import TextArea from '../../forms/inputs/TextArea.vue';
  import CustomSelect from '../../forms/selects/CustomSelect.vue';
  import CheckBox from '../../forms/checkboxes/CheckBox.vue';

  import { workSelectYears } from '../../../data/selectYears';

  export default {

    name: 'WorkDetails',

    props: {

    },

    components: {

      InputFieldLg,
      TextArea,
      CustomSelect,
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

    created () {

    },

    mounted () {

    },

    computed: {

      yearsTo () {

        const startIndex = this.years.findIndex((year) => year.abbrv === this.yearfrom.selected);

        return this.years.slice(startIndex);
      },


      ...mapState('workDetails',
        [
        'months',
        'timePeriodChecked'
        ]
      ),

      ...mapGetters('workDetails',
        [
          'getCompany',
          'getPosition',
          'getCity',
          'getDescription',
          'getMonthTo',
          'getYearTo',
          'getMonthFrom',
          'getYearFrom',
        ]
      ),
    },

    methods: {

      ...mapMutations('workDetails',
        [
          'UPDATE_FIELD',
          'TOGGLE_CHECKBOX',
        ]
      ),

      handleSelection ({ selection }) {

        this[selection.marker].selected = selection.selected;

        this.UPDATE_FIELD(selection);
      },

      handleCheckBox () {

        this.yearto.selected = 'Year';
        this.monthto.selected = 'Month';

        this.TOGGLE_CHECKBOX();
      }
    }
  }

</script>


<style lang="scss">

  // @import '../../../../sass/general/_variables.scss';
  // @import '../../../../sass/forms/_inputs.scss';

  .work_details__form_row {
    margin: 2rem 0;
  }

  .work_details_time_period {
    display: flex;
    align-items: center;
    flex-wrap: wrap-reverse;
  }


  .work_details_time_period > p {

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



.work_details_checkbox__container {
  display: flex;
  justify-content: flex-end;
}




</style>