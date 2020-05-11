import request from '@/utils/request'

export function getAdmins() {
  return request({
    url: 'b1/admins',
    method: 'get'
  })
}

export function addAdmin(data) {
  return request({
    url: 'b1/admins',
    method: 'post',
    data
  })
}

export function updateAdmin(data) {
  return request({
    url: `b1/admins/${data.id}`,
    method: 'put',
    data
  })
}

export function deleteAdmin(id) {
  return request({
    url: `b1/admins/${id}`,
    method: 'delete'
  })
}

export function resetAdminPwd(data) {
  return request({
    url: `b1/admins/${data.id}/password`,
    method: 'put',
    data
  })
}

export function resetAdminRole(data) {
  return request({
    url: `b1/admins/${data.id}/role`,
    method: 'put',
    data
  })
}