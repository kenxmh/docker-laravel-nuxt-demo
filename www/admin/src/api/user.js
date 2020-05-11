import request from '@/utils/request'

export function login(data) {
  return request({
    url: 'b1/auth/login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: 'b1/auth/user',
    method: 'get'
  })
}

export function updateUser(data) {
  return request({
    url: 'b1/auth/user',
    method: 'put',
    data
  })
}

export function logout() {
  return request({
    url: 'b1/auth/logout',
    method: 'post'
  })
}
