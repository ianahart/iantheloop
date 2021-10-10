<template>
  <div @click="emitToggle" :class="`factory_toggle_btn_container ${toggleClass.parent}`">
    <div :class="`factory_toggle_btn ${toggleClass.child}`"></div>
  </div>
</template>

<script>

  export default {
    name: 'ToggleBtn',
    props: {
      isToggled: Boolean,
      data: Object,
    },
    data () {
      return {
        active: false,
        initial: null,
      }
    },

    mounted() {
     this.initial = true;
    },

    computed: {
      toggleClass() {
        return {
          parent: this.isToggled ? 'toggle_blocked' : 'toggle_unblocked',
          child: this.isToggled ? 'factory_toggle_btn_blocked' : 'factory_toggle_btn_unblocked',
        };
      },
      variables() {

      },
    },
    methods: {
      emitToggle() {
        this.initial = false;
        this.$emit('toggle', { data: this.data, is_toggled: this.isToggled });
      }
    }
  }
</script>

<style lang="scss">
  .factory_toggle_btn_container {
    box-sizing: border-box;
    border-radius: 30px;
    width: 35px;
    height: 15px;
    position: relative;
    cursor: pointer;
  }
  .factory_toggle_btn {
    box-sizing: border-box;
    transition: all 0.35s ease-in-out;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    background-color: #656768;
    box-shadow: rgb(0 0 0 / 10%) 0px 20px 25px -5px,
        rgb(0 0 0 / 4%) 0px 10px 10px -5px;
  }

  .factory_toggle_btn_blocked {

     transform: translateX(0px);
  }

  .factory_toggle_btn_unblocked {
     transform: translateX(20px);
  }



  .toggle_blocked, .toggle_unblocked {
    transition: all 0.3s ease-in;
  }
  .toggle_blocked {
    background-color: rgb(30, 158, 30);
  }
  .toggle_unblocked {
    background-color: lighten($error, 4);
  }
</style>