<template>
  <transition name="modal" appear>
    <div ref="modal" class="profile_stats_following__modal">
      <div class="following__modal__header">
        <p>{{ baseProfileData.display_name }}</p>
      </div>
      <div class="follow__modal_actions">
        <div @click="muteFollow" class="mute__action">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          <p>Mute</p>
        </div>
        <div @click="unFollow(baseProfileData.user_id)" class="unfollow__action">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
          </svg>
        <p>Unfollow</p>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>

  import { mapState, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'FollowingModal',

    mounted () {

      document.addEventListener('click', this.closeModal);


      },

      beforeDestroy() {

        document.removeEventListener('click', this.closeModal);
      },

   computed: {

     ...mapState('profile',
      [
        'isModalOpen',
        'baseProfileData'
      ]
    ),
   },

    methods: {

      ...mapActions('profile',
        [
          'UNFOLLOW'
        ]
      ),

      ...mapMutations('profile',
        [
          'CLOSE_MODAL'
        ]
      ),

      ...mapMutations('profileWall',
        [
          'RESET_MODULE'
        ]
      ),

      closeModal(e) {

        const modalRef = this.$refs.modal;

        if (e.target !== modalRef && e.target.className !== 'profile_stats_following_btn') {

          this.CLOSE_MODAL(false);
        }
      },

      muteFollow () {

      },

      async unFollow() {

        this.RESET_MODULE();
        await this.UNFOLLOW();

      }
    }
  }

</script>