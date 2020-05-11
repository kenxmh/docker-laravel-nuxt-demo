import request from '@/utils/request'

export function getStats() {
  return request({
    url: 'b1/stats',
    method: 'get'
  })
}