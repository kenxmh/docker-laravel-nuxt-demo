<template>
  <div class="app-container">
    <el-card class="box-card">
      <el-tabs v-model="activeName">
        <el-tab-pane label="个人信息" name="profile">
          <el-form :model="profileForm" ref="profileForm" label-width="80px">
            <el-form-item label="当前角色">
              <el-tag :key="role" v-for="role in roles">{{role}}</el-tag>
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="username" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="真实姓名">
              <el-input v-model="profileForm.realname"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="submitForm('profileForm')">提交</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="修改密码" name="password">
          <el-form
            :model="passwordForm"
            ref="passwordForm"
            :rules="passwordRules"
            label-width="80px"
          >
            <el-form-item label="旧密码" prop="oldPassword">
              <el-input type="password" v-model="passwordForm.oldPassword"></el-input>
            </el-form-item>
            <el-form-item label="新密码" prop="newPassword">
              <el-input type="password" v-model="passwordForm.newPassword"></el-input>
            </el-form-item>
            <el-form-item label="确认密码" prop="rePassword">
              <el-input type="password" v-model="passwordForm.rePassword"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="submitForm('passwordForm')">提交</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { updateUser } from "@/api/user";

const profileForm = {
  action: "profile",
  realname: ""
};

const passwordForm = {
  action: "password",
  oldPassword: "",
  newPassword: "",
  rePassword: ""
};

export default {
  name: "Profile",
  computed: {
    ...mapGetters(["realname", "username", "roles"])
  },
  data() {
    var checkPassword = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入密码"));
      } else {
        callback();
      }
    };
    var checkPassword2 = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请再次输入密码"));
      } else if (value !== this.passwordForm.newPassword) {
        callback(new Error("两次输入密码不一致!"));
      } else {
        callback();
      }
    };
    return {
      profileForm: Object.assign({}, profileForm),
      passwordForm: Object.assign({}, passwordForm),
      passwordRules: {
        oldPassword: [{ validator: checkPassword, trigger: "blur" }],
        newPassword: [{ validator: checkPassword, trigger: "blur" }],
        rePassword: [{ validator: checkPassword2, trigger: "blur" }]
      },
      activeName: "profile"
    };
  },
  created () {
    this.profileForm.realname = this.realname
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          var formData = "";
          if (formName == "passwordForm") {
            formData = this.passwordForm;
          } else if (formName == "profileForm") {
            formData = this.profileForm;
          }
          updateUser(formData).then(response => {
            this.$notify({
              type: "success",
              title: "修改成功"
            });
            if (formName == "passwordForm") {
              this.$refs["passwordForm"].resetFields();
            } else if (formName == "profileForm") {
              this.$store.dispatch('user/setProfile', this.profileForm.realname)
            }
          });
        } else {
          console.log("error submit!!");
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.el-form {
  max-width: 460px;
}
</style>
