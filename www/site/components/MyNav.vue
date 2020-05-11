<template>
  <div class="container">
    <b-navbar class="has-background-white" :mobile-burger="false">
      <template slot="brand">
        <b-navbar-item tag="router-link" :to="{ path: '/' }">
          <img src="/logo.png" alt />
        </b-navbar-item>
      </template>
      <!-- <template slot="start">
        <b-navbar-item href="#" :active="true">个人博客</b-navbar-item>
      </template>-->

      <template slot="end">
        <b-navbar-item tag="div">
          <div class="buttons">
            <b-button tag="router-link" to="/register" type="is-primary">
              <strong>注册</strong>
            </b-button>
            <b-button tag="router-link" to="/login" type="is-light">登录</b-button>
          </div>
        </b-navbar-item>
      </template>
    </b-navbar>
  </div>
</template>


<script>
export default {
  data() {
    return {
      menu_is_active: false,
      isComponentModalActive: false,
      formProps: {
        email: "evan@you.com",
        password: "testing"
      }
    };
  },
  computed: {
    user() {
      return this.$store.state.user.admin;
    }
  },
  methods: {
    toggleActive() {
      this.menu_is_active = !this.menu_is_active;
    },
    goLogin() {
      this.$router.push({ path: `/login` });
    },
    goRegister() {
      this.$router.push({ path: `/register` });
    },
    async signout() {
      try {
        await this.$store.dispatch("user/signout");
      } catch (e) {
        this.$toast.error(e.response.data.msg || e.response.status);
      }
    }
  }
};
</script>

<style>
</style>
