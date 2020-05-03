<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAdd()">新增管理员</el-button>

    <el-table :data="adminList" style="width: 100%;margin-top:30px;" border v-loading="loading">
      <el-table-column align="center" label="ID">
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="角色">
        <template slot-scope="scope">{{ scope.row.role_name }}</template>
      </el-table-column>
      <el-table-column align="center" label="用户名">
        <template slot-scope="scope">{{ scope.row.username }}</template>
      </el-table-column>
      <el-table-column align="center" label="真实姓名">
        <template slot-scope="scope">{{ scope.row.realname }}</template>
      </el-table-column>
      <el-table-column align="center" label="状态">
        <template slot-scope="scope">
          <el-tag type="success" effect="dark" v-if="scope.row.status == 0">正常</el-tag>
          <el-tag type="warning" effect="dark" v-if="scope.row.status == 1">需重登</el-tag>
          <el-tag type="danger" effect="dark" v-if="scope.row.status == 2">封禁</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" min-width="120">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleReset(scope)">重置密码</el-button>
          <el-button type="warning" size="mini" @click="handleRole(scope)">修改角色</el-button>
          <el-button type="primary" size="mini" @click="handleEdit(scope)">修改信息</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="pwdDialogVisible" title="Reset Password">
      <el-form :model="admin" label-width="80px" label-position="left">
        <el-form-item label="用户名">
          <el-input v-model="admin.username" placeholder="用户名" disabled />
        </el-form-item>
        <el-form-item label="密码">
          <el-input v-model="admin.password" placeholder="密码" show-password />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="pwdDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmPwd">确认</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="roleDialogVisible" title="User Role edit">
      <el-form :model="admin" label-width="80px" label-position="left">
        <el-form-item label="用户名">
          <el-input v-model="admin.username" placeholder="用户名" disabled />
        </el-form-item>
        <el-form-item label="角色">
          <!-- 数据类型不同会导致无法选中 -->
          <el-select v-model="admin.role_id" placeholder="请选择" @change="roleChange">
            <el-option v-for="item in roleList" :key="item.id" :label="item.name" :value="item.id">
              <span style="float: left">{{ item.name }}</span>
              <!-- <span style="float: right; color: #8492a6; font-size: 13px">{{ item.name }}</span> -->
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="roleDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmRole">确认</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="adminAddDialogVisible" title="New">
      <el-form :model="admin" label-width="80px" label-position="left">
        <el-form-item label="role">
          <!-- 数据类型不同会导致无法选中 -->
          <el-select v-model="admin.role_id" placeholder="请选择">
            <el-option v-for="item in roleList" :key="item.id" :label="item.name" :value="item.id">
              <span style="float: left">{{ item.name }}</span>
              <!-- <span style="float: right; color: #8492a6; font-size: 13px">{{ item.name }}</span> -->
            </el-option>
          </el-select>
          <!-- <el-input v-model="admin.role_id" placeholder="role_id" /> -->
        </el-form-item>

        <el-form-item label="用户名">
          <el-input v-model="admin.username" placeholder="用户名" />
        </el-form-item>
        <el-form-item label="密码">
          <el-input v-model="admin.password" placeholder="密码" />
        </el-form-item>
        <el-form-item label="真实姓名">
          <el-input v-model="admin.realname" />
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="admin.email" />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="adminAddDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmAdmin">确认</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="adminEditDialogVisible" title="Edit">
      <el-form :model="admin" label-width="80px" label-position="left">
        <el-form-item label="用户名">
          <el-input v-model="admin.username" placeholder="username" disabled />
        </el-form-item>
        <el-form-item label="真实姓名">
          <el-input v-model="admin.realname" />
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="admin.email" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="admin.status">
            <el-radio :label="0">正常</el-radio>
            <el-radio :label="1">需重登</el-radio>
            <el-radio :label="2">封禁</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="adminEditDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmAdmin">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import {
  getAdmins,
  addAdmin,
  deleteAdmin,
  updateAdmin,
  resetAdminPwd,
  resetAdminRole
} from "@/api/admin";
import { getRoles } from "@/api/role";

const defaultAdmin = {
  id: "",
  username: "",
  password: "",
  realname: "",
  email: ""
};

export default {
  data() {
    return {
      admin: Object.assign({}, defaultAdmin),
      index: 0,
      routes: [],
      adminList: [],
      adminAddDialogVisible: false,
      adminEditDialogVisible: false,
      roleDialogVisible: false,
      pwdDialogVisible: false,
      roleList: [],
      loading: true
    };
  },
  created() {
    this.getAdmins();
    this.getRoles();
  },
  methods: {
    async getAdmins() {
      const res = await getAdmins();
      this.adminList = res;
      this.loading = false;
    },
    async getRoles() {
      const res = await getRoles();
      this.roleList = res;
    },
    handleRole(scope) {
      this.roleDialogVisible = true;
      this.checkStrictly = true;
      this.admin = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleReset(scope) {
      this.pwdDialogVisible = true;
      this.checkStrictly = true;
      this.admin = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleAdd() {
      this.admin = Object.assign({}, defaultAdmin);
      this.adminAddDialogVisible = true;
    },
    handleEdit(scope) {
      this.adminEditDialogVisible = true;
      this.checkStrictly = true;
      this.admin = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleDelete({ $index, row }) {
      this.$confirm("是否确认删除?", "Warning", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(async () => {
          await deleteAdmin(row.id);
          this.adminList.splice($index, 1);
          this.$message({
            type: "success",
            message: "删除成功"
          });
        })
        .catch(err => {
          console.error(err);
        });
    },
    roleChange(id) {
      let obj = {};
      obj = this.roleList.find(item => {
        return item.id === id;
      });
      this.admin.role_name = obj.name;
    },
    async confirmPwd() {
      await resetAdminPwd(this.admin);

      const { username, nickname } = this.admin;
      this.pwdDialogVisible = false;
      this.$notify({
        type: "success",
        title: "修改成功"
      });
    },
    async confirmRole() {
      await resetAdminRole(this.admin);
      if (this.admin.status == 0) {
        this.admin.status = 1;
      }
      // const { username, nickname } = this.admin
      this.$set(this.adminList, this.index, this.admin);
      this.roleDialogVisible = false;
      this.$notify({
        type: "success",
        title: "修改成功"
      });
    },
    async confirmAdmin() {
      const isEdit = this.adminEditDialogVisible;

      let msg = "";
      if (isEdit) {
        await updateAdmin(this.admin);
        this.$set(this.adminList, this.index, this.admin);
        this.adminEditDialogVisible = false;
        msg = "修改成功";
      } else {
        let res = await addAdmin(this.admin);
        this.adminList.push(res);
        this.adminAddDialogVisible = false;
        msg = "创建成功";
      }

      this.$notify({
        type: "success",
        title: msg
      });

      // this.getAdmins()
    }
  }
};
</script>

<style lang="scss" scoped>
.app-container {
  .roles-table {
    margin-top: 30px;
  }
  .permission-tree {
    margin-bottom: 30px;
  }
}
</style>
