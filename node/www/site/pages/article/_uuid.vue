<template>
  <div>
    <nuxt-link to="/" style="position: absolute;top: 1.5rem;">返回</nuxt-link>
    <div class="has-text-centered" style="margin-top:3rem">
      <div class="is-size-7">
        <template v-for="(category, index) in article.categories">
          <span class="tag" :key="index">
            <a :style="{ color: '#' + category.color }">
              <fa :icon="['fas', 'tag']" />
              {{ category.name }}
            </a>
          </span>
        </template>
      </div>
      <div class="is-size-4 margin-top-md">{{ article.title }}</div>
    </div>
    <div style="margin-top:3rem">
      <!-- 评论 -->
      <div class="comment-component">
        <ul class="comments">
          <div
            v-infinite-scroll="loadMore"
            infinite-scroll-disabled="busy"
            infinite-scroll-distance="10"
            infinite-scroll-immediate-check="true"
          >
            <li class="comment">
              <div class="comment-meta">
                <span class="comment-nickname">Ken</span>
                <span class="tag">作者</span>
                <span class="comment-time">&nbsp;&nbsp;{{ article.created_at | prettyCommentTime }}</span>
                <span class="comment-floor"># 1</span>
              </div>
              <div class="comment-content content">
                <div v-html="article.body"></div>
              </div>
            </li>
            <li class="comment" v-for="(comment, index) in comments" v-bind:key="index">
              <div class="comment-meta" style="margin-top:0.5rem;">
                <span class="comment-nickname">{{ comment.is_author ? 'Ken' : comment.nickname }}</span>
                <span v-if="comment.is_author" class="tag">作者</span>
                <span class="comment-time">&nbsp;&nbsp;{{ comment.created_at | prettyCommentTime }}</span>

                <span class="comment-floor"># {{ index + 2 }}</span>
                <span class="comment-floor" style="margin-right:0.5rem">
                  <a @click="quoteReply(comment)">引用</a>
                </span>
              </div>
              <div class="comment-content content">
                <article class="message" v-if="comment.quote">
                  <div class="message-body">
                    <div class="comment-meta">
                      <span class="comment-nickname">
                        引用 @{{ comment.quote.is_author ? 'Ken' : comment.quote.nickname }}
                        <span
                          v-if="comment.quote.is_author"
                          class="tag is-dark"
                        >作者</span> 的评论
                      </span>
                    </div>
                    <p v-html="comment.quote.content"></p>
                  </div>
                </article>
                <p v-html="comment.content"></p>
              </div>
            </li>
          </div>
        </ul>

        <div class="comment-form" style="margin-top:30px;">
          <div class="field">
            <label class="label is-large">留言区</label>
            <div class="control has-icons-left has-icons-right">
              <input class="input" v-model="nickname" placeholder="昵称（仅用于显示）" />
              <span class="icon is-small is-left">
                <fa :icon="['fas', 'user']" />
              </span>
            </div>
          </div>

          <div class="field">
            <div class="control has-icons-left has-icons-right">
              <input class="input" type="email" v-model="email" placeholder="Email（选填，仅用于回复通知）" />
              <span class="icon is-small is-left">
                <fa :icon="['fas', 'envelope']" />
              </span>
            </div>
          </div>

          <div class="comment-create">
            <div class="comment-input-wrapper">
              <div v-if="quoteComment" class="comment-quote-info">
                <!-- 回复：
                <label >{{quoteComment.nickname}}</label>
                <fa :icon="['fas', 'times']" @click="quoteCancel"/>-->
                <article class="message">
                  <div class="message-body">
                    <div class="comment-meta">
                      <span class="comment-nickname">
                        引用 @{{ quoteComment.is_author ? 'Ken' : quoteComment.nickname }}
                        <span
                          v-if="quoteComment.is_author"
                          class="tag is-dark"
                        >作者</span> 的评论
                      </span>
                      <fa
                        :icon="['fas', 'times']"
                        @click="quoteCancel"
                        style="position:absolute;right:2rem"
                      />
                    </div>
                    <p v-html="quoteComment.content"></p>
                  </div>
                </article>
              </div>

              <textarea
                ref="commentEditor"
                v-model="content"
                @keydown.ctrl.enter="ctrlEnterCreate"
                @keydown.meta.enter="ctrlEnterCreate"
                @input="autoHeight"
                class="comment-input"
                placeholder="发表你的想法、疑问或建议"
              ></textarea>
              <div class="comment-button-wrapper">
                <button @click="create" v-text="btnName" class="button is-light" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// import utils from '~/common/utils'

export default {
  layout: "blog",
  components: {},
  data() {
    return {
      commentContent: "",
      page: 1, // 当前页码
      maxPage: 0, // 最大页码
      nickname: "",
      email: "",
      content: "", // 内容
      sending: false, // 发送中
      quote: null, // 引用的对象
      comments: [],
      articleUUID: this.$route.params.uuid,
      busy: false,
      quoteComment: null
    };
  },
  // validate({ params }) {
  //   return /^\d+$/.test(params.uuid) // must be number
  // },
  computed: {
    btnName() {
      return this.sending ? "正在评论..." : "评论";
    }
  },
  // mounted() {
  //   // utils.handleToc(this.$refs.toc)
  //   this.articleUUID = this.$route.params.uuid;
  // },
  async asyncData(context) {
    const articleUUID = context.params.uuid; // 从动态路由参数中获取帖子id
    const [articleResult, commentResult] = await Promise.all([
      context.app.$axios.get(`/api/f1/articles/${articleUUID}`),
      context.app.$axios.get(`/api/f1/articles/${articleUUID}/comments`, {
        params: {
          page: 1
        }
      })
    ]);
    return {
      article: articleResult.data,
      comments: commentResult.data
    };
  },
  methods: {
    quoteReply(quoteComment) {
      this.quoteComment = quoteComment;
    },
    quoteCancel() {
      this.quoteComment = null;
    },
    async create() {
      if (!this.content) {
        this.$toast.error("请输入评论内容");
        return;
      } else if (this.sending) {
        this.$toast.error("正在发送中，请不要重复提交...");
        return;
      }

      this.sending = true;
      try {
        const articleUUID = this.articleUUID;
        const newComment = {
          nickname: this.nickname,
          email: this.email,
          content: this.content,
          quote_uuid: this.quoteComment ? this.quoteComment.uuid : null
        };
        const res = await this.$axios.post(
          `/api/f1/articles/${articleUUID}/comments`,
          newComment
        );

        this.comments.push(res.data);
        this.content = "";
        this.quote = null;
      } catch (e) {
        this.$toast.error(
          "评论失败：" + (e.response.data.message || e.response.status)
        );
      } finally {
        this.sending = false;
      }
    },
    loadMore() {
      this.busy = true;
      setTimeout(() => {
        this.loadComment();
      }, 500);
    },
    async loadComment() {
      try {
        const commentResult = await this.$axios.get(
          `/api/f1/articles/${this.articleUUID}/comments`,
          {
            params: {
              page: this.page + 1
            }
          }
        );

        if (commentResult.data.length > 0) {
          this.comments = this.comments.concat(commentResult.data);
          this.page++;
          this.busy = false;
        } else {
          this.busy = true;
        }
      } catch (err) {
        // console.log(err);
        this.busy = false;
      }
    },
    autoHeight() {
      const elem = this.$refs.commentEditor;
      elem.style.height = "auto";
      elem.scrollTop = 0; // 防抖动
      elem.style.height = elem.scrollHeight + "px";
    }
  },
  head() {
    return {
      title: `${this.article.title} - KenBucket`,
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
.tag {
  &:not(:first-child) {
    margin-left: 5px;
  }
}

.content {
  padding-top: 10px;
  font-size: 15px;
  color: #000;
  white-space: normal;
  word-break: break-all;
  word-wrap: break-word;
  margin-bottom: 30px;
}

// Comment
.comment-component {
  margin: 0 6rem;
  .comment-form {
    .comment-create {
      border: 1px solid #dbdbdb;
      border-radius: 4px;
      margin-bottom: 10px;
      overflow: hidden;
      position: relative;
      padding: 10px;
      box-sizing: border-box;

      .comment-input {
        width: 100%;
        min-height: 8.75rem;
        font-size: 0.875rem;
        background: transparent;
        resize: vertical;
        -webkit-transition: all 0.25s ease;
        transition: all 0.25s ease;
        border: none;
        outline: none;
        padding: 10px 5px;
        max-width: 100%;
        margin-top: 0;
        margin-bottom: 0;
        overflow: hidden;
      }

      .comment-button-wrapper {
        .comment-help {
          float: left;
          margin-top: 5px;
        }

        button {
          float: right;
        }
      }
    }
  }

  .comments {
    .comment {
      // padding: 8px 0;
      overflow: hidden;

      &:not(:last-child) {
        border-bottom: 1px solid #e7edf3;
      }

      .comment-meta {
        position: relative;
        height: 36px;

        .comment-nickname {
          position: relative;
          font-size: 14px;
          line-height: 24px;
          font-weight: 800;
          margin-right: 5px;
          cursor: pointer;
          text-decoration: none;
          display: inline-block;
        }

        .comment-time {
          font-size: 12px;
          line-height: 24px;
          color: #999999;
          // line-height: 1;
          display: inline-block;
          position: relative;
        }

        .comment-floor {
          float: right;
          font-size: 12px;
          line-height: 24px;
        }
      }

      .comment-content {
        word-wrap: break-word;
        word-break: break-all;
        text-align: justify;
        color: #4a4a4a;
        // font-size: 14px;
        line-height: 1.6;
        position: relative;
        // padding-left: 10px;
        // margin-top: -5px;
      }
    }
  }
}

@media screen and (max-width: 768px) {
  .comment-component {
    margin: 0;
  }
}
</style>
