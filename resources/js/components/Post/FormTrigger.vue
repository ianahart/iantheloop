<template>
  <div class="form_trigger__container">
    <section id="top_trigger_form">
      <img v-if="currentUserProfilePic"  :src="currentUserProfilePic" />
      <DefaultProfileIcon
        v-else
        className="default_profile_image_rel_md"
      />
      <div @click="launchPostForm">
        <p v-if="currentUserId === parseInt(baseProfile.user_id)">
          Want to share your thoughts, {{ currentUserFirstName }}?
        </p>
        <p v-else>Write something to {{ viewUserFirstName }}...</p>
      </div>
    </section>
    <section id="bottom_trigger_form">
      <div class="trigger_form_separator"></div>
      <div class="trigger_form_options">
        <div @click="launchPostForm" class="trigger_form_option">
          <div>
            <PictureSolidIcon
              className="icon__sm__theme"
            />
          </div>
          <p>Photo</p>
        </div>
        <div @click="launchPostForm" class="trigger_form_option">
          <div>
            <VideoSolidIcon
              className="icon__sm__theme"
            />
          </div>
          <p>Video</p>
        </div>

      </div>
    </section>
    <Form
     :baseProfile="baseProfile"
     :currentUserId="currentUserId"
     :currentUserFirstName="currentUserFirstName"
     :viewUserFirstName="viewUserFirstName"
     :currentUserProfilePic="currentUserProfilePic"
    />
  </div>
</template>

<script>

  import { mapState, mapMutations } from 'vuex';

  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';
  import PictureSolidIcon from '../Icons/PictureSolidIcon.vue';
  import VideoSolidIcon from '../Icons/VideoSolidIcon.vue';
  import Form from './Form.vue';

  export default {

    name: 'FormTrigger',

    props: {
      baseProfile: Object,
      currentUserId: Number,
      viewUserFirstName: String,
      currentUserProfilePic: String,
    },

    components: {
      DefaultProfileIcon,
      PictureSolidIcon,
      Form,
      VideoSolidIcon,
    },

    computed: {
      ...mapState('posts',
        [
          'currentUserFirstName'
        ]
      ),

    },

    methods: {

      ...mapMutations('posts',
        [
          'OPEN_MODAL'
        ]
      ),
      launchPostForm () {

        this.OPEN_MODAL({ modal: 'create_post', activeFlagPostId: '' });
      },
    },
  }
</script>

<style lang="scss">

.form_trigger__container {
  position: relative;
  box-sizing: border-box;
  background-color: $primaryGray;
  border-radius: 8px;
  box-shadow: rgba(0, 0, 0, 0.1) 0px 20px 25px -5px, rgba(0, 0, 0, 0.04) 0px 10px 10px -5px;
  padding: 1.5rem 0.5rem;
  width: 100%;
}

#top_trigger_form {
  box-sizing: border-box;
  display: flex;
  justify-content: flex-start;
  align-items: center;

  img {
    height: 75px;
    width:75px;
    border-radius: 50%;
  }

  svg {
    color: $themePink;
    background: $themeLightBlue;
  }

  div {
    display: flex;
    align-items: center;
    cursor: pointer;
    box-sizing: border-box;
    margin-left: 2rem;
    width: 85%;
    padding: 0.5rem;
    border-radius: 16px;
    height: 50px;
    border: 1px solid darken($primaryGray, 3);
    background-color: $primaryWhite;
    text-align: left;
    font-size:0.95rem;
    color: gray;

    p {
      margin: 0.1rem;
    }
  }
}

#bottom_trigger_form {
  box-sizing: border-box;
  padding: 1.5rem 0.5rem 0 0.5rem;
}

.trigger_form_separator {
  height: 2px;
  border-top: 2px solid darken($primaryGray, 3);
}

.trigger_form_options {
  display: flex;
  align-items: center;
  justify-content: space-evenly;
  padding: 0.5rem 0.5rem 0 0.5rem;
  box-sizing: border-box;
}

.trigger_form_option {
  display: flex;
  cursor: pointer;

  div {
    &:first-of-type {
     transform: rotate(-15deg);
    }
  }

  p {
    margin: 0.1rem;
    margin-left: 0.3rem;
    color: gray;
    cursor: pointer;
    font-size: 0.95rem;
    display:flex;
    flex-direction: column;
    justify-content: flex-end;
  }
}

@media (max-width: 600px) {
  #top_trigger_form {
    flex-direction: column;
    justify-content: center;

    div {
      margin: 0 auto;
      margin-top: 1.2rem;
      width: 99%;
    }
  }
}

</style>