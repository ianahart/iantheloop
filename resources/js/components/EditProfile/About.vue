<template>
  <div class="edit_profile_about__container">
    <h3 class="profile_edit__title">About</h3>
      <div class="edit_profile_about_row">
        <RadioBtns
          v-for="(status, index) in getEditGroup.relationship.statuses"
          :key="index"
          :field="getEditGroup.relationship.field"
          :type="getEditGroup.relationship.type"
          :errors="getEditGroup.relationship.errors"
          :label="getEditGroup.relationship.statuses[index]"
          :value="getEditGroup.relationship.value"
          :nameAttr="getEditGroup.relationship.nameAttr"
          :selected="selectedRelationshipRadio"
          commitPath="profileEdit/UPDATE_FIELD"
        />
      </div>
      <div class="edit_profile_about_row">
        <TextArea
          :field="getEditGroup.bio.field"
          maxLength="500"
          :type="getEditGroup.bio.type"
          :errors="getEditGroup.bio.errors"
          :label="getEditGroup.bio.label"
          :value="getEditGroup.bio.value"
          :nameAttr="getEditGroup.bio.nameAttr"
          commitPath="profileEdit/UPDATE_FIELD"
        />
      </div>
      <div class="edit_profile_about_row">
        <p class="interest_limit" v-if="getEditGroup.interests.length === 5">You've reached the maximum interests you can have (5)</p>
          <InterestForm
            @inputchange="updateInterestInput"
            @add="indexInterest"
            :field="getEditGroup.interests.field"
            :label="getEditGroup.interests.label"
            :nameAttr="getEditGroup.interests.nameAttr"
            :type="getEditGroup.interests.type"
            :errors="getEditGroup.interests.errors"
            :value="getEditGroup.interests.value"
            :numOfInterests="getEditGroup.interests.interests.length"
          />
          <InterestCollection
            :interests="getEditGroup.interests.interests"
            @delete="handleDeleteInterest"
          />
        </div>
      </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import InterestCollection from './InterestCollection.vue';
  import InterestForm from './InterestForm.vue';
  import RadioBtns from '../forms/inputs/RadioBtns.vue';
  import TextArea from '../forms/inputs/TextArea.vue';





  export default {

    name: 'About',

    props: {

    },

    components: {
      InterestCollection,
      InterestForm,
      RadioBtns,
      TextArea,
    },

    beforeDestroy() {

    },

    computed: {

      ...mapGetters('profileEdit',
        [
          'getEditGroup',
          'selectedRelationshipRadio',
        ]
      ),
    },

    methods: {

      ...mapMutations('profileEdit',
        [
          'SET_INTEREST_VALUE',
          'INDEX_INTEREST',
          'DELETE_INTEREST',
        ]
      ),
      updateInterestInput (payload) {

          this.SET_INTEREST_VALUE(payload);
      },

      indexInterest(payload) {

        this.INDEX_INTEREST(payload);
      },

    handleDeleteInterest (payload) {

      this.DELETE_INTEREST(payload);
    }
    },
  }
</script>

<style lang="scss">
.edit_profile_about_row {
  margin: 2rem auto;
}
</style>