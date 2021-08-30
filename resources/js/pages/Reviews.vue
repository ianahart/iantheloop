<template>
  <div class="reviews_container">
    <h2>Read what other users are saying</h2>
    <div class="review_sort_by_container">
      <CustomSelect
            @selected="handleSelection"
            className="custom_select__container custom-select_size__md"
            commitPath="reviews/UPDATE_FILTER"
            :errors="[]"
            label=""
            :value="filters[0].value"
            :nameAttr="filters[0].nameAttr"
            :field="filters[0].field"
            :options="options"
            :selected="filters[0].value"
          />
    </div>
       <div v-if="!alreadySubmitted && authenticated" class="reviews_form_heading">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
      </svg>
    <h4>Rate and Review</h4>
    </div>
    <Form
       v-if="!alreadySubmitted && authenticated"
    />
    <div class="reviews_list_container">
      <Review
        v-for="review in reviews"
        :key="review.id"
        :review="review"
      />
      <div class="reviews_pagination">
        <button v-if="pagination.page > 1" @click="paginate('prev')">Prev</button>
        <Logo v-if="pagination.page > 1 && pagination.page !== pagination.last_page"/>
        <button v-if="pagination.page !== pagination.last_page" @click="paginate('next')">Next</button>
      </div>
    </div>
  </div>
</template>


<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import Form from '../components/Reviews/Form.vue';
  import Review from '../components/Reviews/Review.vue';
  import Logo from '../components/Icons/Logo.vue';
  import CustomSelect from '../components/forms/selects/CustomSelect.vue';

  export default {
    name: 'Reviews',

    components: {
      Form,
      Review,
      Logo,
      CustomSelect,
    },

    data () {
      return {
        options: [
          {name: 'Newest', abbrv: 'Newest', id: 1},
          {name: 'Oldest', abbrv: 'Oldest', id: 2},
          {name: 'Lowest Rated', abbrv: 'Lowest Rated', id: 3},
          {name: 'Highest Rated', abbrv: 'Highest Rated', id: 4},
        ],
      }
    },

    async created () {
      await this.RETRIEVE_REVIEWS();
    },

    mounted() {

    },

    beforeDestroy() {
      this.RESET_REVIEW_MODULE();
    },

    computed: {
      ...mapState('reviews',
      [
        'alreadySubmitted',
        'authenticated',
        'pagination',
        'reviews',
        'filters',
      ]),
    },

    methods: {
      ...mapMutations('reviews',
        [
          'RESET_REVIEW_MODULE',
          'UPDATE_FILTER',
          'SET_PAGINATION',
        ]
      ),
      ...mapActions('reviews',
        [
          'RETRIEVE_REVIEWS'
        ]
      ),

      async handleSelection (selection) {
        this.UPDATE_FILTER(selection);
        this.SET_PAGINATION({total: 0, page: 1, last_page: null})
        await this.RETRIEVE_REVIEWS();
      },

      async paginate(order) {
        await this.RETRIEVE_REVIEWS(order)
      }
    }
  }


</script>

<style lang="scss">
.reviews_container {
  box-sizing: border-box;
  max-width: 1140px;
  width: 100%;;
  height: 100%;
  margin: 0 auto;
  margin-top: 2rem;
  h2 {
    color: #8e8e94;
    font-size: 20px;
    font-family: 'Open Sans', sans-serif;
    letter-spacing: 1px;
    text-align: center;
  }

}

.reviews_list_container {
  box-sizing: border-box;
  background-color: lighten($primaryGray, 3);
  border-radius: 8px;
  margin: 3rem 0;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
}

.review_sort_by_container {
  box-sizing: border-box;
  margin: 0 auto;
  width: 200px;
}

.reviews_pagination {
  box-sizing: border-box;
  width: 300px;
  margin: 1.5rem auto;
  display: flex;
  div {
    justify-content: space-evenly;
    align-items: center;
  }


   img {
    width: 35px;
    height: 35px;
  }

  button {
    width: 140px;
    font-size: 1.1rem;
    border: none;
    background-color: transparent;
    text-decoration: underline;
    cursor: pointer;
    color: $themeLightBlue;
    font-family: 'Secular One', sans-serif;
    margin: 0.3rem 1.2rem;
    transition: all 0.3s ease-in-out;

    &:hover {
      color: lighten($themeLightBlue, 7);
    }
  }
}

.reviews_form_heading {
  box-sizing: border-box;
  padding: 0.5rem;
  margin-top: 4rem;
  background-color: $themeLightBlue;
  width: 70%;
  margin: 0 auto;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  svg {
    height: 60px;
    width: 60px;
    color: lighten($themePink, 5);
  }

  h4 {
    color: $primaryWhite;
    margin: 0;
    margin-left: 3rem;
    font-size: 1.5rem;
    font-family: 'Secular One', sans-serif;
  }
}

.review_form_wrapper {
  box-sizing: border-box;
  width: 100%;
}

@media(max-width:600px) {
  .reviews_form_heading {
    width: 100%;
    font-size: 1rem;

    svg {
      height: 30px;
      width: 30px;
    }
    h4 {
      margin-left: 1.5rem;
    }
  }

  .reviews_pagination {
    display:flex;
    justify-content: center;
    width: 180px;
    button {
      width: 100%;
    }
  }
}
</style>