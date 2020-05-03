<template>
  <div class="container">
    <div class="text-center" style="padding:30px;font-size:48px;">
        <div v-if="error.statusCode === 404"> 
            <img class="text-center margin-top-md" src="/404.png" style="height:128px;">
            <p class="text-lg margin-top-md" > 找不到页面</p>
        </div>
        <div v-else>
            <img class="text-center margin-top-md" src="/error.png" style="height:128px;">
            <p class="text-lg margin-top-md" > 页面不正确 </p>
        </div>

        <a class="button is-primary margin-top-lg" href="/">返回首页</a>
    </div>
  </div>
</template>

<script>
export default {
  props: ['error'],
//   layout: 'blog' // 你可以为错误页面指定自定义的布局
}
</script>