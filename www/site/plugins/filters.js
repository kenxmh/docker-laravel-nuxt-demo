import Vue from 'vue'

const filters = {
  formatDate(timestamp, fmt) {
    fmt = fmt || 'yyyy-MM-dd HH:mm:ss'
    const date = new Date(timestamp.replace(/-/g,"/"))
    const o = {
      'M+': date.getMonth() + 1,
      'd+': date.getDate(),
      'h+': date.getHours() % 12,
      'H+': date.getHours(),
      'm+': date.getMinutes(),
      's+': date.getSeconds(),
      'q+': Math.floor((date.getMonth() + 3) / 3),
      S: date.getMilliseconds()
    }
    if (/(y+)/.test(fmt)) {
      fmt = fmt.replace(
        RegExp.$1,
        (date.getFullYear() + '').substr(4 - RegExp.$1.length)
      )
    }
    for (const k in o) {
      if (new RegExp('(' + k + ')').test(fmt)) {
        fmt = fmt.replace(
          RegExp.$1,
          RegExp.$1.length === 1
            ? o[k]
            : ('00' + o[k]).substr(('' + o[k]).length)
        )
      }
    }
    return fmt
  },

  prettyDate(timestamp, type) {
    const minute = 1000 * 60
    const hour = minute * 60
    const day = hour * 24
    
    const objts= new Date(timestamp.replace(/-/g,"/")).getTime();
    const nowts= new Date().getTime();
    const diffValue = nowts - objts
    
    if (diffValue / minute < 1) {
      return '刚刚'
    } else if (diffValue / minute < 60) {
      return parseInt(diffValue / minute) + '分钟前'
    }  else if (diffValue / hour <= 24) {
      return parseInt(diffValue / hour) + '小时前'
    } else if (diffValue / hour <= 48) {
      return '昨天 ' + filters.formatDate(timestamp, 'HH:mm')
    } else {
      if (type == 1) {
        return filters.formatDate(timestamp, 'M月dd日 · yyyy年')
      } else {
        return filters.formatDate(timestamp, 'MM-dd HH:mm')
      }
    }
  },

  prettyTopicTime(timestamp) {
    return filters.prettyDate(timestamp, 1);
  },

  prettyCommentTime(timestamp) {
    return filters.prettyDate(timestamp, 2);
  }
}

Object.keys(filters).forEach(function(key) {
  Vue.filter(key, filters[key])
})
