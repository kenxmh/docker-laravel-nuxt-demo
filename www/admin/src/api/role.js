import request from '@/utils/request'

export function getRoles() {
  return request({
    url: 'b1/roles',
    method: 'get'
  })
}

export function addRole(data) {
  return request({
    url: 'b1/roles',
    method: 'post',
    data
  })
}

export function updateRole(data) {
  return request({
    url: `b1/roles/${data.id}`,
    method: 'put',
    data
  })
}

export function deleteRole(id) {
  return request({
    url: `b1/roles/${id}`,
    method: 'delete'
  })
}

export function getRoleAccesses(id) {
  return request({
    url: `b1/roles/${id}/accesses`,
    method: 'get'
  })
}