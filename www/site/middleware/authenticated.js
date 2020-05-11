export default function(context) {
  const user = context.store.state.user.admin
  if (!user) {
    // const signInUrl = getSignInUrl(context)
    context.redirect('/404')
  }
}

// 获取登录跳转地址
// function getSignInUrl(context) {
//   let ref // 来源地址
//   if (process.server) {
//     // 服务端
//     ref = context.req.originalUrl
//   } else if (process.client) {
//     // 客户端
//     ref = context.route.path
//   }
//   let signinUrl = '/user/login'
//   if (ref) {
//     signinUrl += '?ref=' + encodeURIComponent(ref)
//   }
//   return signinUrl
// }
