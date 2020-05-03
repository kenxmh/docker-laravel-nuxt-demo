<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAdd()">新增路由规则</el-button>
    <el-table
      style="width: 100%;margin-top:30px;"
      v-loading="loading"
      border
      stripe
      row-key="id"
      :data="accessList"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column align="center" label="ID" prop="id" sortable>
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="Name" prop="name" sortable>
        <template slot-scope="scope">{{ scope.row.name }}</template>
      </el-table-column>

      <el-table-column align="center" label="Action" prop="action" sortable>
        <template slot-scope="scope">{{ scope.row.action }}</template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleEdit(scope)">修改信息</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="accessDialogVisible" :title="accessDialogType==='edit'?'Edit':'New'">
      <el-form :model="access" label-width="80px" label-position="left">
        <el-form-item label="name">
          <el-input v-model="access.name" placeholder="name" />
        </el-form-item>
        <el-form-item label="action">
          <el-input v-model="access.action" placeholder="action" />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="accessDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmAccess">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import {
  getAccesses,
  addAccess,
  updateAccess,
  deleteAccess
} from "@/api/access";

const defaultAccess = {
  id: "",
  name: "",
  action: ""
};

export default {
  data() {
    return {
      access: Object.assign({}, defaultAccess),
      index: 0,
      routes: [],
      accessList: [],
      accessDialogVisible: false,
      accessDialogType: "",
      loading: true
    };
  },
  created() {
    this.getAccesses();
  },
  methods: {
    async getAccesses() {
      const res = await getAccesses();
      this.accessList = res;
      this.loading = false;
    },
    handleAdd() {
      this.access = Object.assign({}, defaultAccess);
      this.accessDialogVisible = true;
      this.accessDialogType = "new";
    },
    handleEdit(scope) {
      this.accessDialogVisible = true;
      this.accessDialogType = "edit";
      this.checkStrictly = true;
      this.access = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleDelete({ $index, row }) {
      this.$confirm("是否确认删除?", "Warning", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(async () => {
          await deleteAccess(row.id);
          this.accessList.splice($index, 1);
          this.$message({
            type: "success",
            message: "删除成功"
          });
        })
        .catch(err => {
          console.error(err);
        });
    },
    async confirmAccess() {
      const isEdit = this.accessDialogType == "edit";

      let msg = "";
      if (isEdit) {
        await updateAccess(this.access);
        this.$set(this.accessList, this.index, this.access);
        this.accessDialogVisible = false;
        msg = "修改成功";
      } else {
        let res = await addAccess(this.access);
        this.accessList.push(res);
        this.accessDialogVisible = false;
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
