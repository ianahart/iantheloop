<template>
        <div class="story_sidebar">
        <slot name="Sidebar"></slot>
       </div>
</template>

<script>

  export default {
    name: 'Sidebar',
    props: {

    },
    components: {

    }
  }
</script>
<style lang="scss">

 .story_sidebar {
    box-sizing: border-box;
    background-color: #3a3a3a;
    border-right: 1px solid #616161;
    flex-grow: 1;
    max-width: 500px;
  }

  @media(max-width:800px) {
    .story_sidebar {
      flex-grow: 1;
      width: 100%;
      max-width: 100%;
      margin-bottom: 1rem;
    }
  }
</style>