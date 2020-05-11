<template>
  <main style="padding: 0px 15px;margin-bottom:3rem;">
    <div class="container margin-top-lg">
      <div class="card article">
        <div class="card-content">
          <!-- Header avatar -->
          <div class="media">
            <div class="media-center">
              <img src="/avatar.jpg" class="author-image" />
            </div>
          </div>

          <!-- Nuxt Content -->
          <nuxt />

          <!-- Footer -->
          <footer class="footer margin-top-lg">
            <div class="has-text-centered">
              <p>
                Copyright © 2020
                <a href="#">
                  <strong>KenBucket</strong>
                </a>
                <br />
                <a
                  href="http://beian.miit.gov.cn"
                  target="_blank"
                  class="has-text-black"
                >粤ICP备18148020号</a>
              </p>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  middleware: "global",
  components: {},
};
</script>

