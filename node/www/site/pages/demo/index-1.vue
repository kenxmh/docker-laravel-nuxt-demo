<template>
  <div>
    <div class="has-background-primary has-text-white has-text-centered" style="padding: 30px 0;">
      <div class="container">
        <!-- <button class="delete" ></button> -->
        <p class="title is-5 is-spaced has-text-white">欢迎来到 KenBucket - 博客</p>
        <p class="subtitle is-6 has-text-light">
          主要内容是如何快速开始一个新技术
          <br />以及学习笔记，踩坑记录等
        </p>
      </div>
    </div>
    <main style="padding: 0px 15px;">
      <div class="container margin-top-lg">
        <div class="columns">
          <div class="column">
            <div class="white-block">
              <p class="menu-label" style="margin-bottom: 0.25rem;">文章</p>
              <div v-if="this.articleList">
                <template v-for="(article, index) in this.articleList.data">
                  <content-loader width="602" height="89" v-if="isLoading">
                    <rect x="0" y="10" rx="3" ry="3" width="500" height="27" />
                    <rect x="0" y="42" rx="3" ry="3" width="220" height="18" />
                    <rect x="0" y="65" rx="3" ry="3" width="260" height="24" />
                  </content-loader>
                  <article class="media" :key="index" @click="goArticle(article.uuid)">
                    <div v-if="!isLoading" class="media-content" style="padding: 0.5rem;">
                      <div class="content">
                        <div class="is-size-6 has-text-weight-bold">{{ article.title }}</div>
                        <div class="is-size-7 has-text-grey-light margin-top-xs">
                          发布于 {{ article.created_at | prettyTopicTime }}
                          &nbsp;｜&nbsp;
                          阅读量 {{ article.views }}
                        </div>
                        <div class="is-size-7 has-text-grey-light margin-top-xs">
                          标签&nbsp;&nbsp;
                          <template v-for="(item, index) in article.categories">
                            <span
                              :key="index"
                              class="tag"
                              :style="'color:#FFF;background-color:#' + item.color"
                            >{{ item.name }}</span>
                          </template>
                        </div>
                      </div>
                    </div>
                  </article>
                </template>
              </div>

              <div v-else style="padding:15px 0;">暂无帖子</div>

              <nav
                class="pagination is-small"
                role="navigation"
                aria-label="pagination"
                style="margin-top:15px;padding:0.5rem;"
              >
                <ul class="pagination-list" v-if="articleList.meta!=undefined">
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
          </div>
          <div class="column is-one-third">
            <aside class="menu white-block">
              <p class="menu-label">标签</p>
              <div class="buttons are-small">
                <button class="button is-text"
                :style="category == 'all' || category == '' ? 'color:#FFF;background-color:#4a4a4a': ''"
                  @click="toOtherCategory('all')" >全部 ({{statList.total_article}})
                  </button>
                <template v-for="(item, index) in categoryList">
                <button class="button is-text"
                :style="category == item.key ? 'color:#FFF;background-color:#' + item.color : ''"
                  @click="toOtherCategory(item.key)" >{{ item.name }} ({{item.count}})
                  </button>
                </template>
              </div>
            </aside>

            <aside class="menu white-block margin-top-lg">
              <p class="menu-label">常用工具</p>
              <ul class="menu-list">
                <li>
                  <a href="https://tool.lu/" target="_blank">程序员的工具箱</a>
                </li>
              </ul>
              <p class="menu-label">设计/配色/图片</p>
              <ul class="menu-list">
                <li>
                  <a href="http://hao.shejidaren.com/" target="_blank">设计导航</a>
                </li>
                <li>
                  <a href="https://hao.uisdc.com/" target="_blank">优设导航</a>
                </li>
              </ul>
              <p class="menu-label">趣站推荐</p>
              <ul class="menu-list">
                <li>
                  <a href="https://tophub.today/" target="_blank">今日热榜</a>
                </li>
                <li>
                  <a href="https://www.v2ex.com/" target="_blank">V2ex</a>
                </li>
              </ul>
            </aside>
          </div>
        </div>
      </div>
    </main>
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
      var queryString = "";
      for (let row in this.$route.query) {
        if (row !== key) queryString += `&${row}=${this.query[row]}`;
      }
      this.$router.replace(`?${queryString}&${key}=${value}`);
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

<style lang="scss" scoped>
$primary: #8892BF;

.is-active {
  background-color: $primary !important;
  color: #ffffff !important;
  font-weight: 600;
}

.white-block {
  background-color: #ffffff;
  border-radius: 3px;
  padding: 15px;
  -webkit-box-shadow: 0 0 15px 0 rgba(114, 114, 114, 0.2);
  box-shadow: 0 0 15px 0 rgba(114, 114, 114, 0.2);
}

.menu {
  font-size: 14px;

  svg {
    min-width: 20px !important;
  }
}

.media-content {
  transition: background 0.5s;

  &:hover {
    cursor: pointer;
    background: #f3f6f9;
    border-bottom: none;
  }
}

.media {
  &:not(:first-child) {
    border-top: 1px dashed rgba(219, 219, 219, 0.5);
    margin-top: 0.5rem;
    padding-top: 0.5rem;
  }
}

.tag {
  &:not(:first-child) {
    margin-left: 5px;
  }
}

.pagination-link.is-current {
  background-color: $primary;
  border-color: $primary;
  color: #fff;
}

.menu-label {
  font-size: 11px;
}
</style>
