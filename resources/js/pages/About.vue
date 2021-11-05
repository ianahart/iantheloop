<template>
    <div class="about_page">
        <div class="about_page_container">

            <section class="about_main_content_container">
                <img
                    :src="require('../../assets/about_main.jpeg').default"
                    alt="Office with glass windows"
                />
            </section>
            <section class="secondary_content_one_container">
                <div>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Blanditiis, ad maiores. Maxime recusandae enim non
                        debitis laudantium repudiandae quos culpa corporis
                        consequatur doloribus rem excepturi tempore temporibus
                        officia explicabo aperiam quibusdam veritatis distinctio
                        praesentium rerum, ipsum laboriosam modi iusto.
                        Aspernatur, sed fugit doloribus maxime ut minus non
                        laborum adipisci placeat.
                    </p>
                </div>
                <div>
                    <img
                        :src="require('../../assets/about_stars.svg').default"
                        alt="stars"
                    />
                    <img
                        :src="
                            require('../../assets/about_activity.svg').default
                        "
                        alt="a developer working on a computer"
                    />
                </div>
            </section>
            <section class="secondary_content_two_container">
                <div>
                    <h3>Creative Thinking</h3>
                    <p class="secondary_content_two_underline"></p>
                    <p>
                        Lorem, ipsum dolor sit amet consectetur adipisicing
                        elit. Rerum id quae quia nihil laborum ad sint nemo
                        recusandae voluptates at cum, est nesciunt qui ducimus
                        consequatur numquam assumenda officiis. Impedit facilis
                        nihil culpa sint ullam, voluptates aperiam! Deserunt,
                        odit sapiente!
                    </p>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Praesentium facere ullam illo iure eaque voluptatem
                        dolorem repellat magnam labore eum.
                    </p>
                </div>
                <div>
                    <img
                        :src="require('../../assets/about_secondary.jpeg').default"
                        alt="People huddled together looking at a laptop"
                    />
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: "About"
};
</script>
<style lang="scss">
.about_page {
    background-color: $primaryGray;
    min-height: 100%;
    p {
        font-size: 1, rem;
        color: #5c5f5f;
        line-height: 1.6;
    }
    section {
        margin: 2rem 0;
    }
}
.about_page_container {
    box-sizing: border-box;
    max-width: 1280px;
    margin: 6.5rem auto 0 auto;
    padding: 0.5rem;
}
.about_main_content_container {
    box-sizing: border-box;
    width: 100%;
    display: flex;
    justify-content: center;
    img {
        width: 100%;
    }
}

.secondary_content_one_container {
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    img {
        width: 60%;
        border: 1px solid #e7e8ea;
        border-radius: 8px;
        padding: 0.25rem;
    }
    img:first-of-type {
        margin-bottom: 1.5rem;
        padding-top: 1.5rem;
    }
    img:last-of-type {
        padding-bottom: 1.5rem;
    }
    div:first-of-type {
        box-sizing: border-box;
        background-color: #fff;
        padding: 5rem;
        width: 55%;
        align-self: baseline;
        height: 400px;
        display: flex;
        align-items: center;
    }
    div:last-of-type {
        box-sizing: border-box;
        display: flex;
        width: 45%;
        flex-direction: column;
        align-items: flex-end;
    }
}

.secondary_content_two_container .secondary_content_two_underline {
    height: 3px;
    background-color: #696767;
    width: 170px;
}

.secondary_content_two_container {
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    h3 {
        color: #635f5f;
        font-family: "Open Sans", sans-serif;
    }
    img {
        width: 100%;
    }
    div:first-of-type {
        box-sizing: border-box;
        background-color: #fff;
        padding: 2rem;
        width: 30%;
    }
    div:last-of-type {
        box-sizing: border-box;
        width: 70%;
        padding: 0 2rem;
    }
}

@media (max-width: 900px) {
    .about_page_container {
        max-width: 95%;
        margin: 0 auto;
        width: 95%;
    }
    .secondary_content_one_container {
        flex-direction: column;
        div:first-of-type {
            margin: 0 auto;
            width: 63%;
        }
        div:last-of-type {
            margin: 1.5rem 0;
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            img {
                width: 45%;
                padding: 0.5rem;
                margin: 0.1rem;
                min-height: 300px;
                height: 300px;
            }
        }
    }
    .secondary_content_two_container {
        flex-direction: column;
        div:first-of-type {
            width: 100%;
            padding: 0.5rem;
        }
        div:last-of-type {
            margin-top: 0.5rem;
            width: 100%;
            padding: 0;
        }
    }
}
@media (max-width: 600px) {
    .secondary_content_one_container {
        div:first-of-type {
            width: 100%;
            height: 100%;
            padding: 0.5rem;
        }
        div:last-of-type {
            width: 100%;
            padding: 0.5rem;
        }
    }
}
</style>
