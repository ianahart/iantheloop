<template>
  <div class="interaction_container">
    <div
      @click="deleteInteraction(interaction)"
      class="delete_interaction_container"
    >
      <CloseIcon
        className="icon__sm__dark"
      />
    </div>
    <div class="interaction_contents">
      <UserPicture
        :src="interaction.data.sender_profile_picture"
        :alt="interaction.data.sender_name"
      />
      <p id="interaction_sender_text">{{ interaction.data.text }}</p>
      <img class="interaction_post_picture" v-if="interaction.data.photo_link" :src="interaction.data.photo_link" alt="a picture from the post of the interaction"/>
    </div>
     <p id="interaction_sender_blurb"><em>&ldquo;{{ interaction.data.blurb }}&rdquo;</em></p>
     <p id="interaction_created_at_date">{{ interaction.data.readable_date }}</p>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  import UserPicture from './UserPicture.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {
    name: 'Interaction',
    props: {
      interaction: Object,
    },
    components: {
      UserPicture,
      CloseIcon,
    },

    data () {
      return {

      }
    },

    computed: {

    },


    methods: {
      ...mapActions('notifications',
        [
          'DELETE_INTERACTION_NOTIFICATION'
        ]
      ),
      async deleteInteraction(interaction) {
        await this.DELETE_INTERACTION_NOTIFICATION(interaction);
      },
    },
  }


</script>

<style lang="scss">

  .interaction_container {
    box-sizing: border-box;
    margin: 1rem auto 0 auto;
  }

  .delete_interaction_container {
    margin-bottom: 0.3rem;
    display: flex;
    justify-content: flex-end;
    svg {
      height: 16px;
      width: 16px;
      color: darken($themePink, 10);
      transition: all 0.2s ease-out;
      &:hover {
        color: lighten($themePink, 5);
        transform: scale(1.1);
      }
    }
  }

  .interaction_contents {
    box-sizing: border-box;
    display: flex;
    justify-content: flex-start;
    p {
      font-size: 0.75rem;
    }
    img {
      margin: 0 0.2rem;
    }
  }

  .interaction_post_picture {
      width: 50px;
      height: 50px;
      border-radius: 8px;
  }

  #interaction_sender_text {
    margin: 0.2rem 0.3rem;
    color: darken($primaryWhite,5);
    overflow-wrap: break-word;
    word-wrap: break-word;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
}

  #interaction_sender_blurb {
    font-size: 0.75rem;
    margin: 0.2rem 0.3rem;
    color: darken($primaryWhite,5);
    overflow-wrap: break-word;
    word-wrap: break-word;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    -moz-hyphens: auto;
    hyphens: auto;
  }

  #interaction_created_at_date {
    text-align: left;
    margin: 0;
    font-size: 0.65rem;
    color: #fb4d70;
    margin-bottom: 0.25rem;
  }

</style>