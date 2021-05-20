<template>
  <div class="profile__dropdown">
    <div class="profile__dropdown__top">
      <div>
        <ProfileIcon />
      </div>
      <p>{{ userName }}</p>
    </div>
    <div class="profile__dropdown__links">
      <ul>
        <li>
          <div class="profile__dropdown__section">
            <div v-if="getProfileStatus" class="profile__dropdown__link">
              <BookIcon
                className="icon__sm__light"
              />
              <DropDownLink
                name="Profile"
                linkText="Your Profile"
                param="id"
                :paramValue="getUserId.toString()"
              />
            </div>
          </div>
        </li>
        <li>
          <div class="profile__dropdown__section">
            <div v-if="!getProfileStatus" class="profile__dropdown__link">
              <CreateProfileIcon />
              <DropDownLink
                name="CreateProfile"
                linkText="Create Profile"
              />
            </div>
            <div v-if="getProfileStatus" class="profile__dropdown__link">
                <SearchIcon
                  className="icon__sm__light"
                />
                <DropDownLink
                  name="FindFriends"
                  linkText="Find Friends"
                />
             </div>
              <div v-if="getProfileStatus" class="profile__dropdown__link">
                <FriendsIcon />
                <DropDownLink
                  name="Friends"
                  linkText="Friends"
                />
            </div>
          </div>
        </li>
        <li>
          <div class="profile__dropdown__section">
            <div @click="logout" class="profile__dropdown__link">
              <SignoutIcon />
              <span>Log Out</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>




<script>
  import BookIcon from '../Icons/BookIcon.vue';
  import CreateProfileIcon from '../../components/Icons/CreateProfileIcon.vue';
  import DropDownLink from '../../components/Dropdowns/DropDownLink.vue';
  import FriendsIcon from '../../components/Icons/FriendsIcon.vue';
  import ProfileIcon from '../Navigation/LinkIcons/ProfileIcon.vue';
  import SearchIcon from '../../components/Icons/SearchIcon.vue';
  import SignoutIcon from '../../components/Icons/SignoutIcon.vue';



  import { mapMutations, mapGetters, mapActions } from 'vuex';

  export default {

    name: 'ProfileDropdown',

    props: {

    },

    components: {
      BookIcon,
      CreateProfileIcon,
      DropDownLink,
      FriendsIcon,
      ProfileIcon,
      SearchIcon,
      SignoutIcon,
    },

    data () {

      return {

      }
    },

    created () {

    },

    mounted () {

    },

    watch:{

      '$route' () {

        this.closeDropdown();
      },
    },

    computed: {

      ...mapGetters('user',
        [
          'userName',
          'getProfileStatus',
          'getProfilePic',
          'getUserId'
        ]
      ),
    },

    methods: {
      ...mapActions('user',
          [
            'LOGOUT'
          ]
        ),

      ...mapMutations('profileDropdown',
          [
            'CLOSE_PROFILE_DROPDOWN'
          ]
        ),

      closeDropdown () {

        this.CLOSE_PROFILE_DROPDOWN(false);
      },

      async logout() {

        await this.LOGOUT();

        this.CLOSE_PROFILE_DROPDOWN(false);

        this.$router.push({ name: 'Login' });
      },
    },
  }


</script>


<style lang="scss">

  .profile__dropdown {
    height: 600px;
    border-radius: 8px;
    background-color: $primaryBlack;
    position: absolute;
    right:10px;
    top: 60px;
    box-shadow: 0px 0px 11px 1px rgba(0,0,0,1);
    z-index: 10;
    width: 190px;
    box-sizing: border-box;
  }

  .profile__dropdown__top {
    position: relative;
    width: 100%;
    display: flex;
    box-sizing: border-box;
    padding: 0 0.5rem;
    align-items: center;
    margin-bottom:1.5rem;


    div {

      &:first-of-type {
        margin-top: 0.1rem;
      }
    }


    p {
      margin-top: 0.3rem;
      color: darken($primaryWhite, 5);
    }
  }

  .profile__dropdown__links {

    ul {
      padding: 0 0.5rem;
    }

    li {
      list-style-type: none;
    }
  }

  .profile__dropdown__section {

    border-top: 1px solid darken(#838383, 5);
  }

  .profile__dropdown__link {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    cursor: pointer;
    border-radius: 5px;
    padding: 0.4rem 0.4rem;

    &:hover {
      background-color: rgba(105,105,105,0.5);
    }

    a {
      text-decoration: none;
    }

    span, a {
      color: $primaryWhite;
      font-size: 0.85rem;
    }
  }

  @media(max-width: 600px) {
    .profile__dropdown {
      height: auto;
      right: calc(300px - 190px);
      top: 300px;
    }
  }

</style>

