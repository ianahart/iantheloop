<template>
  <div @click="toggleMessenger" class="messenger_trigger">
    <PlusPlainIcon
      className="icon__xsm__light"
      v-if="!isMessengerOpen"
    />
    <CloseIcon
      className="icon__xsm__light"
      v-if="isMessengerOpen"
    />
    <h3>Messenger</h3>
  </div>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';
  import PlusPlainIcon from '../Icons/PlusPlainIcon.vue';
  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {
    name: 'MessengerTrigger',
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
      ...mapState('messenger',
        [
          'isMessengerOpen',
          'contactsCount',
          'contacts',
        ]
      ),
    },

    methods: {
      ...mapMutations('messenger',
        [
          'TOGGLE_MESSENGER'
        ]
      ),
     ...mapActions('messenger',
        [
          'GET_MESSENGER_CONTACTS'
        ]
      ),

        async toggleMessenger() {

          try {

              this.TOGGLE_MESSENGER();

              if (this.isMessengerOpen && this.contactsCount !== this.contacts.length) {
                await this.GET_MESSENGER_CONTACTS();
              };
          } catch (err) {

            console.log('MessengerTrigger.vue: ', err);
          }
        }
    },
  }
</script>

<style lang="scss">
  .messenger_trigger {
    display: flex;
    align-items: center;
    cursor: pointer;
    background-color: lighten($primaryBlack, 7);
    padding: 0.5rem;
    position: fixed;
    right: -38px;
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
  .messenger_trigger {
    top: 17%;
    left: -45px;
    right:unset;
    z-index: 35;
    transform: rotate(90deg);
  }
}
</style>