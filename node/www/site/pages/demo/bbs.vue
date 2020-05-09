<template>
  <div>
    <main style="padding: 20px 15px;">
      <div class="container is-desktop">
        <div class="columns">
        <div class="tabs">
          <ul>
            <li class="is-active">
              <a>
                <span class="icon is-small">
                  <i class="fas fa-image" aria-hidden="true"></i>
                </span>
                <span>最新</span>
              </a>
            </li>
            <li>
              <a>
                <span class="icon is-small">
                  <i class="fas fa-music" aria-hidden="true"></i>
                </span>
                <span>热门</span>
              </a>
            </li>
            <li>
              <a>
                <span class="icon is-small">
                  <i class="fas fa-film" aria-hidden="true"></i>
                </span>
                <span>精华</span>
              </a>
            </li>
          </ul>
        </div>
        </div>
        <div class="columns">
          <div class="column">
            <div v-if="this.articleList">
              <template v-for="(article, index) in this.articleList.data">
                <content-loader width="602" height="89" v-if="isLoading">
                  <rect x="0" y="10" rx="3" ry="3" width="500" height="27" />
                  <rect x="0" y="42" rx="3" ry="3" width="220" height="18" />
                  <rect x="0" y="65" rx="3" ry="3" width="260" height="24" />
                </content-loader>
                <article
                  v-if="!isLoading"
                  class="media"
                  :key="index"
                  @click="goArticle(article.uuid)"
                >
                  <figure class="media-left">
                    <p class="image is-32x32">
                      <img src="https://bulma.io/images/placeholders/32x32.png" />
                    </p>
                  </figure>
                  <div class="media-content">
                    <div class="content">
                      <div class="is-size-6 has-text-weight-bold">{{ article.title }}</div>
                      <!-- <div class="is-size-7 has-text-grey-light margin-top-xs">
                          发布于 {{ article.created_at | prettyTopicTime }}
                          &nbsp;｜&nbsp;
                          阅读量 {{ article.views }}
                      </div>-->
                      <!-- <div class="text-md margin-top-xs">
                        摘要：
                      </div>-->

                      <!-- <div class="is-size-7 has-text-grey-light margin-top-xs">
                          标签&nbsp;&nbsp;
                          <template v-for="(item, index) in article.categories">
                            <span
                              :key="index"
                              class="tag"
                              :style="'color:#FFF;background-color:#' + item.color"
                            >{{ item.name }}</span>
                          </template>
                      </div>-->

                      <div class="is-size-7 margin-top-xs">
                        <div class="tags has-addons is-hidden-tablet">
                          <span class="tag is-primary">投篮</span>
                          <span class="tag">跳投</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="media-right is-hidden-mobile">
                    <div class="tags has-addons is-pulled-left">
                      <span class="tag is-primary">投篮</span>
                      <span class="tag">跳投</span>
                    </div>
                    <div class="is-pulled-left">
                      &nbsp;&nbsp;
                      <fa :icon="['fas', 'fire']" /> 101
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
      </div>
    </main>
  </div>
</template>

<script>
import { ContentLoader } from "vue-content-loader";

export default {
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
// .is-active {
//   background-color: #00d1b2 !important;
//   color: #ffffff !important;
//   font-weight: 600;
// }

.white-block {
  background-color: #ffffff;
  border-radius: 3px;
  padding: 15px;
}

.menu {
  font-size: 14px;

  svg {
    min-width: 20px !important;
  }
}

// .menu-list a {
//   color:#667d99;
// }

article {
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
  margin-bottom: 0;
}

.tags {
  margin-bottom: 0;
}

.pagination-link.is-current {
  background-color: #00d1b2;
  border-color: #00d1b2;
  color: #fff;
}

.menu-label {
  font-size: 11px;
}
</style>
