<template>
  <div class="profile_work_preview">
    <p>{{ getBaseProfile.full_name }}</p>
    <p>Works as <span>{{ getBaseProfile.position }}</span></p>
  </div>
</template>

<script>

  import { mapGetters } from 'vuex';

  export default {

    name: 'WorkPreview',


    computed: {
      ...mapGetters('profile',
        [
          'getBaseProfile'
        ]
      ),
    }
  }
</script>

<style lang="scss">

  .profile_work_preview {
    color: gray;
    font-size: 0.9rem;

    span {
      color: $error,
    }
  }

</style>