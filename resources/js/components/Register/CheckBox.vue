<template>
  <label @change="handleCheckBox" class="create__account__checkbox">
    <input type="checkbox" :checked="isChecked ? 'checked': ''">
    <span class="checkmark_checkmark"></span>
    <div>
     <span> I agree to LoOped's
       <a :href="url">Privacy Policy</a>
     </span>
     <p>{{ checkboxError }}</p>
    </div>
  </label>

</template>

<script>

  import { mapMutations, mapState } from 'vuex';

  export default {

    name: 'CheckBox',

    props: {
      url: String,
    },

    computed: {

      ...mapState('createAccount',
        [
          'isChecked',
          'checkboxError',
        ]
      ),
    },

    methods: {

      ...mapMutations('createAccount',
        [
          'TOGGLE_CHECKBOX'
        ]
      ),
      handleCheckBox() {

         this.TOGGLE_CHECKBOX();
      }
    },
  }

</script>

<style lang="scss">

.create__account__checkbox {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
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

.create__account__checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark_checkmark {
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

.create__account__checkbox input:checked ~ .checkmark_checkmark {
  background-color: $mainInputLabel;
  border: none;
  border-radius: 5px;
}

.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.create__account__checkbox input:checked ~ .checkmark_checkmark:after {
  display: block;
}

.create__account__checkbox .checkmark_checkmark:after {
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