<template>
  <div @click="toggleConversator" class="conversator_trigger">
    <PlusPlainIcon
      className="icon__xsm__light"
      v-if="!isConversatorOpen"
    />
    <CloseIcon
      className="icon__xsm__light"
      v-if="isConversatorOpen"
    />
    <h3>Conversator</h3>
  </div>
</template>

<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import PlusPlainIcon from '../Icons/PlusPlainIcon.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {
    name: 'ConversatorTrigger',
    props: {

    },
    components: {
      PlusPlainIcon,
      CloseIcon,
    },

    data () {
      return {

      }
    },

    computed: {
      ...mapState('conversator',
        [
          'isConversatorOpen',
          'contactsCount',
          'contacts',
          'chatWindowUserId'
        ]
      ),
      ...mapGetters('user', ['getUserId', 'getToken']),
    },

    methods: {
      ...mapMutations('conversator',
        [
          'TOGGLE_CONVERSATOR',
          'UPDATE_UNREAD_MESSAGE_COUNT'
        ]
      ),
     ...mapActions('conversator',
        [
          'GET_CONVERSATOR_CONTACTS'
        ]
      ),

        async toggleConversator() {

          try {

              this.TOGGLE_CONVERSATOR();

              if (this.isConversatorOpen && this.contactsCount !== this.contacts.length) {

                await this.GET_CONVERSATOR_CONTACTS();
              };
          } catch (err) {

            console.log('ConversatorTrigger.vue: ', err);
          }
        },
    }
  }
</script>

<style lang="scss">
  .conversator_trigger {
    display: flex;
    align-items: center;
    cursor: pointer;
    background-color: lighten($primaryBlack, 7);
    padding: 0.5rem;
    position: fixed;
    right: -46px;
    top: 20%;
    text-align: center;
    height: 30px;
    border-radius: 0.25rem;
    transform: rotate(-90deg);
    transition: all 0.3s ease-out;
    &:hover {
      background-color: lighten($primaryBlack, 3);
    }

    h3 {
      color: $primaryWhite;
      letter-spacing: 0.01rem;
      margin: 0;
      margin-left: 0.3rem;
      font-family: 'Secular One', sans-serif;
    }
  }

@media (max-width:600px) {
  .conversator_trigger {
    top: 17%;
    left: -45px;
    right:unset;
    z-index: 35;
    transform: rotate(90deg);
  }
}
</style>