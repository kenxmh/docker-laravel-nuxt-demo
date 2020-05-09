import request from '@/utils/request'

// article
export function getArticles(query) {
  return request({
    url: 'b1/articles',
    method: 'get',
    params: query
  })
}

export function addArticle(data) {
  return request({
    url: 'b1/articles',
    method: 'post',
    data
  })
}

export function updateArticle(data) {
  return request({
    url: `b1/articles/${data.id}`,
    method: 'put',
    data
  })
}

export function deleteArticle(id) {
  return request({
    url: `b1/articles/${id}`,
    method: 'delete'
  })
}

// category
export function getCategories() {
  return request({
    url: 'b1/categories',
    method: 'get'
  })
}

export function addCategory(data) {
  return request({
    url: 'b1/categories',
    method: 'post',
    data
  })
}

export function updateCategory(data) {
  return request({
    url: `b1/categories/${data.id}`,
    method: 'put',
    data
  })
}

export function deleteCategory(id) {
  return request({
    url: `b1/categories/${id}`,
    method: 'delete'
  })
}

// images

export function addImage(data) {
  return request({
    url: `b1/images`,
    method: 'post',
    headers: {
      'Content-Type': 'multipart/form-data'
    },
    data
  })
}

// comment
export function getComments(query) {
  return request({
    url: 'b1/comments',
    method: 'get',
    params: query
  })
}

export function addComment(data) {
  return request({
    url: `b1/comments`,
    method: 'post',
    data
  })
}

export function deleteComment(id) {
  return request({
    url: `b1/comments/${id}`,
    method: 'delete'
  })
}