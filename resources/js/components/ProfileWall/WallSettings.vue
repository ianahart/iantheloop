<template>
  <div class="profile_wall_settings_container">
    <div v-if="!filtersShowing"  class="profile_wall_settings_trigger_container">
      <h2>Posts</h2>
      <div @click="openFilters" :class="`profile_wall_settings_trigger ${filterStyleIndicator}`">
        <FilterSolidIcon
          className="icon__sm__light"
        />
        <p>Filters</p>
      </div>
    </div>
      <div v-if="filtersShowing"  class="profile_wall_settings">
        <div class="profile_wall_settings_header">
          <h2>Post Filters</h2>
          <div class="profile_wall_settings_close" @click="closeFilters">
            <CloseSolidIcon
              className="icon__md__dark"
            />
          </div>
        </div>
        <div class="profile_wall_settings_divider"> </div>
        <div class="profile_wall_settings_info">
          <h4>Use Filters to look for posts on your wall.</h4>
          <p>This will not affect how other users see your wall.</p>
          <form @submit.prevent="applyFilters">
            <div class="posted_by_filter_container">
                <h4>Posted By:</h4>
                <CustomSelect
                  @selected="handleSelection"
                  className="custom_select__container custom-select_size__md"
                  commitPath="profileWallSettings/UPDATE_FILTER"
                  :errors="[]"
                  label=""
                  :value="getPostedBy.value"
                  :nameAttr="getPostedBy.nameAttr"
                  :field="getPostedBy.field"
                  :options="postedByOptions"
                  :selected="getPostedBy.value"
                />
              </div>
              <div class="go_to_date_filter_container">
                <h4>Go To:</h4>
                <CustomSelect
                  @selected="handleSelection"
                  className="custom_select__container custom-select_size__md"
                  commitPath="profileWallSettings/UPDATE_FILTER"
                  :errors="[]"
                  label=""
                  :value="getGoToYear.value"
                  nameAttr="go_to_year"
                  :field="getGoToYear.field"
                  :options="years"
                  :selected="getGoToYear.value"
                />
                <CustomSelect
                  v-if="getGoToYear.value.length && getGoToYear.value.toLowerCase() !== 'year'"
                  @selected="handleSelection"
                  className="custom_select__container custom-select_size__md"
                  commitPath="profileWallSettings/UPDATE_FILTER"
                  :errors="[]"
                  label=""
                  :value="getGoToMonth.value"
                  nameAttr="go_to_year"
                  :field="getGoToMonth.field"
                  :options="months"
                  :selected="monthSelected.length ? monthSelected : 'Month'"
                />
              </div>
              <p v-if="feedback.length" id="profile_wall_settings_feedback">{{ feedback }}</p>
              <div class="profile_wall_settings_filter_btns">
                <button type="button" @click.stop="clearFilters">Clear</button>
                <button type="submit" @click="applyFilters">Apply</button>
              </div>
          </form>
        </div>
      </div>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import { goToYears } from '../../data/profileWallSettingsData';

  import CustomSelect from '../forms/selects/CustomSelect.vue';
  import CloseSolidIcon from '../Icons/CloseSolidIcon.vue';
  import FilterSolidIcon from '../Icons/FilterSolidIcon.vue';

  export default {

    name : 'WallSettings',

    props: {
      currentUserId: Number,
      subjectUserId: String,
    },

    components: {
      CloseSolidIcon,
      FilterSolidIcon,
      CustomSelect,
    },

    data () {

      return {
        monthSelected: '',
        years: goToYears(),
        postedByOptions: [
          {name: 'Anyone', abbrv: 'Anyone', id: 1},
          {name: 'You', abbrv: 'You', id: 2},
          {name: 'Others', abbrv: 'Others', id: 3},
        ],
        months: [
            {name:'1', abbrv: 'Jan', id: 1},
            {name:'2', abbrv: 'Feb', id: 2},
            {name:'3', abbrv: 'Mar', id: 3},
            {name:'4', abbrv: 'Apr', id: 4},
            {name:'5', abbrv: 'May', id: 5},
            {name:'6', abbrv: 'Jun', id: 6},
            {name:'7', abbrv: 'Jul', id: 7},
            {name:'8', abbrv: 'Aug', id: 8},
            {name:'9', abbrv: 'Sep', id: 9},
            {name:'10', abbrv: 'Oct', id: 10},
            {name:'11', abbrv: 'Nov', id: 11},
            {name:'12', abbrv: 'Dec', id: 12},
        ],
      }
    },

    computed: {
      ...mapState('profileWallSettings',
          [
            'filters',
            'filtersShowing',
            'feedback',
          ]
        ),
      ...mapGetters('profileWallSettings',
        [
          'getPostedBy',
          'getGoToYear',
          'getGoToMonth',
          'checkFiltersOn',
        ]
      ),

      filterStyleIndicator() {

        return this.checkFiltersOn ? 'profile_wall_settings_filter_active': 'profile_wall_settings_filter_inactive';
      },
    },

    beforeDestroy() {

      this.$store.commit('profileWallSettings/RESET_MODULE');
    },

    methods: {

      ...mapMutations('profileWallSettings',
        [
          'OPEN_FILTERS',
          'CLOSE_FILTERS',
          'UPDATE_FILTER',
          'CLEAR_FILTERS',
          'SET_FEEDBACK',
        ]
      ),

      ...mapMutations('posts',
        [
          'SET_INITIAL_FILTER',
          'RESET_POSTS',
        ]
      ),

      ...mapActions('posts',
        [
          'LOAD_POSTS'
        ]
      ),

      handleSelection ({ selection }) {
        if (selection.field === 'go_to_month') {
          this.monthSelected = selection.selected;
        }
        this.UPDATE_FILTER(selection);
      },
      openFilters() {
        this.OPEN_FILTERS();
      },

      closeFilters() {
        this.CLOSE_FILTERS();
      },

      clearFilters () {
        this.CLEAR_FILTERS(['Who', 'Month', 'Year']);
        this.SET_INITIAL_FILTER(true);
        this.RESET_POSTS();

        this.debounce(async () => {
          await this.LOAD_POSTS(this.subjectUserId);
        }, 400);
      },

      applyFilters () {
        this.SET_FEEDBACK('');
        this.debounce(async () => {
          if (this.checkFiltersOn) {
            this.RESET_POSTS();
              await this.LOAD_POSTS(this.subjectUserId);
              this.closeFilters();
            } else {
              this.SET_FEEDBACK('Make sure to select an option for each box');
            }
        }, 400);
      },

      debounce(fn, delay = 400) {
        return ((...args) => {
          clearTimeout(this.debounceID);
          this.debounceID = setTimeout(() => {
              this.debounceID = null;
              fn(...args);
          }, delay);
        })();
      },
    }
  }
</script>


<style lang="scss">

.profile_wall_settings_container {
  margin: 0 auto;
  width: 80%;
  margin-top: 1.5rem;
  border-radius: 8px;
  background-color: $primaryGray;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px, rgb(0 0 0 / 4%) 0px 10px 10px -5px;
}

@media (max-width: 600px) {
  .profile_wall_settings_container {
    width: 100%;
  }
}

.profile_wall_settings_trigger_container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  box-sizing: border-box;

  h2 {
    font-size: 1.2rem;
    color: lighten($primaryBlack, 4);
    font-family: 'Secular One', sans-serif;
  }
}


.profile_wall_settings_filter_active {
  background-color: #4bb543;
  &:hover {
    background-color: darken(#4bb543, 4);
  }
  svg {
    color: darken($primaryWhite, 4);
  }
  p {
    color: darken($primaryWhite, 4);
  }
}

.profile_wall_settings_filter_inactive {
   background-color: lighten($mainInputLabel, 5);

  &:hover {
    background-color: $mainInputLabel,
  }
  svg {
    color: #fff;
  }
  p {
    color: #fff;
  }
}

.profile_wall_settings_trigger {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.5rem;
  border-radius: 8px;
  box-sizing: border-box;
  cursor: pointer;
  transition: all 0.25s ease-out;


  p {
    margin: 0.1rem;
    margin-left: 0.5rem;
  }
}

.profile_wall_settings_header {
  display:flex;
  align-items: center;
  justify-content: space-around;
  margin-left: 7rem;
  box-sizing: border-box;

  h2 {
    font-size: 1.1rem;
    color: lighten($primaryBlack, 5);
    font-family: 'Secular One', sans-serif;
  }
}

.profile_wall_settings_close {
  margin-left: 10rem;
  svg {
    height: 30px;
    width: 30px;
    color: lighten($primaryBlack, 5);
  }
}


.profile_wall_settings_divider {

  border-bottom: 1px solid darken($primaryGray, 5);
  padding: 0.3rem 0;
}

.profile_wall_settings_info {
  box-sizing: border-box;
  padding: 0.5rem;

  h4 {
    margin: 0.1rem;
    color: $mainInputLabel;
    font-size: 1rem;
    font-family: 'Open Sans', sans-serif;
    letter-spacing: 0.5px;
  }

  p {
    margin: 0.1rem;
    font-size: 0.8rem;
    color: gray;
    font-family: 'Open Sans', sans-serif;
  }
}

.posted_by_filter_container {
  display: flex;
  align-items: center;
  justify-content: flex-start;

  h4 {
    margin-top: 1.2rem;
    color: lighten($primaryBlack, 5);
    font-size: 1rem;
  }
}

.go_to_date_filter_container {
  display: flex;
  align-items: center;
  justify-content: flex-start;

   h4 {
    margin-top: 1.2rem;
    color: lighten($primaryBlack, 5);
    font-size: 1rem;
  }
}

.profile_wall_settings_filter_btns {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-end;
  align-items: center;

  button {
    cursor: pointer;
    border: none;
    border-radius: 8px;
    width: 100px;
    transition: all 0.25s ease-out;
    height: 35px;
    &:hover {
      opacity: 0.7;
    }
    &:first-of-type {
      background-color: darken($primaryGray, 5);
      margin-right: 0.5rem;
    }
    &:last-of-type {
      background-color: lighten($themeLightBlue, 3);
      color: #fff;
      margin-left: 0.5rem;
    }
  }
}

#profile_wall_settings_feedback {
  text-align: right;
  margin-right: 1.5rem;
}

@media (max-width:600px) {
  .profile_wall_settings_header {
    margin-left: 3rem;
  }
}


</style>
