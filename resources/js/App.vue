<template>
    <div class="app__container">
        <Navbar />
        <router-view></router-view>
    </div>
</template>

<script>
/*
SASS
*/
import "./sass/app.scss";

export default {
    name: "App",

    components: {
        Navbar: () => import("./components/Navigation/Navbar.vue")
    }
};
</script>

<style lang="scss">
    #app {
        height: 100%;
    }

    .app__container {
        height: 100%;
    }

</style>
