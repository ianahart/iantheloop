<template>
  <div class="edit_profile_section__nav">
    <nav>
      <PenSolidIcon
        className="icon__md__light"
        styles="margin-left: 0.5rem; color: #fb4d70;"
      />
      <ul>
        <NavBtn
          v-for="(section, idx) in windows"
          :key="idx"
          :text="section"
          :id=idx
          :currentWindow="currentWindow"
        />
      </ul>
    </nav>
  </div>
</template>

<script>

  import { mapGetters, mapState, mapMutations } from 'vuex';

  import NavBtn from './NavBtn.vue';
  import PenSolidIcon from '../Icons/PenSolidIcon.vue';

  export default {

    name: 'SectionNav',

    props: {

    },

    components: {
      NavBtn,
      PenSolidIcon,
    },

    computed: {
      ...mapState('profileEdit',
        [
          'windows',
          'currentWindow'
        ]
      ),
    },

    methods: {

    }
  }

</script>

<style lang="scss">
  .edit_profile_section__nav {
    height: 55px;
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: $themeLightBlue;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
      height: 100%;
    }

    ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
      height: 100%;
      display: flex;
      align-items: center;
    }
  }

  @media (max-width: 780px) {

  .edit_profile_section__nav {
    height: 140px;
    nav {
      display: flex;
      align-items: flex-start;
    }

    ul {
      display: flex;
      flex-wrap: wrap;

      li {
        margin: 0.3rem 1rem;
        height: auto;
      }
    }
  }
  }
</style>

