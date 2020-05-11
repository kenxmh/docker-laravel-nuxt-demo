<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAdd()">新增文章分类</el-button>
    <el-table
      style="width: 100%;margin-top:30px;"
      v-loading="loading"
      border
      stripe
      row-key="id"
      :data="categoryList"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column align="center" label="ID" prop="id" sortable>
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="Name" prop="name" sortable>
        <template slot-scope="scope">{{ scope.row.name }}</template>
      </el-table-column>
      <el-table-column align="center" label="key" prop="key" sortable>
        <template slot-scope="scope">{{ scope.row.key }}</template>
      </el-table-column>
      <el-table-column align="center" label="color" prop="color" sortable>
        <template slot-scope="scope">{{ scope.row.color }}</template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleEdit(scope)">修改信息</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="categoryDialogVisible" :title="categoryDialogType==='edit'?'Edit':'New'">
      <el-form :model="category" label-width="80px" label-position="left">
        <el-form-item label="name">
          <el-input v-model="category.name" placeholder="name" />
        </el-form-item>
        <el-form-item label="name">
          <el-input v-model="category.key" placeholder="key" />
        </el-form-item>
        <el-form-item label="color">
          <el-input v-model="category.color" placeholder="color" />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="categoryDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmCategory">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import {
  getCategories,
  addCategory,
  updateCategory,
  deleteCategory
} from "@/api/article";

const defaultCategory = {
  id: "",
  name: "",
  key:"",
  color: "",
};

export default {
  data() {
    return {
      category: Object.assign({}, defaultCategory),
      index: 0,
      routes: [],
      categoryList: [],
      categoryDialogVisible: false,
      categoryDialogType: "",
      loading: true
    };
  },
  created() {
    this.getCategories();
  },
  methods: {
    async getCategories() {
      const res = await getCategories();
      this.categoryList = res;
      this.loading = false;
    },
    handleAdd() {
      this.category = Object.assign({}, defaultCategory);
      this.categoryDialogVisible = true;
      this.categoryDialogType = "new";
    },
    handleEdit(scope) {
      this.categoryDialogVisible = true;
      this.categoryDialogType = "edit";
      this.checkStrictly = true;
      this.category = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleDelete({ $index, row }) {
      this.$confirm("是否确认删除?", "Warning", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(async () => {
          await deleteCategory(row.id);
          this.categoryList.splice($index, 1);
          this.$message({
            type: "success",
            message: "删除成功"
          });
        })
        .catch(err => {
          console.error(err);
        });
    },
    async confirmCategory() {
      const isEdit = this.categoryDialogType == "edit";

      let msg = "";
      if (isEdit) {
        await updateCategory(this.category);
        this.$set(this.categoryList, this.index, this.category);
        this.categoryDialogVisible = false;
        msg = "修改成功";
      } else {
        let res = await addCategory(this.category);
        this.categoryList.push(res);
        this.categoryDialogVisible = false;
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
