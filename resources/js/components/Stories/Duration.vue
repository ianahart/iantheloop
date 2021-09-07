<template>
   <CustomSelect
      marker="duration"
      @selected="handleSelection"
      className="custom_select__container story_select custom_select_size__sm"
      commitPath="stories/UPDATE_STORY_FIELD"
      :errors="[]"
      label="Duration"
      :value="newStory.duration.name"
      nameAttr="duration"
      field="duration"
      :options="[{name: '5s', abbrv: '5s', id: 1}, {name: '10s', abbrv: '10s', id: 2}, {name: '15s', abbrv: '15s', id: 3}]"
      :selected="newStory.duration.name"
  />
</template>

<script>
  import { mapState,  mapMutations } from 'vuex';

  import CustomSelect from '../forms/selects/CustomSelect.vue';
  export default {
    name: 'Duration',
    props: {

    },
    components: {
       CustomSelect,
    },
    computed: {
      ...mapState('stories',
        [
          'newStory'
        ]
      ),
    },
    methods: {
      ...mapMutations('stories',
        [
          'UPDATE_STORY_FIELD'
        ]
      ),

      handleSelection ({ selection }) {
        this.UPDATE_STORY_FIELD(selection);
      },
    }
  }
</script>

<style lang="scss">
</style>
