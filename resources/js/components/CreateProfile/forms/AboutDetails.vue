<template>
  <div class="about_detals__container">
    <form class="about_details__form">
      <div class="about_detals_form__row">
        <TextArea
          :field="getBio.field"
          maxLength="300"
          :type="getBio.type"
          :errors="getBio.errors"
          :label="getBio.label"
          :value="getBio.value"
          :nameAttr="getBio.nameAttr"
          commitPath="aboutDetails/UPDATE_FIELD"
        />
      </div>
      <div class="about_detals_form__row">
        <RadioBtns
          v-for="(status, index) in getRelationship.statuses"
          :key="index"
          :field="getRelationship.field"
          :type="getRelationship.type"
          :errors="getRelationship.errors"
          :label="getRelationship.statuses[index]"
          :value="getRelationship.value"
          :nameAttr="getRelationship.nameAttr"
          commitPath="aboutDetails/UPDATE_FIELD"
        />
      </div>
      <div class="about-details_form__row">
        <p class="interest_limit" v-if="getInterests.interests.length === 5">You've reached the maximum interests you can have (5)</p>
        <AddInterests
          v-if="getInterests.interests.length !== 5"
          :field="getInterests.field"
          :type="getInterests.type"
          :errors="getInterests.errors"
          :label="getInterests.label"
          :value="getInterests.value"
          :nameAttr="getInterests.nameAttr"
        />
        <p class="forms__input__error" v-for="(error, index) in getInterests.errors" :key="index">{{ error }}</p>
        <div v-if="getInterests.interests.length" class="about_details_interests">
          <Interests
              :interests="getInterests.interests"
          />
        </div>
      </div>
    </form>
  </div>
</template>

<script>

  import { mapGetters } from 'vuex';

  import TextArea from '../../forms/inputs/TextArea.vue';
  import RadioBtns from '../../forms/inputs/RadioBtns.vue';
  import AddInterests from '../../CreateProfile/forms/AddInterests.vue';
  import Interests from '../../CreateProfile/forms/Interests.vue';

  export default {

    name: 'AboutDetails',

    props: {

    },

    components: {

      TextArea,
      RadioBtns,
      AddInterests,
      Interests,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    computed: {

        ...mapGetters('aboutDetails',
          [
            'getBio',
            'getRelationship',
            'getInterests',
          ]
        ),
    },

    methods :{

    },
  }

</script>

<style lang="scss">

  // @import '../../../../sass/general/_variables.scss';

  .about_detals_form__row {
    margin: 2rem 0;
  }

  .about_details_interests {
    box-sizing: border-box;
    margin-top: 2rem;
    border: 1px solid $mainInputBorder;
    padding: 0.7rem;
    border-radius: 8px;
    max-width: 420px;
    min-height: 150px;
    height: auto;
  }

  .interest_limit {

    color: $warning;
    font-size: 0.8rem;
    font-weight: bold;
  }

</style>