<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAdd()">新增角色</el-button>
    <el-table
      style="width: 100%;margin-top:30px;"
      v-loading="loading"
      border
      stripe
      row-key="id"
      :data="roleList"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column align="center" label="ID" prop="id" sortable>
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="Name" prop="name" sortable>
        <template slot-scope="scope">{{ scope.row.name }}</template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleEdit(scope)">修改信息</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog
      id="roleDialog"
      :visible.sync="roleDialogVisible"
      :title="roleDialogType==='edit'?'Edit':'New'"
      @close="closeRoleDialog"
    >
      <el-form :model="role" label-width="80px" label-position="left">
        <el-form-item label="name">
          <el-input v-model="role.name" placeholder="name" />
        </el-form-item>
        <el-form-item label="accesses" style="max-height:500px; overflow-y: scroll;" >
          <el-tree 
            :data="accessList"
            show-checkbox
            node-key="id"
            ref="tree"
            highlight-current
            :props="{children: 'children', label: 'name'}"
          ></el-tree>
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="closeRoleDialog">取消</el-button>
        <el-button type="primary" @click="confirmRole">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import {
  getRoles,
  addRole,
  updateRole,
  deleteRole,
  getRoleAccesses
} from "@/api/role";
import { getAccesses } from "@/api/access";

const defaultRole = {
  id: "",
  name: "",
  accesses: ""
};

export default {
  data() {
    return {
      role: Object.assign({}, defaultRole),
      roleAccessList: [],
      index: 0,
      roleList: [],
      roleDialogVisible: false,
      roleDialogVisible: false,
      roleDialogType: "",
      loading: true,
      fullLoading: false,
      accessList: []
    };
  },
  created() {
    this.getRoles();
    this.getAccesses();
  },
  methods: {
    async getRoles() {
      const res = await getRoles();
      this.roleList = res;
      this.loading = false;
    },
    async getAccesses() {
      const res = await getAccesses();
      this.accessList = res;
    },
    handleAdd() {
      this.role = Object.assign({}, defaultRole);
      this.roleDialogVisible = true;
      this.roleDialogType = "new";
    },
    async handleEdit(scope) {
      this.roleDialogVisible = true;
      this.roleDialogType = "edit";
      this.checkStrictly = true;
      
      const loading = this.$loading({ 
          lock: true,
          target: '#roleDialog',
      });

      this.index = scope.$index;
      const role = deepClone(scope.row);
      this.role = role;

      const res = await getRoleAccesses(this.role.id);
      // this.roleAccessList = res;
      this.$nextTick(() => {
        this.$refs.tree.setCheckedNodes(res)
      });
      // await this.$nextTick();

      // this.$refs.tree.setCheckedNodes(this.roleAccessList);
      loading.close();
      
    },
    closeRoleDialog(){
      this.$refs.tree.setCheckedNodes([])
      this.roleDialogVisible = false;
    },
    handleDelete({ $index, row }) {
      this.$confirm("是否确认删除?", "Warning", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(async () => {
          await deleteRole(row.id);
          this.roleList.splice($index, 1);
          this.$message({
            type: "success",
            message: "删除成功"
          });
        })
        .catch(err => {
          console.error(err);
        });
    },
    async confirmRole() {
      const isEdit = this.roleDialogType == "edit";
      
      const tmpRole = this.role
      tmpRole.accesses = this.$refs.tree.getCheckedKeys()

      let msg = "";
      if (isEdit) {
        await updateRole(tmpRole);
        this.$set(this.roleList, this.index, this.role);
        this.roleDialogVisible = false;
        msg = "修改成功";
      } else {
        let res = await addRole(this.role);
        this.roleList.push(res);
        this.roleDialogVisible = false;
        msg = "创建成功";
      }

      this.$notify({
        type: "success",
        title: msg
      });
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
