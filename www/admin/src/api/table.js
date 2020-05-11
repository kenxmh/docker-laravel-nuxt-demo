import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/b1/articles',
    method: 'get',
    params
  })
}
