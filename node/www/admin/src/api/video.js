import request from '@/utils/request'

export function getWaitingVideos() {
  return request({
    url: 'b1/videos',
    method: 'get'
  })
}