<template>
  <transition :name="optionTran" appear>
    <div class="flag_post_option__container">
      <div
        :class="`flag_option_group ${!option.selected ? xAxisPos : 'flag_option_selected'}`"
        @click="emitSelect(option)"
      >
        <p>{{ option.reasonText }}</p>
        <div
          class="remove_selected__flag_option"
          v-if="option.selected"
          @click="emitDeSelect(option)"
        >
          <CloseIcon
          />
        </div>
      </div>
    </div>
  </transition>
</template>

<script>

  import CloseIcon from '../../Icons/CloseIcon.vue';

  export default {

    name: 'FlaggedOption',

    props: {
      option: Object,
    },

    components: {
      CloseIcon,
    },

    computed: {

        xAxisPos() {
          return this.option.id % 2 === 0 ? 'flag_option_left' : 'flag_option_right';
        },

        optionTran() {
          return this.option.id % 2 === 0 ? 'flag-left' : 'flag-right';
        },
    },

    methods: {

      emitSelect(option) {
        this.$emit('selection', option);
      },

      emitDeSelect(option) {
        this.$emit('deselection', option);
      }
    }
  }

</script>

<style lang="scss">

.flag-left-enter-active, .flag-left-leave-active {
  transition: all 0.4s ease-out;
}
.flag-left-enter ,.flag-left-leave-to {
  transform: translate(100px);
  opacity: 0;
}

.flag-right-enter-active, .flag-right-leave-active {
  transition: all 0.4s ease-out;
}
.flag-right-enter ,.flag-right-leave-to {
  transform: translate(-100px);
  opacity: 0;
}

.remove_selected__flag_option {
  position: absolute;
  top: -1px;
  right: 1px;
}

.flag_post_option__container {

  color: #fff;

  p {
    font-size: 0.8rem;
    font-family: 'Open Sans', sans-serif;
    margin: 0.1rem;
    background-color: darken($primaryBlack, 4);
    box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    border-radius: 12px;
    padding: 0.3rem 0.6rem;
    cursor: pointer;
    transition: all 0.45s ease-out;
    &:hover {
      background-color: $primaryBlack;
    }
  }
}

.flag_option_group {
  position: relative;
}

.flag_option_left {
  display: flex;
  justify-content: flex-start;
  align-items: center;

  p {
    &:hover {
      transform: rotate(6deg);
    }
  }
}


.flag_option_selected {
  margin-right: auto;
  max-width: 150px;
  svg {
    height: 12px;
    width:12px;
  }
   p {
     margin: 0.2rem 0;
     background-color: rgba(72, 74, 164, 0.5);
    &:hover {
      background-color: rgba(72, 74, 164, 0.8);
      transform: rotate(6deg);
    }
  }
}

.flag_option_right {
  display: flex;
  justify-content: flex-end;
  align-items: center;

  p {
    &:hover {
      transform: rotate(-6deg);
    }
  }
}


</style>