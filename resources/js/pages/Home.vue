<template>
    <div class="home__container">
            <img v-if="!thresholdPassed" class="decorative_star_1 decorative_star" :src="require('../../assets/decoration-star.svg').default" alt="decorative star images" />
            <img v-if="!thresholdPassed" class="decorative_star_2 decorative_star" :src="require('../../assets/decoration-star.svg').default" alt="decorative star images" />
            <section class="home_main">
                <div class="home_main_row">
                    <div class="home_tagline_container">
                        <h1>Linking people globally</h1>
                        <p>Prosciutto pancetta short loin pork belly venison pork chop sirloin t-bone. Pastrami salami capicola chicken pork jowl tail turkey meatball filet mignon.</p>
                        <div class="home_tagline_actions">
                            <router-link :to="{name: 'CreateAccount'}">Signup</router-link>
                            <a href="mailto:looped@example.com?subject=Basic Information:">Contact</a>
                        </div>
                    </div>
                    <div class="home_main_images_container">
                        <img :src="require('../../assets/home_main_1.svg').default" alt="a user looking through their friend's list" />
                    </div>
                </div>
                <div class="home_main_statistics">
                  <div class="home_main_statistic">
                    <h1>{{ userCount }}</h1>
                    <p>Happy Users</p>
                 </div>
                   <div class="home_main_statistic">
                    <h1>{{ reviewCount }}</h1>
                    <p>Active Reviews</p>
                 </div>
                   <div class="home_main_statistic">
                    <h1>{{ reviewRating }}</h1>
                    <p>User Rating</p>
                 </div>
                </div>
        </section>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
export default {
    name: "Home",

    data () {
        return {
           screenWidth: '',
           thresholdPassed: false,
        }
    },
    async mounted () {
        window.addEventListener('resize', this.handleResize);
        await this.RETRIEVE_USER_COUNT();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.handleResize);
    },

    computed: {
        ...mapState([
            'userCount',
            'reviewCount',
            'reviewRating',
        ]),
    },

    methods: {
        ...mapActions([ 'RETRIEVE_USER_COUNT' ]),

        handleResize(e) {
            if (e.target.innerWidth <= 650) {
                if (!this.thresholdPassed) {
                    this.thresholdPassed = true;
                }
            } else {
                if (this.thresholdPassed) {
                    this.thresholdPassed = false;
                }
            }
        },
    },

};
</script>

<style lang="scss">


.home__container {
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    position: relative;
}

.home_main {
    box-sizing: border-box;
    margin: 0 auto;
    margin-top: 6.5rem;
    width:1140px;
}

.home_main_row {
    display: flex;
    justify-content: space-evenly;
}
.home_main_statistics {
    display: flex;
    justify-content: space-evenly;
}
.home_main_statistic {
    box-sizing: border-box;
    margin-top: 3rem auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    h1 {
        font-size: 6rem;
        color: $primaryBlack;
        font-family: 'Open Sans', sans-serif;
        margin-bottom: 0.05rem;
    }
    p {
      margin-top: -1rem;
      color: #6c717b;
    }
}

.home_tagline_container {
    box-sizing: border-box;
    width: 100%;
    display: flex;
    align-items: center;
    flex-direction: column;
    padding-right: 1rem;
    h1 {
        font-size: 4rem;
        color: $primaryBlack;
        font-family: 'Open Sans', sans-serif;
        font-weight: 700;
        margin-top: 8rem;
        text-align: left;
    }
    p {
        font-size: 1.125rem;
        color: #6c717b;
        line-height: 1.65;
    }
}

.home_main_images_container{
    box-sizing: border-box;
    padding-left: 1rem;
    width:690x;
    img {
        width: 600px;
    }
}

.decorative_star {
      z-index: -20;
      position: absolute;
}

.decorative_star_1 {
    height: 500px;
    width: 500px;
    left: -140px;
    top: 150px;
    margin-left: 2rem;
}

.decorative_star_2 {
    width: 600px;
    height: 600px;
    right: 0px;
    top: 50px;
}

.home_tagline_actions {
    box-sizing: border-box;
    a {
        box-sizing: border-box;
        padding: 1rem 1.5rem 1rem 1.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        line-height: inherit;
        text-align: center;
        transition: all 0.35s ease-in-out;
        margin: 0 1rem;
        font-weight: bold;
        font-family: 'Secular One',sans-serif;
        cursor: pointer;
        width: 170px;
        text-decoration: none;
        display: inline-block;
        &:first-of-type {
            background-color: $themeLightBlue;
            color: $primaryWhite;
            &:hover {
                border: 1px solid $themeLightBlue;
                background: transparent;
                color: $themeLightBlue;
            }
        }
        &:last-of-type {
            color: $themePink;
            background-color: transparent;
            border: 1px solid $themePink;
            &:hover {
                background-color: $themePink;
                color: $primaryBlack;
            }
        }
    }

}

@media (max-width: 950px) {
    .home_main {
        width: 95%;
    }
    .home_main_row {
        flex-direction: column;
        align-items: center;
        margin: 1rem auto;
    }

    .home_main_images_container {
        margin: 0 auto;
        margin-top: 2rem;
        padding: 0;
        text-align: center;

        img {
            width: 80%;
        }
    }

    .home_tagline_container {
        h1 {
            text-align: center;
        }
    }

    .decorative_star_1 {
        width: 250px;
        height: 250px;
    }

    .decorative_star_2 {
      width: 350px;
      height: 350px;
    }

    .home_tagline_actions {
        display: flex;
        justify-content: space-between;

        a {
            width: 100px;
            padding: 0.5rem 1rem 0.5rem 1rem;
        }
    }
}
</style>
