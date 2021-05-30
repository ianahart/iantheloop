<template>
  <div class="dropdown_user_status__container">
    <div class="user_status_flex_item">
      <p>{{ status }}</p>
      <p :class="indicatorStyle"></p>
    </div>
    <div @click="toggleUserStatus"  :class="`status_toggle_btn__container ${toggleBorder}`">
      <span v-if="status === 'online'">&check;</span>
      <div :class="`status_toggle_btn ${toggleBtn}`"></div>
    </div>
  </div>
</template>



<script>

  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';

  export default {

    name: 'UserStatus',

    props: {
      status: String,
    },

    created () {

      if (this.status === 'online') {

        this.SET_INITIAL_TOGGLE_VALUE(true);
      } else if (this.status === 'offline') {
        this.SET_INITIAL_TOGGLE_VALUE(false);
      }
    },

    data () {
      return {
        debounceID: '',
      }
    },

    beforeDestroy() {

      window.clearTimeout(this.debounceID);
    },

    computed: {


      indicatorStyle () {

        return this.status === 'online' ? 'user_status_indicator_online' : 'user_status_indicator_offline';
      },

      toggleBtn() {

        return this.status === 'online' ? 'status_toggle_btn_online' : 'status_toggle_btn_offline';
      },

      toggleBorder () {

        return this.status === 'online' ? 'status_toggle_btn_container_online' : 'status_toggle_btn_container_offline';
      },



      ...mapState('user',
        [
        'statusToggledBtn',
        // 'status',
        ]
      ),
    },

    methods: {

      ...mapActions('user',
        [
          'UPDATE_USER_STATUS',
          'SET_INITIAL_STATUS',
        ]
      ),

      ...mapMutations('user',
        [
          'TOGGLE_STATUS_BTN',
          'SET_INITIAL_TOGGLE_VALUE',
        ]
      ),
      debounce(fn, delay = 1000) {

      return ((...args) => {

        clearTimeout(this.debounceID)

        this.debounceID = setTimeout(() => {

          this.debounceID = null

          fn(...args)
        }, delay)
      })()
      },
        async toggleUserStatus () {

          this.TOGGLE_STATUS_BTN();

          this.debounce(async() => {

            await this.UPDATE_USER_STATUS();
          }, 400);
        }
    },
  }
</script>

<style lang="scss">

.dropdown_user_status__container {
  display: flex;
  align-items: center;

  p {
    color: $primaryWhite;
    font-size: 0.8rem;
    font-weight: 100;
  }
}

.user_status_flex_item {
  pointer-events: none;
  display:flex;
  align-items: center;
}

.status_toggle_btn__container {
  position: relative;
  border-radius: 16px;
  width: 50px;
  height: 25px;
  margin-left: auto;
  cursor: pointer;
  padding: 0.1rem;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  background-color: #000;

  span {
    color: $themeLightBlue;
    pointer-events: none;
  }
}

.status_toggle_btn {
  box-sizing: border-box;
  padding: 0.1rem;
  top: 0;
  left:0;
  border-radius: 50px;
  height: 85%;
  width: 15px;
}

.status_toggle_btn_online {
  background-color: $themeLightBlue;
  transition: all 0.4s ease-out;
  transform: translateX(17px);
  pointer-events: none;
}

.status_toggle_btn_offline {
  background-color: $primaryWhite;
  transition: all 0.4s ease-out;
  pointer-events: none;
}

.status_toggle_btn_container_online {
  border: 2px solid $themeLightBlue;
  transition: all 0.4s ease-out;
}

.status_toggle_btn_container_offline {
  border: 2px solid $primaryWhite;
  transition: all 0.4s ease-out;
}


</style>