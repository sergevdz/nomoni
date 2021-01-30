import { Dialog } from 'quasar'

export default ({ app, Vue }) => {
  // TODO - Write docs

  Vue.prototype.$showMessage = (title, message, persistent = false) => {
    return Dialog.create({
      title: title,
      message: message,
      persistent: persistent
    })
  }

  function padZeros(n, width, z) {
    z = z || '0'
    n = n + ''
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n
  }

  Vue.prototype.$today = () => {
    const newDate = new Date();
    const year = newDate.getFullYear()
    const month = padZeros(newDate.getMonth() + 1, 2)
    const day = padZeros(newDate.getDate(), 2)
    const fullDate = year + '-' + month + '-' + day;
    return fullDate
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
    return padZeros(n, width, z)
  }
}
