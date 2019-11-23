import { Dialog } from 'quasar'

export default ({ app, Vue }) => {
  // TODO - Write docs

  Vue.prototype.$showMessage = (title, message) => {
    Dialog.create({
      title: title,
      message: message,
      persistent: true
    })
  }

  Vue.prototype.$formatNumber = (number, len = 2) => parseFloat((100 * number) / 100).toFixed(len).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')

  Vue.prototype.$lockIntegers = (evt) => {
    if (!evt) {
      evt = window.event
    }
    let charCode = (evt.which) ? evt.which : evt.keyCode
    if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
      evt.preventDefault()
    } else {
      return true
    }
  }

  Vue.prototype.$lockDecimals = (evt) => {
    if (!evt) {
      evt = window.event
    }
    let charCode = (evt.which) ? evt.which : evt.keyCode
    if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
      evt.preventDefault()
    } else {
      return true
    }
  }

  Vue.prototype.$padZeros = (n, width, z) => {
    z = z || '0'
    n = n + ''
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n
  }
}
