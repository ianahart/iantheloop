<template>
  <div class="profile_completion_bar__container">
    <h5><span>{{ getUserFirstName }}'s </span>Profile Completion</h5>
    <div class="profile_completion_row">
      <p>{{ completionBar }}%</p>
      <div class="profile_completion_bar">
        <div :style="`width: ${completionBar}%`" class="profile_completion_progess">
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapState, mapGetters } from 'vuex';

  export default {

    name: 'CompletionBar',

    computed: {

      ...mapState('profileAbout',
        [
          'completionBar',
        ]
      ),

      ...mapGetters('profileAbout',
        [
          'getUserFirstName'
        ]
      ),
    },
  }

</script>

<style lang="scss">

.profile_completion_bar__container {
  margin-right: 1rem;
  margin-top: 1rem;
  display: flex;
  flex-direction: column;
  align-items: flex-end;

  h5 {
    text-align: right;
    font-weight: 600;
    letter-spacing: -1px;
    color: darken(gray, 10);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 0 0.5rem;
    margin: 0.2rem 0;

    span {
      color: $themeLightBlue;
    }
  }
}

.profile_completion_row {
  display: flex;

    p {
    padding: 0 0.3rem;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: $themeLightBlue;
  }
}

.profile_completion_bar {
  box-sizing: border-box;
  position: relative;

  width: 180px;
  height: 40px;
  background-color: darken($primaryGray, 10);

}

.profile_completion_progess {
  position: absolute;
  top:0;
  left:1px;
  background-color:springgreen;
  height: 100%;
  display: flex;
  align-items: center;
}

</style>