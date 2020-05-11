if (process.client && process.env.NODE_ENV === 'production' && process.env.BAIDU_ANALYTICS_KEY != '') {
  /*
  ** baidu 统计分析脚本
  */
 var _hmt = _hmt || [];
 (function() {
   var hm = document.createElement("script");
   hm.src = "https://hm.baidu.com/hm.js?" + process.env.BAIDU_ANALYTICS_KEY;
   hm.id = "baidu_tj";
   var s = document.getElementsByTagName("script")[0];
   s.parentNode.insertBefore(hm, s);
 })();
}

export default ({ app: { router }, store }) => {
  /*
  ** 每次路由变更时进行pv统计
  */
 if (process.client && process.env.NODE_ENV === 'production' && process.env.BAIDU_ANALYTICS_KEY != '') {
  router.afterEach((to, from) => {
    var _hmt = _hmt || [];
    (function() {
      document.getElementById('baidu_tj') && document.getElementById('baidu_tj').remove();
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?" + process.env.BAIDU_ANALYTICS_KEY;
      hm.id = "baidu_tj";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(hm, s);
    })();
  })
}
}