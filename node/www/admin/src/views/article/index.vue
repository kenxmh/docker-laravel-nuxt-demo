<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAdd()">新增文章</el-button>
    <el-table
      style="width: 100%;margin-top:30px;"
      v-loading="loading"
      border
      stripe
      row-key="id"
      :data="articleList.data"
      @sort-change="sortChange"
    >
      <el-table-column align="center" label="ID" prop="id" sortable width="100">
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="Title" prop="title">
        <template slot-scope="scope">{{ scope.row.title }}</template>
      </el-table-column>
      <el-table-column align="center" label="分类" prop="categories">
        <template slot-scope="scope">
          <el-tag
            size="mini"
            v-for="category in scope.row.categories"
            v-bind:key="category.id"
            style="margin-right:5px;"
          >{{ category.name }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Views" prop="views" sortable width="120">
        <template slot-scope="scope">{{ scope.row.views }}</template>
      </el-table-column>
      <el-table-column align="center" label="comments" prop="comments" sortable width="140">
        <template slot-scope="scope">{{ scope.row.comments }}</template>
      </el-table-column>
      <el-table-column align="center" label="sort" prop="sort" sortable width="100">
        <template slot-scope="scope">{{ scope.row.sort }}</template>
      </el-table-column>
      <el-table-column align="center" label="操作" width="300">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleEdit(scope)">修改信息</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div class="block" style="margin-top:30px;">
      <el-pagination
        layout="prev, pager, next, jumper"
        background
        :page-size="10"
        :pager-count="7"
        :hide-on-single-page="true"
        @current-change="handleCurrentChange"
        :total="articleList.total"
      ></el-pagination>
    </div>

    <el-dialog
      :visible.sync="articleDialogVisible"
      :title="articleDialogType==='edit'?'Edit':'New'"
      :close-on-press-escape="false"
      :close-on-click-modal="false"
      fullscreen
    >
      <el-form :model="article" label-width="80px" label-position="top" :inline="true">
        <el-form-item label="标题" style="width:40%">
          <el-input v-model="article.title" placeholder="title" />
        </el-form-item>
        <el-form-item label="分类" style="width:40%">
          <el-select
            v-model="article.categories"
            value-key="id"
            multiple
            placeholder="请选择"
            style="width:100%"
          >
            <el-option v-for="item in categoryList" :key="item.id" :label="item.name" :value="item"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="排序" style="width:10%">
          <el-input v-model="article.sort" placeholder="sort" />
        </el-form-item>
        <el-form-item label="内容" style="width:100%;">
          <mavon-editor ref="md" v-model="article.body" @imgAdd="$imgAdd" style="height: 580px"></mavon-editor>
        </el-form-item>
      </el-form>
      <div style="text-align:center;">
        <el-button type="primary" @click="confirmArticle">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import {
  getCategories,
  getArticles,
  addArticle,
  updateArticle,
  deleteArticle,
  addImage
} from "@/api/article";

// import mavonEditor from 'mavon-editor'
// import 'mavon-editor/dist/css/index.css'

// import MavonEditor from "@/components/MavonEditor/index.vue";
import { mavonEditor } from "mavon-editor";
import "mavon-editor/dist/css/index.css";

const defaultArticle = {
  id: "",
  title: "",
  body: "",
  sort: "",
  categories: []
};

export default {
  components: {
    'mavon-editor': mavonEditor
  },
  data() {
    return {
      article: Object.assign({}, defaultArticle),
      index: 0,
      routes: [],
      articleList: [],
      categoryList: [],
      articleDialogVisible: false,
      articleDialogType: "",
      loading: true,
      query: {
        prop: "",
        order: "",
        page: 1,
        category: ""
      }
    };
  },
  created() {
    this.getArticles();
  },
  mounted() {},
  watch: {
    query: {
      deep: true,
      handler: function(newObj, oldObj) {
        this.loading = true;
        getArticles(this.query)
          .then(response => {
            this.articleList = response;
          })
          .catch(error => {
            this.$message({
              type: "error",
              message: "获取失败"
            });
          })
          .finally(() => {
            this.loading = false;
          });
      }
    }
  },
  methods: {
    async getArticles() {
      this.loading = true;
      this.articleList = await getArticles(this.query);
      this.categoryList = await getCategories();
      this.loading = false;
    },
    sortChange({ prop, order }) {
      console.log(order);
      if (order == null) {
        this.query.prop = "";
        this.query.order = "";
      } else {
        this.query.prop = prop;
        this.query.order = order == "ascending" ? "asc" : "desc";
      }
    },
    handleCurrentChange(page) {
      this.query.page = page;
    },
    handleAdd() {
      this.article = Object.assign({}, defaultArticle);
      this.articleDialogVisible = true;
      this.articleDialogType = "new";
    },
    async handleEdit(scope) {
      this.articleDialogVisible = true;
      this.articleDialogType = "edit";
      this.checkStrictly = true;
      this.article = deepClone(scope.row);
      this.index = scope.$index;
    },
    handleDelete({ $index, row }) {
      this.$confirm("是否确认删除该角色?", "Warning", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(async () => {
          await deleteArticle(row.id);
          this.articleList.data.splice($index, 1);
          this.$message({
            type: "success",
            message: "删除成功"
          });
        })
        .catch(err => {
          console.error(err);
        });
    },
    async confirmArticle() {
      const isEdit = this.articleDialogType == "edit";
      // 复制一个article对象
      const temp = deepClone(this.article)
      const categories = []
      temp.categories.forEach(item =>{
            categories.push(item.id)
      })
      temp.categories = categories
      
      let msg = "";
      if (isEdit) {
        await updateArticle(temp);
        this.$set(this.articleList.data, this.index, this.article);
        this.articleDialogVisible = false;
        msg = "修改成功";
      } else {
        let res = await addArticle(temp);
        this.articleList.data.unshift(res);
        this.articleDialogVisible = false;
        msg = "创建成功";
      }

      this.$notify({
        type: "success",
        title: msg
      });
    },
    async addImage(data) {
      return await addImage(data);
    },
    // 绑定@imgAdd event
    $imgAdd(pos, $file) {
      // 第一步.将图片上传到服务器.
      var formdata = new FormData();
      formdata.append("image", $file);
      this.addImage(formdata).then(res => {
        console.log(res);
        if (res.url) {
          this.$refs.md.$img2Url(pos, res.url);
        } else {
          this.$message({
            type: "error",
            message: "上传图片失败"
          });
        }
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