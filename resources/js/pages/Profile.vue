<template>
   <div class="profile__container">
        <div v-if="dataLoaded && !restrictedProfile.status">
            <Modal />
            <div ref="profile" class="profile_component__wrapper">
                <Header />
                <ProfileStats/>
                <Dashboard />
                <ProfileWall
                :currentUserId="currentUserId"
                :currUserFollowing="currUserFollowing"
                :baseProfile="getBaseProfile"
                :currentUserFirstName="getBaseProfile.current_user_first_name"
                :viewUserFirstName="getBaseProfile.view_user_first_name"
                :currentUserProfilePic="getProfilePic"

                />
            </div>
        </div>
        <div class="restricted_profile_container" v-else-if="dataLoaded && restrictedProfile.status">
            <div v-if="restrictedProfile.user === 'current_user'" class="restricted_current_user">
              <p>You have blocked this user. To un-block, go to privacy inside your settings and remove them from blocked profiles.</p>
              <div>
                <router-link :to="{name: 'Settings', params: {slug: `${this.getUserSlug}`}}">Settings</router-link>
              </div>
            </div>
            <div class="restricted_viewing_user" v-if="restrictedProfile.user === 'viewing_user'">
              <p v-if="restrictedProfile.user === 'viewing_user'">Something went wrong trying to view this user's profile. They may have enabled certain settings to prevent you from seeing their content.</p>
              <div>
                <button v-if="!restrictedProfile.current_user_does_not_follow" @click="unfollow">Unfollow</button>
                <router-link :to="{name: 'Home'}">Return Home</router-link>
              </div>
            </div>
        </div>
        <Loader v-else />
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapState, mapActions } from "vuex";
import { debounce } from '../helpers/moduleHelpers.js';

import Header from "../components/Profile/Header.vue";
import Dashboard from "../components/Profile/Dashboard.vue";
import ProfileStats from "../components/Profile/ProfileStats.vue";
import ProfileWall from "../components/ProfileWall/ProfileWall.vue";
import Modal from '../components/ProfileWall/Modal.vue';
import Loader from '../components/Misc/Loader.vue';

export default {
    name: "Profile",

    components: {
        Header,
        Dashboard,
        ProfileStats,
        ProfileWall,
        Modal,
        Loader,
    },

    async created() {
        this.unfollow = debounce(this.unfollow, 250);
        await this.fetchBaseProfileData(this.$route.params.id);
    },
    beforeDestroy() {
        this.clearBaseProfileState();
        this.clearProfileWallState();
    },

    data() {
        return {
            paramChange: false
        };
    },


    watch: {
        "$route.params.id": function() {
            this.RESET_POSTS();
            this.RESET_MODULE();
            this.fetchBaseProfileData(this.$route.params.id);
        },

        fetchError() {
            if (this.fetchError) {
                this.RESET_MODULE();
                this.$router.push({ name: "NotFound" });
            }
        }
    },

    computed: {

        linkParam() {
            return this.$route.params.id;
        },
        ...mapState("profile",
            [
                "dataLoaded",
                "fetchError",
                "currentUserId",
                "currUserFollowing",
                'restrictedProfile'
            ]
        ),

        ...mapState('posts',
           [
            'currentUserFirstName'
           ]
        ),

        ...mapGetters('user',
            [
              'getProfilePic',
              'getUserSlug',
              'getUserId',
              'getToken',
              'userName',
            ]
        ),

        ...mapGetters('profile',
            [
            'getBaseProfile',
            ]
        ),
    },

    methods: {
        ...mapMutations("profile",
            [
              "RESET_MODULE"
            ]
        ),

        ...mapMutations("posts",
            [
                'SET_INITIAL_POST_INPUT_TEXT',
                'RESET_POSTS',
            ]
        ),

        ...mapActions("profile", ["FETCH_BASE_PROFILE_DATA", 'UNFOLLOW']),

        clearBaseProfileState() {
            this.RESET_MODULE();
        },

        clearProfileWallState() {

            this.$store.commit('posts/RESET_MODULE');
        },

        async unfollow() {
           try {
             await this.UNFOLLOW({ currentUserId: this.getUserId, viewingUserId: this.$route.params.id });
             this.$router.push({ name: 'Home' });
           } catch(e) {
           }
        },

        async fetchBaseProfileData(profileId) {
            await this.FETCH_BASE_PROFILE_DATA(profileId);
            this.setInitialPostInputText();
        },

          setInitialPostInputText() {
            this.SET_INITIAL_POST_INPUT_TEXT(
            {
                baseProfileUserId: this.getBaseProfile.user_id,
                currentUserId:this.currentUserId,
                'currentUserFirstName': this.userName.split(' ')[0],
                viewUserFirstName: this.getBaseProfile.view_user_first_name,
            }
            );
      },
    }
};
</script>

<style lang="scss">
.profile_component__wrapper {
    max-width: 960px;
    margin: 0 auto;
    height: 100%;
}

.profile__container {
    box-sizing: border-box;
    width: 100%;
    position: relative;
    height: 100%;
    min-height: 100vh;
}

.restricted_current_user, .restricted_viewing_user {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    div:first-of-type {
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-evenly;
        width: 100%;
    }
}


.restricted_profile_container {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 50%;
    width: 300px;
    border: 1px solid #e7dddd;
    margin: 0 auto;
    border-radius: 8px;
    margin-top: 3rem;
    a {
      color: gray;
      text-decoration-color: #e7dddd;
      font-size: 0.85rem;
    }
    button {
        background-color: transparent;
        border: 1px solid darken($primaryGray, 18);
        cursor: pointer;
        height: 30px;
        padding: 0.3rem 0.5rem;
        border-radius: 8px;
        color: gray;
        transition: 0.5s ease-out;
        &:hover {
        background-color: darken($primaryWhite, 5);
        }
    }
    p {
        color: #5a5858;
        font-size: 0.9rem;
        font-family: 'Open Sans',sans-serif;
        text-align: center;
        line-height: 1.6;
    }
}


</style>
