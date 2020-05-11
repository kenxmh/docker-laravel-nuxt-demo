// import qs from 'qs'

export default function ({ app }) {

  let axios = app.$axios; 
  
  axios.defaults.timeout = 10000
  axios.defaults.headers.post['Content-Type'] = 'application/json'

  axios.onRequest(config => {

    // 设置 token
    const token = app.$cookies.get('adminToken') // 从cookie中获取token
    if (token) { // 如果找到了token，那么将token放到请求头中
      config.headers.common['token'] = token
    }
  })

  axios.onResponse(response => {
    if (response.status == 200 || response.status == 201 || response.status == 204) {
      const jsonResult = response.data
      return Promise.resolve(jsonResult)
    } else {
      return Promise.reject(response)
    }
  })

  // $axios.onError(error => {
  //   const code = parseInt(error.response && error.response.status)

  //   if (code >= 500) {
  //     // redirect('/400')
  //     console.log()
  //   } else if ( code >= 400) {
      
  //   }
  // })
}
