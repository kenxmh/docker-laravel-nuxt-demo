<template>
  <main>
    <div class="container public">
      <section
        class="hero"
        style="background-image: url(/hero3.jpg);min-height:300px;background-size: cover;"
      >
        <div class="hero-body">
          <p class="is-size-3 has-text-white has-text-centered" style="margin-top:60px">欢迎来到 KenBucket 博客</p>
          <p class="is-size-5 has-text-white has-text-centered" style="margin-top:30px">主要内容是如何快速开始一个新技术
以及学习笔记，踩坑记录等</p>
        </div>
      </section>
    </div>
    <div class="container public">
      <div class="container-sm">
        <div class="has-text-centered">
          <div class="buttons is-block" style="padding:15px;width: auto;overflow-x: auto;overflow-y: hidden;white-space: nowrap">
            <b-button rounded
              type="is-white is-outlined"
              :class="category == 'all' || category == '' ? 'is-active' : ''"
              @click="toOtherCategory('all')"
            >全部</b-button>
            <template v-for="(item, index) in categoryList">
              <b-button rounded   
                type="is-white is-outlined"
                :style="category == item.key ? 'color:#FFF;background-color:#' + item.color : ''"
                @click="toOtherCategory(item.key)"
              >{{ item.name }} ({{item.count}})</b-button>
            </template>
          </div>
        </div>
        <div class="columns is-multiline is-gapless">
          <template v-for="(article, index) in this.articleList.data">
            <div class="column is-full article" @click="goArticle(article.uuid)">
              <div class="columns is-gapless" >
                <div class="column is-one-third">
                  <figure class="image">
                    <img src="/article.jpg" />
                  </figure>
                </div>
                <div class="column">
                  <div v-if="!isLoading" class style="padding: 0.4rem;" >
                    <div class="content">
                      <p
                        class="title is-spaced is-size-5 has-text-weight-bold has-text-white"
                      >{{ article.title }}</p>

                      <p
                        class="subtitle is-size-6 has-text-grey-light"
                      >{{ article.abstract }}<p
                        class="is-size-7 has-text-grey-light"
                      >发布于 {{ article.created_at | prettyTopicTime }}</p>
                      <p class="is-size-7 has-text-grey-light">
                        标签&nbsp;&nbsp;
                        <template v-for="(item, index) in article.categories">
                          <span
                            :key="index"
                            class="tag"
                            :style="'color:#FFF;background-color:#' + item.color"
                          >{{ item.name }}</span>
                        </template>
                        <span class="has-text-right" style="float:right;line-height: 24px;">Read More -></span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </main>
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
$primary: #7d7d7d;

.is-active {
  background-color: $primary !important;
  color: #ffffff !important;
  font-weight: 600;
}

// .white-block {
//   background-color: #ffffff;
//   border-radius: 3px;
//   padding: 15px;
// }

.public {
  background: #242424 url(/wave1.svg);
  background-repeat: repeat-y;
  background-position: 50%;
  -webkit-box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.2);
  box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.2);
}

.article {
  margin-top: 40px !important;
  border-radius: 0;
  // display: -webkit-box;
  // display: -ms-flexbox;
  // display: flex;
  // min-height: 200px;
  -webkit-transition: border-right 0.2s ease;
  transition: border-right 0.2s ease;
  border: 1px solid #4a4a4a;
}
p.title {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  word-wrap: break-word;
}
p.subtitle {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box; //将对象作为弹性伸缩盒子模型显示。
  -webkit-box-orient: vertical; //从上到下垂直排列子元素（设置伸缩盒子的子元素排列方式）
  -webkit-line-clamp: 2; //这个属性不是css的规范属性，需要组合上面两个属性，表示显示的行数。此处为2行
  word-wrap: break-word; //允许单词内断句，首先会尝试挪到下一行，看看下一行的宽度够不够，不够的话就进行单词内的断句
}

.menu {
  font-size: 14px;

  svg {
    min-width: 20px !important;
  }
}

// .media-content {
//   transition: background 0.5s;

//   &:hover {
//     cursor: pointer;
//     background: #f3f6f9;
//     border-bottom: none;
//   }
// }

// .media {
//   &:not(:first-child) {
//     border-top: 1px dashed rgba(219, 219, 219, 0.5);
//     margin-top: 0.5rem;
//     padding-top: 0.5rem;
//   }
// }

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
