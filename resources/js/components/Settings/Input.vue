<template>
  <div class="settings_input_container">
    <p v-if="error" class="incremental_search_error">{{ error }}</p>
    <div class="settings_input_actions">
      <div v-if="isUserToBlock" class="settings_input_user_to_block">
        <p class="settings_input_user_block">{{ userToBlock.full_name }}</p>
        <div @click="cancelBlockUser">
          <CloseIcon />
        </div>
      </div>
      <input
        v-if="!isUserToBlock"
        autocomplete="off"
        @keydown="handleInput"
        @focus="cancelBlockUser"
        :name="type"
        :value="value"
        placeholder="Enter user to block..."
      />
      <button :disabled="!isUserToBlock ? true : false" @click.stop="blockUser">Block</button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters, mapMutations, mapActions } from 'vuex';
  import { debounce } from '../../helpers/moduleHelpers.js';

  import CloseIcon from '../Icons/CloseIcon.vue';

  export default {
    name: 'Input',
    props: {
      type: String,
      error: String,
      value: String,
      active: Boolean,
    },
    components: {
       CloseIcon,
    },

    created() {
      this.handleInput = debounce(this.handleInput, 250);
      this.blockUser = debounce(this.blockUser, 250);
    },
    computed: {
      ...mapState('settings', ['userToBlock']),
      ...mapGetters('settings', ['getActiveInput']),

      isUserToBlock() {
        if (this.userToBlock) {
          return this.userToBlock.type === this.type ? true : false;
        }
        return false;
      }
    },

    methods: {
      ...mapMutations('settings', [
        'SET_BLOCK_INPUTS',
        'CLEAR_BLOCK_INPUTS',
        'RESET_SEARCH_PAGINATION',
        'SET_SEARCH_RESULTS',
        'SET_USER_TO_BLOCK',
      ]),
      ...mapActions('settings', ['SEARCH_NETWORK', 'BLOCK_USER']),

       async handleInput(e) {
         try {
           if (e.key.toLowerCase() === 'backspace') {
             this.clearInput();
             return;
           }
           this.RESET_SEARCH_PAGINATION();
           this.SET_BLOCK_INPUTS({ type: this.type, value: e.target.value });
           if (this.getActiveInput.value.trim().length && e.key.toLowerCase() !== 'backspace') {
             await this.SEARCH_NETWORK({ activeInput: this.getActiveInput, initiator: 'typing' });
          }
         } catch(e) {
         }
       },

       cancelBlockUser() {
          this.SET_USER_TO_BLOCK(null);
       },

       async blockUser() {
         try {
           await this.BLOCK_USER();
         }catch(e) {
         }
       },

       clearInput() {
          this.RESET_SEARCH_PAGINATION();
          this.SET_SEARCH_RESULTS({ searches: { data: [] }, initiator: 'clear' });
          if (this.getActiveInput !== undefined) {
           this.CLEAR_BLOCK_INPUTS({ type: this.getActiveInput.type });
          }
       },
    },
  }
</script>

<style lang="scss">
   .settings_input_container {
     box-sizing: border-box;
     display: flex;
     flex-direction: column;
     align-items: center;
   }

  .settings_input_actions {
    margin-top: 1rem;
    box-sizing: border-box;
    display: flex;
    width:100%;
    justify-content: center;
    align-items: center;
    input {
      &:focus {
        color: $themeLightBlue;
        width: 2px;
      }
      border-radius: 8px;
      color: $themeLightBlue;
      outline: none;
      width: 50%;
      min-width: 50%;
      height: 35px;
      border:none;
      border: 1px solid #636366;
      background-color: transparent;
      padding: 0.25rem;;
    }
    button {
      margin-left: 1.2rem;
      height: 35px;
      border-radius: 10px;
      border: none;
      padding: 0.5rem 1rem;
      cursor: pointer;
      font-weight: bold;
      color: darken($primaryWhite, 3);
      background-color: lighten($themeLightBlue, 3);
      &:hover {
        background-color: darken($themeLightBlue, 5);
      }
    }
  }

  .settings_input_user_to_block {
    display: flex;
    justify-content: center;
    box-sizing: border-box;
    align-items: center;
    padding: 0.5rem;
    background-color: #2b2c2c;
    box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    p {
      color: $themePink;
      font-size: 0.9rem;
      font-family: 'Open Sans', sans-serif;
      text-align: center;
      margin: 0;
      margin-right: 0.4rem;
    }
    svg {
      height: 20px;
      width: 20px;
      color: darken($primaryWhite, 5);
      background-color: transparent;
    }
  }

  .incremental_search_error {
    color: #fff;
    font-size: 0.8rem;
    text-align: center;
    font-weight: bold;
  }

  @media(max-width:600px) {
    .settings_input_container {
      width: 100%;
    }
    .settings_input_actions {
    margin-top: 1rem;
    box-sizing: border-box;
    align-items: center;
    display: inherit;
    width: 100%;
    input {
      width: 100%;
    }
   }
  }
</style>
