<template>
  <label @change="handleCheckBox(checked)" class="custom__checkbox">
    <input type="checkbox" :checked="checked">
    <span class="custom_checkmark"></span>
    <div>
     <span>
       {{ text }}
     </span>
    </div>
  </label>
</template>


<script>

  export default {
    name: 'CheckBox',
    props: {
      text: String,
      checked: Boolean,
    },
    methods: {
      handleCheckBox() {
        this.$emit('checkbox');
      }
    }
  }

</script>

<style lang="scss">
</style>