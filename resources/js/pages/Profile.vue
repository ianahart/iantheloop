<template>
    <div v-if="dataLoaded" class="profile__container">
        <Modal />
        <div ref="profile" class="profile_component__wrapper">
            <Header />
            <ProfileStats />
            <Dashboard />
            <ProfileWall
              :currentUserId="currentUserId"
              :currUserFollowing="currUserFollowing"
              :baseProfile="getBaseProfile"
              :currentUserFirstName="currentUserFirstName"
              :viewUserFirstName="viewUserFirstName"
              :currentUserProfilePic="getProfilePic"

            />
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapState, mapActions } from "vuex";

import Header from "../components/Profile/Header.vue";
import Dashboard from "../components/Profile/Dashboard.vue";
import ProfileStats from "../components/Profile/ProfileStats.vue";
import ProfileWall from "../components/ProfileWall/ProfileWall.vue";
import Modal from '../components/ProfileWall/Modal.vue';

export default {
    name: "Profile",

    components: {
        Header,
        Dashboard,
        ProfileStats,
        ProfileWall,
        Modal,
    },

    async mounted() {

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

        viewUserFirstName () {

           return this.getBaseProfile.full_name.split(' ')[0];
        },


        linkParam() {
            return this.$route.params.id;
        },
        ...mapState("profile",
            [
                "dataLoaded",
                "fetchError",
                "currentUserId",
                "currUserFollowing",
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
              'getToken',
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
                "CURRENT_USER_NAME",
                'CURRENT_USER_FIRST_NAME',
                'SET_INITIAL_POST_INPUT_TEXT',
            ]
        ),

        ...mapActions("profile", ["FETCH_BASE_PROFILE_DATA"]),

        clearBaseProfileState() {
            this.RESET_MODULE();
        },

        clearProfileWallState() {

            this.$store.commit('posts/RESET_MODULE');
        },

        async fetchBaseProfileData(profileId) {

            await this.FETCH_BASE_PROFILE_DATA(profileId);
            let currentUserFullname =  JSON.parse(localStorage.getItem('user'))?.name;
            this.CURRENT_USER_NAME(currentUserFullname);
            this.setInitialPostInputText();
        },

          setInitialPostInputText() {

        this.SET_INITIAL_POST_INPUT_TEXT(
          {
            baseProfileUserId: this.getBaseProfile.user_id,
            currentUserId:this.currentUserId,
            viewUserFirstName: this.viewUserFirstName,
          }
        );
      },
    }
};
</script>

<style>
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


</style>
