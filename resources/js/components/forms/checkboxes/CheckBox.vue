<template>
  <label @change="handleCheckBox()" class="custom__checkbox">
    <input type="checkbox" :checked="isChecked ? 'checked': ''">
    <span class="custom_checkmark"></span>
    <div>
     <span>
       {{ text }}
     </span>
     <!-- <p>{{ checkboxError }}</p> -->
    </div>
  </label>
</template>


<script>

  export default {

    name: 'CheckBox',

    props: {
      text: String,
    },

    components: {

    },

    data () {

      return {
        isChecked: false,
      }
    },

    computed: {

    },

    methods: {
      handleCheckBox() {

        this.isChecked = !this.isChecked;

        this.$emit('checkbox', this.isChecked);

      }
    }
  }

</script>


<style lang="scss">

  @import '../../../../sass/general/_variables.scss';

.custom__checkbox {
  display: block;
  position: relative;
  padding-left: 35px;
  cursor: pointer;
  font-size: 22px;
  user-select: none;

  p {
    font-size: 0.7rem;
    color: $error;
  }

  div {
    margin-left: 0.5rem;
    span {
      font-size: 0.8rem;
      font-weight: bold;
      color: $mainInputLabel;
    }
  }

  a {
    font-size: 0.8rem;
    color: $themeBlue;
    text-decoration: none;
    font-weight: bold;
  }
}

.custom__checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.custom_checkmark {
  position: absolute;
  top: 10px;
  left: 10px;
  height: 20px;
  width: 20px;
  background-color: #fff;
  border: 2px solid $mainInputLabel;
  border-radius: 5px;
  font-size: 0.7rem;
}

.custom__checkbox input:checked ~ .custom_checkmark {
  background-color: $themeBlue;
  border: none;
  border-radius: 5px;
}

.custom_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.custom__checkbox input:checked ~ .custom_checkmark:after {
  display: block;
}

.custom__checkbox .custom_checkmark:after {
  left: 7px;
  top: 1px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>