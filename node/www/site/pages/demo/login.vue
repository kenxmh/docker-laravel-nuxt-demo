<template>
  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column"></div>
        <div class="column is-narrow">
          <div class="box" style="max-width: 500px;">
            <div id="registerForm">
              <h1 class="title" style="text-align:center">用户登录</h1>
              <div class="field">
                <label class="label">用户名</label>
                <div class="control">
                  <input v-model="form.username" class="input" type="text" placeholder="请输入用户名" />
                </div>
              </div>
              <div class="field">
                <label class="label">密码</label>
                <div class="control">
                  <input v-model="form.password" class="input" type="password" placeholder="请输入密码" />
                </div>
              </div>
              <div class="field is-grouped is-grouped-centered margin-top-lg">
                <div class="control">
                  <button class="button is-link is-medium" @click="login">登录</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="column"></div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  components: {},
  head() {
    return {
      title: "用户登录"
    };
  },
  data() {
    return {
      form: {
        username: "",
        password: "",
        action: "web-login"
      }
    };
  },
  methods: {
    // 提交注册
    async login() {
      try {
        await this.$store.dispatch("user/login", this.form);
        // utils.linkTo('/user/settings')
      } catch (e) {
        this.$toast.error(e.response.data.message || e.response.status);
      }
    }
  }
};
</script>

<style>
</style>
