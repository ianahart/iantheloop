<template>
  <div class="edit_profile_identity__container">
    <h3 class="profile_edit__title">Identity</h3>
    <div class="identity_form__row">
      <div class="identity__radio_btns">
        <div>
          <p class="identity__label">{{ getEditGroup.gender.label }}:</p>
          <MaleIcon
            v-if="getEditGroup.gender.value === 'male'"
            className="icon__sm__dark"
          />
          <FemaleIcon
            v-if="getEditGroup.gender.value === 'female'"
            className="icon__sm__dark"
          />
          <TransgenderIcon
             v-if="getEditGroup.gender.value === 'trans'"
             className="icon__sm__dark"
          />
        </div>
        <div>
          <RadioBtns
              v-for="(option, index) in getEditGroup.gender.options"
              :key="index"
              :field="getEditGroup.gender.field"
              :type="getEditGroup.gender.type"
              :errors="getEditGroup.gender.errors"
              :label="getEditGroup.gender.options[index]"
              :value="getEditGroup.gender.value"
              :nameAttr="getEditGroup.gender.nameAttr"
              :selected="selectedGenderRadio"
              commitPath="profileEdit/UPDATE_FIELD"
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
            marker="month"
            @selected="handleSelection"
            className="custom_select__container custom_select_size__sm"
            commitPath="profileEdit/UPDATE_FIELD"
            :type="getEditGroup.birth_month.type"
            :errors="getEditGroup.birth_month.errors"
            :label="getEditGroup.birth_month.label"
            :value="getEditGroup.birth_month.value"
            :nameAttr="getEditGroup.birth_month.nameAttr"
            :field="getEditGroup.birth_month.field"
            :options="months"
            :selected="getEditGroup.birth_month.value"
            />
            <CustomSelect
              v-if="getEditGroup.birth_month.value !== 'Month'"
              marker="day"
              @selected="handleSelection"
              className="custom_select__container custom_select_size__sm"
              commitPath="profileEdit/UPDATE_FIELD"
              :type="getEditGroup.birth_day.type"
              :errors="getEditGroup.birth_day.errors"
              :label="getEditGroup.birth_day.label"
              :value="getEditGroup.birth_day.value"
              :nameAttr="getEditGroup.birth_day.nameAttr"
              :field="getEditGroup.birth_day.field"
              :options="getDaysInMonth"
              :selected="getEditGroup.birth_day.value"
            />
        </div>
    </div>
      <div class="identity_form__row">
        <CustomSelect
          marker="day"
          @selected="handleSelection"
          className="custom_select__container custom_select_size__sm"
          commitPath="profileEdit/UPDATE_FIELD"
          :type="getEditGroup.birth_year.type"
          :errors="getEditGroup.birth_year.errors"
          :label="getEditGroup.birth_year.label"
          :value="getEditGroup.birth_year.value"
          :nameAttr="getEditGroup.birth_year.nameAttr"
          :field="getEditGroup.birth_year.field"
          :options="yearsList"
          :selected="getEditGroup.birth_year.value"
      />
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import { years } from '../../data/selectYears';

  import TransgenderIcon from '../Icons/TransgenderIcon.vue';
  import MaleIcon from '../Icons/MaleIcon.vue';
  import FemaleIcon from '../Icons/FemaleIcon.vue';
  import CustomSelect from '../forms/selects/CustomSelect.vue';
  import RadioBtns from '../forms/inputs/RadioBtns.vue';
  import BirthdayIcon from '../Icons/BirthdayIcon.vue';

  export default {

    name: 'Identity',

    props: {

    },

    components: {

      TransgenderIcon,
      MaleIcon,
      FemaleIcon,
      CustomSelect,
      RadioBtns,
      BirthdayIcon,
    },

    data () {

      return {

          yearsList: years,
      }
    },

    beforeDestroy() {

    },

    computed: {

      ...mapState('profileEdit',
        [
          'months'
        ]
      ),

      ...mapGetters('profileEdit',
        [
          'getEditGroup',
          'getDaysInMonth',
          'selectedGenderRadio',
        ]
      ),
    },

    methods: {

        ...mapMutations('profileEdit',
          [
            'UPDATE_FIELD',
          ]
        ),

        handleSelection ({ selection }) {



          this.UPDATE_FIELD(selection);
        },
    },
  }
</script>

<style lang="scss">

</style>