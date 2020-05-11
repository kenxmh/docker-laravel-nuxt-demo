<template>
  <div class="app-container">
    <el-table
      style="width: 100%;margin-top:30px;"
      v-loading="loading"
      border
      stripe
      row-key="id"
      :data="commentList.data"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
    >
      <el-table-column align="center" label="ID" prop="id" sortable>
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column align="center" label="是否游客" prop="nickname">
        <template slot-scope="scope">
          <el-tag type="info" effect="dark" v-if="scope.row.is_author">自己</el-tag>
          <el-tag type="" effect="dark" v-else>游客</el-tag>
          </template>
      </el-table-column>
      <el-table-column align="center" label="留名" prop="email">
        <template slot-scope="scope">{{ scope.row.is_author ? '-' : scope.row.nickname }}</template>
      </el-table-column>
      <el-table-column align="center" label="email" prop="email">
        <template slot-scope="scope">{{ scope.row.is_author ? '-' : scope.row.email }}</template>
      </el-table-column>
      <el-table-column align="center" label="ip" prop="ip" sortable>
        <template slot-scope="scope">{{ scope.row.ip }}</template>
      </el-table-column>
      <el-table-column align="center" label="content" prop="content">
        <template slot-scope="scope">{{ scope.row.content }}</template>
      </el-table-column>
      <el-table-column align="center" label="created_at" prop="created_at">
        <template slot-scope="scope">{{ scope.row.created_at }}</template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleReply(scope)">回复</el-button>
          <el-button type="danger" size="mini" @click="handleDelete(scope.row.id)">删除</el-button>
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
        :total="commentList.total"
      ></el-pagination>
    </div>

    <el-dialog :visible.sync="commentDialogVisible" title="回复评论">
      <el-form :model="comment" label-width="80px" label-position="left">
        <el-input
          type="textarea"
          :autosize="{ minRows: 2, maxRows: 4}"
          placeholder="请输入内容"
          v-model="comment.content"
        ></el-input>
      </el-form>
      <div style="text-align:right;margin-top:15px;">
        <el-button type="danger" @click="commentDialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmComment">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from "@/utils";
import { getComments, addComment } from "@/api/article";

const defaultComment = {
  article_id: "",
  content: "",
  quote_id: ""
};

export default {
  data() {
    return {
      comment: Object.assign({}, defaultComment),
      index: 0,
      routes: [],
      categoryList: [],
      commentDialogVisible: false,
      loading: true,
      commentList: [],
      content: "",
      query: {
        prop: "",
        order: "",
        page: 1,
        articleId: this.$route.query.articleId
      }
    };
  },
  watch: {
    query: {
      deep: true,
      handler: function(newObj, oldObj) {
        this.loading = true;
        getComments(this.query)
          .then(response => {
            this.commentList = response;
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
  created() {
    this.getComments(this.query);
  },
  methods: {
    async getComments() {
      const res = await getComments(this.query);
      this.commentList = res;
      this.loading = false;
    },
    handleCurrentChange(page) {
      this.query.page = page;
    },
    handleReply(scope) {
      this.comment.article_id = scope.row.article_id;
      this.comment.quote_id = scope.row.id;
      this.comment.content = "";
      this.commentDialogVisible = true;
      this.checkStrictly = true;
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
    async confirmComment() {
      let res = await addComment(this.comment);
      this.commentList.data.unshift(res);

      this.commentDialogVisible = false;

      this.$notify({
        type: "success",
        title: "回复成功"
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
