export const state = () => ({
    admin: null,
  })
  
  export const mutations = {
    setAdmin(state, admin) {
      state.admin = admin
    }
  }
  
  export const actions = {
    // 登录成功
    loginSuccess(context, result) {
      this.$cookies.set('adminToken', result.token, { maxAge: 86400 * 7, path: '/' })
      // sessionStorage.setItem('admin', JSON.stringify(result))
      context.commit('setAdmin', result)
      
      this.$toast.success('登录成功', {
        duration: 500,
        onComplete() {
          window.$nuxt.$router.push('/')
        }
      })
    },
  
    // 获取当前登录用户
    async getCurrentAdmin(context) {
      
      const adminToken = this.$cookies.get('adminToken')
      if (!adminToken) {
        return null
      }
      
      // if (sessionStorage.getItem('admin')) {
      //   const admin = JSON.parse(sessionStorage.getItem('admin'))
      //   context.commit('setAdmin', admin)
      //   return admin
      // }

      const admin = await this.$axios.get('/api/f1/admin')
      if (!admin) {
        context.commit('setAdmin', null)
        return null
      }
      
      context.commit('setAdmin', admin)
      return admin
    },
    
    // 登录
    async signin(context, { username, password, action }) {
      const result = await this.$axios.post('/api/f1/admin', {
        username,
        password,
        action
      })
      context.dispatch('loginSuccess', result)
      return result
    },

    // 退出登录
    async signout(context) {
      context.commit('setAdmin', null)
      this.$cookies.remove('adminToken')
    }
  }
  