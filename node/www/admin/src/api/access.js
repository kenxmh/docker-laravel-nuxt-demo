import request from '@/utils/request'

export function getAccesses() {
  return request({
    url: 'b1/accesses',
    method: 'get'
  })
}

export function addAccess(data) {
  return request({
    url: 'b1/accesses',
    method: 'post',
    data
  })
}

export function updateAccess(data) {
  return request({
    url: `b1/accesses/${data.id}`,
    method: 'put',
    data
  })
}

export function deleteAccess(id) {
  return request({
    url: `b1/accesses/${id}`,
    method: 'delete'
  })
}