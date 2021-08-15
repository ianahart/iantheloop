<template>
  <div ref="profileDropDown" class="profile__dropdown">
    <div class="profile__dropdown__top">
      <div>
        <ProfileIcon location="dropDown" />
      </div>
      <p>{{ userName }}</p>
    </div>
    <div  class="dropdown_user_status">
      <p>{{ getStatus }}</p>
    </div>
    <div class="profile__dropdown__links">
      <ul>
          <li>
          <div class="profile__dropdown__section">
            <div ref="statusToggle" id="statusToggle">
              <UserStatus
              :status="getStatus"

              />
            </div>
          </div>
        </li>
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
            <div v-if="getProfileStatus" class="profile__dropdown__link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 icon__sm__light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
             </svg>
             <router-link :disabled="this.getUserSlug === $route.params.slug" :to="{name: 'NewsFeed', params: {slug: this.getUserSlug}}">Newsfeed</router-link>
             <!-- <span class="news_feed_dropdown_link">Newsfeed</span> -->
            </div>
          </div>
        <li>
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
                  name="Explore"
                  linkText="Explore"
                />
             </div>
              <div v-if="getProfileStatus" class="profile__dropdown__link">
              <FollowingIcon
                  className="icon__sm__light"
                />
                <DropDownLink
                  name="Following"
                  linkText="Following"
                  param="id"
                  :paramValue="getUserId.toString()"
                />
              </div>
              <div v-if="getProfileStatus" class="profile__dropdown__link">
                <FollowersIcon
                  className="icon__sm__light"
                />
                <DropDownLink
                  name="Followers"
                  linkText="Followers"
                  param="id"
                  :paramValue="getUserId.toString()"
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
  import ProfileIcon from '../Navigation/LinkIcons/ProfileIcon.vue';
  import SearchIcon from '../../components/Icons/SearchIcon.vue';
  import SignoutIcon from '../../components/Icons/SignoutIcon.vue';
  import FollowersIcon from '../Icons/FollowersIcon.vue';
  import FollowingIcon from '../Icons/FollowingIcon.vue';
  import UserStatus from '../User/UserStatus.vue';


  import { mapState, mapMutations, mapGetters, mapActions } from 'vuex';

  export default {

    name: 'ProfileDropdown',

    props: {

    },

    components: {
      BookIcon,
      CreateProfileIcon,
      DropDownLink,
      ProfileIcon,
      SearchIcon,
      SignoutIcon,
      FollowersIcon,
      FollowingIcon,
      UserStatus,
    },

    data () {

      return {

      }
    },

    created () {
      window.addEventListener('click', this.dropDownClickAway);
    },

    mounted () {

    },

    beforeDestroy() {
      window.removeEventListener('click', this.dropDownClickAway);
      this.closeDropdown();
    },

    watch:{

      '$route' () {

        this.closeDropdown();
      },
    },

    computed: {

      ...mapState('profileDropdown',
        [
          'isProfileDropdownOpen',
        ]
      ),

      ...mapGetters('user',
        [
          'userName',
          'getProfileStatus',
          'getProfilePic',
          'getUserId',
          'getStatus',
          'getUserSlug',
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
            'CLOSE_PROFILE_DROPDOWN',
          ]
        ),

      ...mapMutations('messenger',
          [
          'UPDATE_CONTACT_STATUS',
          'CHECK_PAGE'
          ]
        ),
        ...mapMutations('notifications',
          [
            'SET_NAV_ALERTS'
          ]
        ),

      closeDropdown () {

        this.CLOSE_PROFILE_DROPDOWN(false);
      },

      async logout() {

        await this.LOGOUT();

        this.leaveUserStatusChannel();
        this.leaveNotificationChannel();
        this.CLOSE_PROFILE_DROPDOWN(false);
        this.SET_NAV_ALERTS({ nav_interaction_alerts: 0, nav_message_alerts: false });
        this.$router.push({ name: 'Login' });

      },

      leaveUserStatusChannel() {
        Echo.leave('userstatus', (user) => {
          this.UPDATE_CONTACT_STATUS({...user, status: 'offline' });
        });
      },

      leaveNotificationChannel() {
        Echo.leave(`notifications.${this.getUserId}`);
      },

      dropDownClickAway(e) {

        const profileIcon = e.target.id === 'profileIcon';
        const statusContainer = e.target.id === 'statusToggle';
        const dropDown = this.$refs.profileDropDown === e.target;

        const [statusChildren, statusGrandChildren] = [
            [...this.$refs.statusToggle.children],
            [...this.$refs.statusToggle.children[0].children],
          ];

        const statusGreatGrandChild = [...statusGrandChildren[1].children][0];
        const statusGreatGrandChildSibling = [...statusGrandChildren[0].children][0];

        const isChild = statusChildren.includes(e.target);
        const isGrandChild = statusGrandChildren.includes(e.target);
        const isGreatGrandChild = statusGreatGrandChild === e.target;
        const isGreatGrandChildSibling = statusGreatGrandChildSibling === e.target;

        const conditions = [
          profileIcon,
          statusContainer,
          dropDown,
          isChild,
          isGrandChild,
          isGreatGrandChild,
          isGreatGrandChildSibling,
          ];
        const allPassed = conditions.every((condition) => condition === false);

        if (this.isProfileDropdownOpen && allPassed) {

          this.closeDropdown();
        }

      }
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
    z-index: 17;
  }

  .profile__dropdown__top {
    position: relative;
    width: 100%;
    display: flex;
    box-sizing: border-box;
    padding: 0 0.5rem;
    align-items: center;


    div {

      &:first-of-type {
        margin-top: 0.1rem;
      }
    }


    p {
      margin-top: 2rem;
      margin-bottom: 0;
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

  .dropdown_user_status {

    p {
      color: $primaryWhite;
      font-size: 0.85rem;
      text-align: center;
      margin: 0.1rem 0 0 0;
      font-weight: 100;
      color: silver;
    }
  }

  .profile__dropdown__section {

    border-top: 1px solid darken(#838383, 5);
  }

  .profile__dropdown__link {
    display: flex;
    box-sizing: border-box;
    align-items: center;
    cursor: pointer;
    border-radius: 5px;
    padding: 0.4rem 0.4rem;
    width:100%;

    &:hover {
      background-color: rgba(105,105,105,0.5);
    }

    a {
      text-decoration: none;
      width:100%;
    }

    span, a {
      color: $primaryWhite;
      font-size: 0.85rem;
      padding-left: 0.1rem;

    }
  }

  #statusToggle {
    border-radius: 5px;
    padding: 0.4rem 0.4rem;
    box-sizing: border-box;
  }

  @media(max-width: 600px) {
    .profile__dropdown {
      height: auto;
      right: calc(300px - 190px);
      top: 300px;
    }
  }

</style>

