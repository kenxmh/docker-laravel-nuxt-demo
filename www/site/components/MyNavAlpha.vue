<template>
  <section class="hero is-medium">
    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="subtitle">Welcome to</h1>
        <h2 class="title">KenBucket</h2>
        <h1 class="subtitle profession">A FULLSTACK BLOG</h1>
      </div>
    </div>
    <div class="hero-foot">
      <div class="hero-foot--wrapper">
        <div class="hero-menu has-text-centered">
          <ul>
            <li>
              <a @click="toOtherCategory('all')">all</a>
            </li>
            <template v-for="(item, index) in categoryList">
              <li>
                <a @click="toOtherCategory(item.key)">{{ item.name }}</a>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>


<script>
export default {
  props: {
    categoryList: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  data() {
    return {
      query: {
        page: this.$route.query.page || 1,
        category: this.$route.query.category || ""
      },
      category: "all",
    };
  },
  methods: {
    toOtherCategory(category) {
      if (this.category != category) {
        this.$emit('toOtherCategory', category)
      }
    }
  }
};
</script>

<style scoped>
.hero {
  background-image: url(/banner1.png);
  /* background: linear-gradient(rgba(31, 44, 108, 0.65), rgba(31, 44, 108, 0.65)), rgba(0, 0, 0, 0.55) url(/banner.png) no-repeat; */
  /* background-attachment: fixed; */
  /* background-size: cover; */
  color: white;
  box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
  font-family: "Poppins", sans-serif;
}
.hero .title {
  font-family: "Kaushan Script", sans-serif;
  transform: rotate(-5deg);
  font-size: 4rem;
  color: whitesmoke;
  /* color: #a79fca; */
}

.hero .subtitle {
  padding: 5px;
  color: whitesmoke;
}

.hero .profession {
  padding-top: 50px;
  font-size: 1.7rem;
  /* text-transform: uppercase; */
}

.hero .hero-foot {
  height: 50px;
  background: white;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.082);
}

/* @media (max-width: 599px) {
  .hero .hero-foot {
    display: none;
  }
} */

.hero .hero-foot .hero-foot--wrapper {
  margin: 7px auto;
  height: 50px;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu {
  line-height: 38px;
  font-weight: 600;
  text-transform: lowercase;
  letter-spacing: 1px;
  font-size: 1rem;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu ul {
  overflow-x: auto;
  white-space: nowrap;
  width: auto;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu ul li {
  display: inline-block;
  padding-right: 15px;
  padding-left: 15px;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu ul li.is-active a {
  border-bottom: 2px solid #1f2c6c;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu ul li a {
  color: #1f2c6c;
}

.hero .hero-foot .hero-foot--wrapper .hero-menu ul li a:hover {
  color: #1f2c6c;
  transition: all 0.2s ease-in-out;
  border-bottom: 2px solid #1f2c6c;
}
</style>
