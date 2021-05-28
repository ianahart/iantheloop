<template>
  <transition name="network-item-fade" appear>
  <div @click="goToUserProfile(networkListItem.user_id)" class="network_list_item__container">
    <div class="network_list_item__image">
      <img
        class="profile_image_rel_md"
        v-if="networkListItem.profile_picture"
        :src="networkListItem.profile_picture"
        :alt="networkListItem.name"
      />
      <DefaultProfileIcon
        v-else
        className="default_profile_image_rel_md"
      />
    </div>
    <div class="network_list_item__main_info">
      <div class="network_list_item__main_info_names">
        <p>{{ networkListItem.name }}</p>
        <p>{{ networkListItem.display_name }}</p>
      </div>
      <div class="network_list_item__time">
        <p>{{ network }} since: <span>{{ networkListItem.follow_time }}</span></p>
      </div>
      <div class="network_list_item__work_info">
        <div v-if="networkListItem.company" class="network_list_item__work">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
          </svg>
          <p>{{ networkListItem.company }}</p>
        </div>
        <div  v-if="networkListItem.position" class="network_list_item__work">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
          </svg>
         <p>{{ networkListItem.position }}</p>
        </div>
      </div>
    </div>
    <div v-if="parseInt(userId) !== getUserId" class="network_list_item__action">
      <div v-if="networkListItem.curUserFollowing && networkListItem.user_id !== getUserId" class="network_list_item__action_container">
        <svg  xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        <p v-if="networkListItem.curUserFollowing">{{ network }}</p>
      </div>
    </div>
    <div v-else class="network_list_item__no_action"></div>
  </div>
  </transition>
</template>


<script>

  import { mapGetters } from 'vuex';
  import DefaultProfileIcon from '../Icons/DefaultProfileIcon.vue';

  export default {
    name: 'NetworkListItem',

    props: {
      networkListItem: Object,
      userId: String,
      network: String,
    },


    components: {

      DefaultProfileIcon,
    },

    computed: {
      ...mapGetters('user',
        [
          'getUserId',
        ]
      ),
    },

    methods: {
      goToUserProfile(userId) {

        this.$router.push({ name: 'Profile', params: { id: userId.toString() }})
      }
    },
  }

</script>

<style lang="scss">

.network-item-fade-enter-active, .network-item-fade-leave-active {
  transition: opacity 0.75s;
}
.network-item-fade-enter, .network-item-fade-leave-to {
  opacity: 0;
}

.network_list_item__container {
  display: flex;
  border-bottom: 1px solid $mainInputBorder;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;


  &:hover {
    background-color: rgba(98,30,44,0.1);
  }
}

.network_list_item__image {
  flex-grow: 1;
  svg {
    color: $themePink;
    background: $themeLightBlue;
  }
}

.network_list_item__main_info {

  flex-grow: 1;

  p {
    margin-bottom: 0.2rem;
    font-family: 'Open Sans', sans-serif;
    font-size: 0.85rem;
    color: gray;
    font-weight: 400;
  }
}

.network_list_item__main_info_names {
  display: flex;
  justify-content: center;

  p {
   &:first-of-type {
     margin-right: 0.5rem;
   }

   &:last-of-type {
     font-style: italic;
   }

  }
}

.network_list_item__time {
  display: flex;
  justify-content: center;

  p {
    font-weight: 100;
  }

   span {
    font-family: 'Open Sans', sans-serif;
    color: $themePink;
    font-size: 0.75rem;
    font-weight: 400;
  }
}

.network_list_item__work_info {
  display: flex;
  justify-content: space-evenly;
   p {
     margin-top: 0;
   }
  }

.network_list_item__work {
  display: flex;

    svg {
    margin-right: 0.4rem;
    height: 18px;
    width: 18px;
    color: $themePink;
    display: flex;
    align-items: center;
  }

}


.network_list_item__action {
  flex-grow: 1;
  display: flex;
  justify-content: flex-end;
}

.network_list_item__action_container {
  display: flex;
  align-items: center;
  background-color: darken($primaryGray, 2);
  border-radius: 8px;
  padding: 0.4rem 0.5rem;

  p {
    margin: 0;
    color: $primaryBlack;
    font-family: 'Secular one', sans-serif;
    font-size: 0.85rem;
  }

  svg {
    color: $primaryBlack;
    height: 18px;
    width: 18px;
    display: flex;
    align-items: center;
  }
}

.network_list_item__no_action {
  padding: 1rem;
  flex-grow: 1;
}

@media(max-width: 600px) {

  .network_list_item__container {
    width: 99%;
    flex-wrap: wrap;
  }

  .network_list_item__action {
    margin-top: 1.5rem;
    padding-bottom: 0.2rem;
    justify-content: center;
  }
}



</style>
