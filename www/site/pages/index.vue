<template>
  <div style="margin-top:2.5rem">
    <!-- Category Menu -->
    <div class="buttons are-small" style="justify-content: center;">
      <button
        class="button is-text"
        :style="category == 'all' || category == '' ? 'color:#FFF;background-color:#4a4a4a': ''"
        @click="toOtherCategory('all')"
      >全部 ({{statList.total_article}})</button>
      <template v-for="(item, index) in categoryList">
        <button
          class="button is-text"
          :style="category == item.key ? 'color:#FFF;background-color:#' + item.color : ''"
          @click="toOtherCategory(item.key)"
        >{{ item.name }} ({{item.count}})</button>
      </template>
    </div>

    <!-- Article List -->
    <div v-if="this.articleList">
      <template v-for="(article, index) in this.articleList.data">
        <div v-if="isLoading" class="media-content has-text-centered">
          <b-skeleton width="60%" height="36px" :animated="true"></b-skeleton>
          <p style="margin-top:10px"></p>
          <b-skeleton width="30%" height="24px" :animated="true"></b-skeleton>
          <p style="margin-top:20px"></p>
          <b-skeleton width="25%" height="24px" :animated="true"></b-skeleton>
        </div>
        <div
          v-if="!isLoading"
          class="media-content has-text-centered"
          @click="goArticle(article.uuid)"
        >
          <p class="title article-title is-size-5-mobile is-size-4-tablet">{{ article.title }}</p>
          <p class="subtitle is-size-7-mobile is-size-6-tablet article-subtitle">
            {{ article.created_at | prettyTopicTime }} &nbsp;｜&nbsp;
            阅读量 {{ article.views }}
          </p>
          <div class="media-tags is-size-7 has-text-grey-light">
            <template v-for="(item, index) in article.categories">
              <span
                :key="index"
                class="tag"
                :style="'color:#FFF;background-color:#' + item.color"
              >{{ item.name }}</span>
            </template>
          </div>
        </div>
      </template>
    </div>
    <div v-else style="padding:15px 0;">暂无帖子</div>

    <!-- Pagination -->
    <nav
      class="pagination is-small"
      role="navigation"
      aria-label="pagination"
      style="margin-top:15px;padding:0.5rem;"
    >
      <ul
        class="pagination-list"
        v-if="articleList.meta!=undefined"
        style="justify-content: center;"
      >
        <template v-for="n in articleList.meta.last_page">
          <li :key="n">
            <a
              @click="toOtherPage(n)"
              :class="[articleList.meta.current_page == n ? 'is-current' : '', 'pagination-link']"
              aria-current="page"
            >{{ n }}</a>
          </li>
        </template>
      </ul>
    </nav>
  </div>
</template>

<script>
import { ContentLoader } from "vue-content-loader";

export default {
  layout: "blog",
  components: {
    "content-loader": ContentLoader
  },
  data() {
    return {
      query: {
        page: this.$route.query.page || 1,
        category: this.$route.query.category || ""
      },
      category: "all",
      articleList: [],
      categoryList: [],
      isLoading: false
    };
  },
  computed: {
    isLogin() {
      return this.$store.state.user.admin != null;
    }
  },
  mounted() {},
  async asyncData(context) {
    try {
      const category = context.query.category || "all";
      const page = context.query.page || 1;
      const [articleList, categoryList, statList] = await Promise.all([
        context.app.$axios.get(`/api/f1/articles`, {
          params: {
            page: page,
            category: category
          }
        }),
        context.app.$axios.get(`/api/f1/categories`),
        context.app.$axios.get(`/api/f1/stats`)
      ]);

      return {
        articleList: articleList || [],
        categoryList: categoryList || [],
        statList: statList || [],
        category: category,
        page: page
      };
    } catch (err) {
      // console.log(err)
    }
  },
  methods: {
    async goArticle(uuid) {
      this.$router.push({ path: `/article/${uuid}` });
    },
    toOtherPage(page) {
      this.isLoading = true;
      this.changeQuery("page", page);
      this.reloadArticleList();
    },
    toOtherCategory(category) {
      if (this.category != category) {
        this.isLoading = true;
        this.changeQuery("category", category);
        this.changeQuery("page", 1);
        this.category = category;
        this.reloadArticleList();
      }
    },
    changeQuery(key, value) {
      this.query[key] = value;
      // var queryString = "";
      // for (let row in this.$route.query) {
      //   if (row !== key) queryString += `&${row}=${this.query[row]}`;
      // }
      // this.$router.replace(`?${queryString}&${key}=${value}`);
    },
    async reloadArticleList() {
      try {
        this.articleList = await this.$axios.get(`/api/f1/articles`, {
          params: this.query
        });
        this.isLoading = false;
        // this.articleList = article_result.articleList;
        // this.page_total = article_result.page_total
      } catch (err) {
        console.log(err);
      }
    }
  },
  head() {
    return {
      title: "KenBucket - 首页",
      meta: [
        {
          hid: "keywords",
          name: "keywords",
          content:
            "Kenbucket,博客,技术博客,IT博客,服务器,数据库,PHP,Docker,Vue,SEO"
        },
        {
          hid: "description",
          name: "description",
          content:
            "Kenbucket 是一个关注前后端开发的技术博客，包含服务器、数据库、PHP、Docker、Vue、SEO等技术信息博客，并提供博主在学习成果和工作中经验总结。"
        }
      ]
    };
  }
};
</script>


