<template>
  <div class="interaction_container">
    <p>A single Interaction</p>
    <p>{{ interaction.data.sender_name }}</p>
  </div>
</template>

<script>
/*
*Contents of an Interaction Notification:
*/
// {sender_name: "Dan Weatherman", recipient_name: "Bill Branson", sender_user_id: 17, recipient_user_id: 40, post_id: 196, …}
// blurb: "those vegetables make me hungry, really hungry"
// id: "ad20280f-6dcc-49f5-8b76-95f95a2c948c"
// photo_link: "https://hart-looped.s3.amazonaws.com/posts/1628353355branson_post.jpeg"
// post_id: 196
// recipient_name: "Bill Branson"
// recipient_user_id: 40
// sender_name: "Dan Weatherman"
// sender_profile_picture: "https://hart-looped.s3.amazonaws.com/60a6c999541f0weatherman_pro_2.jpeg"
// sender_user_id: 17
// text: "Dan Weatherman commented on a post on your wall."


  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {
    name: 'Interaction',
    props: {
      interaction: Object,
    },
    components: {

    },

    data () {
      return {

      }
    },

    computed: {

    },

    methods: {

    },


  }


</script>

<style lang="scss">

  .interaction_container {
    box-sizing: border-boxs;

    p {
      font-size: 3rem;
    }
  }

</style>