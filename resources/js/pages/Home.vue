<template>
    <div class="home__container">
        <h1>HOME PAGE</h1>
        <router-link :to="{ name: 'NewsFeed' }">News feed</router-link>
    </div>
</template>

<script>
export default {
    name: "Home",


};
</script>

<style lang="scss">

.home__container {
    box-sizing: border-box;
    background-image: url("../../assets/home_bg.png");
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
}
</style>
