<template>
  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column"></div>
        <div class="column is-narrow">
          <div class="box" style="max-width: 500px;">
            <div id="registerForm">
              <h1 class="title" style="text-align:center">用户注册</h1>
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
              <label class="label">手机</label>
              <div class="field is-horizontal">
                <div class="field-body">
                  <div class="field has-addons">
                    <p class="control">
                      <span class="select">
                        <select>
                          <option>+86</option>
                        </select>
                      </span>
                    </p>
                    <p class="control is-expanded">
                      <input class="input" type="text" placeholder="手机" />
                    </p>
                  </div>
                  
                </div>
              </div>
              <label class="label">验证码</label>
              <div class="field is-horizontal">
                <div class="field-body">
                  <div class="field has-addons">
                    <p class="control is-expanded">
                      <input v-model="form.vcode" class="input" type="text" placeholder="请输入验证码" />
                    </p>
                    <p class="control">
                      <button class="button is-info">获取验证码</button>
                    </p>
                  </div>
                  
                </div>
              </div>
              <div class="field is-grouped is-grouped-centered margin-top-lg">
                <div class="control">
                  <button class="button is-info is-medium" @click="register">注册</button>
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
      title: "用户注册"
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
    async register() {
      try {
        await this.$store.dispatch("user/register", this.form);
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
