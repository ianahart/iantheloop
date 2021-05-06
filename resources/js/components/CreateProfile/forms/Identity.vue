<template>
  <div class="identity__container">
    <div class="identity_form__row">
      <div class="identity__radio_btns">
        <div>
          <p class="identity__label">{{ getGender.label }}:</p>
          <MaleIcon
            v-if="getGender.value === 'male'"
            className="icon__sm__dark"
          />
          <FemaleIcon
            v-if="getGender.value === 'female'"
            className="icon__sm__dark"
          />
          <TransgenderIcon
             v-if="getGender.value === 'trans'"
             className="icon__sm__dark"
          />
        </div>
        <div>
          <RadioBtns
              v-for="(option, index) in getGender.options"
              :key="index"
              :field="getGender.field"
              :type="getGender.type"
              :errors="getGender.errors"
              :label="getGender.options[index]"
              :value="getGender.value"
              :nameAttr="getGender.nameAttr"
              commitPath="identity/UPDATE_FIELD"
            />
        </div>
      </div>
    </div>
    <div class="identity_form__row">
        <div class="identity_label__container">
          <p class="identity__label">Birthday:</p>
          <BirthdayIcon
            className="icon__sm__dark"
          />
        </div>
        <div class="day__month__selects">
        <CustomSelect
            :marker="month.marker"
            @selected="handleSelection"
            className="custom_select__container custom_select_size__sm"
            defaultOption="Month"
            commitPath="identity/UPDATE_FIELD"
            :type="getBirthMonth.type"
            :errors="getBirthMonth.errors"
            :label="getBirthMonth.label"
            :value="getBirthMonth.value"
            :nameAttr="getBirthMonth.nameAttr"
            :field="getBirthMonth.field"
            :options="months"
            :selected="month.selected"
            />
            <CustomSelect
              v-if="getBirthMonth.value.length"
              :marker="day.marker"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              defaultOption="Month"
              commitPath="identity/UPDATE_FIELD"
              :type="getBirthDay.type"
              :errors="getBirthDay.errors"
              :label="getBirthDay.label"
              :value="getBirthDay.value"
              :nameAttr="getBirthDay.nameAttr"
              :field="getBirthDay.field"
              :options="getDaysInMonth"
              :selected="day.selected"
            />
        </div>
    </div>
      <div class="identity_form__row">
        <CustomSelect
          :marker="year.marker"
          @selected="handleSelection"
          className="custom_select__container custom_select_size__sm"
          defaultOption="Month"
          commitPath="identity/UPDATE_FIELD"
          :type="getBirthYear.type"
          :errors="getBirthYear.errors"
          :label="getBirthYear.label"
          :value="getBirthYear.value"
          :nameAttr="getBirthYear.nameAttr"
          :field="getBirthYear.field"
          :options="yearsList"
          :selected="year.selected"
      />
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations } from 'vuex';

  import RadioBtns from '../../forms/inputs/RadioBtns.vue';
  import MaleIcon from '../../Icons/MaleIcon.vue';
  import FemaleIcon from '../../Icons/FemaleIcon.vue';
  import TransgenderIcon from '../../Icons/TransgenderIcon.vue';
  import CustomSelect from '../../forms/selects/CustomSelect.vue';
  import BirthdayIcon from '../../Icons/BirthdayIcon.vue';

  import { years } from '../../../data/selectYears.js';

  export default {

    name: 'Identity',

    components: {

      RadioBtns,
      MaleIcon,
      FemaleIcon,
      TransgenderIcon,
      CustomSelect,
      BirthdayIcon,
    },

    data () {

      return {
          yearsList: years,
          day: {
          marker: 'day',
          selected: 'Day',
        },
        month: {
          marker: 'month',
          selected: 'Month',
        },
        year: {
          marker: 'year',
          selected: 'Year',
        },
      }
    },

    created () {

    },

    mounted () {

    },

    computed: {

      ...mapState('identity',
        [
          'months'
        ]
      ),

      ...mapGetters('identity',
        [
          'getGender',
          'getBirthDay',
          'getBirthYear',
          'getBirthMonth',
          'getDaysInMonth',
        ]
      ),
    },

    methods: {

      ...mapMutations('identity',
        [
          'UPDATE_FIELD'
        ]
      ),

      handleSelection ({ selection }) {

        if (selection.field === 'birth_month') {

          this.day.selected = 'Day';
        }

        this[selection.marker].selected = selection.selected;

        this.UPDATE_FIELD(selection);
      },
    }
  }
</script>

<style lang="scss">

  // @import '../../../../sass/general/_variables.scss';


  .identity_form__row {
    margin: 2rem 0;

  }

  .identity__radio_btns {


    div {
      display: flex;
      align-items: center;
    }
  }

  .identity_label__container {
    display: flex;


    svg {
      place-self: flex-end;
    }

    p {
      place-self: flex-end;
      margin-bottom: 0rem;
      margin-right: 0.4rem;
      margin-top: 0;
    }
  }


  .identity__label {
    font-size: 0.85rem;
    font-weight: bold;
    color: $mainInputLabel;
  }

  .day__month__selects {
    display: flex;
    align-items: center;
  }

  @media(max-width:600px) {

    .identity__radio_btns {

      div {
        &:last-of-type {
          display: block;
        }
      }
    }
  }

</style>

