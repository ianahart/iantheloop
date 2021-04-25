const navigation = {

  namespaced: true,

  state: {

    navigationLinks: [
      {component: 'Home', linkText: 'home'},
      {component: 'Login', linkText: 'login'},
      {component: 'CreateAccount', linkText: 'create account'},
      {component: 'About', linkText: 'about'},
    ],
    authNavigationLinks: [
      {component: 'Home', linkText: 'home'},
      {component: 'About', linkText: 'about'},
    ],
  },

  getters: {

  },

  mutations: {


  },

  actions: {

  }
}

export default navigation;