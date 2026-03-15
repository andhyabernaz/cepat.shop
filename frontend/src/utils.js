import { Notify, copyToClipboard } from 'quasar';

function requiredRules(val) {
   if (val.length > 0) {
      return true
   }
   return 'Bidang tidak boleh kosong'
}

function validPhoneRules(val) {
   if (!val.startsWith('08')) {
      return 'No telp harus berawalan 08xxx'
   }
   if (val.length < 9) {
      return 'No telp terlalu pendek'
   }
   return true
}

function getRatio(ratio) {
   if (ratio.includes('/')) {
      const [numerator, denominator] = ratio.split('/')
      return Number(numerator) / Number(denominator)
   } else {
      return ratio
   }
}

function getColorBadge(str) {

   if (!str) return 'grey-8'

   str = str.toLowerCase()

   if (str == 'payment_submitted') return 'blue'
   if (str == 'success' || str == 'complete' || str == 'completed' || str == 'credit') return 'green'
   if (str == 'failed' || str == 'debit' || str == 'debet' || str == 'expired' || str == 'canceled' || str == 'cancelled') return 'red'
   if (str == 'shipping' || str == 'paid') return 'teal'
   if (str == 'process') return 'blue'

   return 'grey-8'
}
function formatDateFromTimestamp(timestamp) {
   if (!timestamp) return ''
   var date = new Date(timestamp * 1000);
   // return date.toLocaleString('id-ID');
   return new Intl.DateTimeFormat('id-ID', { dateStyle: 'full', timeStyle: 'short' }).format(date);

}

function moneyIdr(numb) {
   if (!numb) return 'Rp 0'
   return 'Rp ' + numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function moneyFormat(numb) {
   if (!numb) return 0;
   return numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function numberFormat(numb) {
   if (!numb) return 0;
   return numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function getOrderStatusColor(status) {
   if (['PAYMENT_SUBMITTED'].includes(status)) {
      return 'blue'
   }
   if (['AWAITING_PICKUP', 'SHIPPING'].includes(status)) {
      return 'teal'
   }
   if (['COMPLETE', 'PAID', 'Sent'].includes(status)) {
      return 'green'
   }
   if (['CANCELED', 'EXPIRED', 'Failed'].includes(status)) {
      return 'red'
   }
   if (['TOSHIP', 'TO_PROCESS', 'PROCESS'].includes(status)) {
      return 'amber-8'
   }
   return 'grey-7'
}

function sortByKey(data, key = 'price') {
   let sortedData = data.sort(function (a, b) {

      let x = parseInt(a[key]);
      let y = parseInt(b[key]);

      if (x > y) { return 1; }
      if (x < y) { return -1; }
      return 0;
   });

   return sortedData;
}

function generateSku(numb = 32) {
   let result = ''
   var randomChars = 'ABCDEFGHIJKL9MNOPQRST8UVWXYZ01T2343567890';

   for (var i = 0; i < numb; i++) {
      result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
   }

   return result;
}

function replaceString(str) {
   return str.replace(/[\W_]+/g, "");
}

function jumpTo(id) {
   let element = document.getElementById(id)
   if (!element) return
   var headerOffset = 55;
   var elementPosition = element.getBoundingClientRect().top;
   var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

   setTimeout(() => {
      window.scrollTo({
         top: offsetPosition,
         behavior: "smooth"
      });
   }, 50)
}

function formatPhoneNumber(number) {
   let formatted = number.replace(/\D/g, '')

   if (formatted.startsWith('0')) {
      formatted = '62' + formatted.substr(1)
   }

   return formatted;
}

function getRandomString(numb = 28) {
   let result = ''
   var randomChars = 'ABCDEFGHIJKL9MNOPQRST8UVWXYZ01T2343567890abcdefghijklmnopqrstuvwxyz';

   for (var i = 0; i < numb; i++) {
      result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
   }
   return result;
}

function getStatusIcon(status) {
   let icon = ''
   if (status == 'TOSHIP') {
      return 'move_to_inbox'
   }
   if (status == 'PENDING') {
      return 'payments'
   }
   if (status == 'SHIPPING') {
      return 'local_shipping'
   }
   if (status == 'CAOMPLETE') {
      return 'receipt_long'
   }
   if (status == 'CANCELLED') {
      return 'production_quantity_limits'
   }
   return icon
}

function copyString(str) {
   copyToClipboard(str)
      .then(() => {
         Notify.create({
            type: 'positive',
            message: 'Berhasil menyalin'
         })
      })
      .catch(() => {
         Notify.create({
            type: 'negative',
            message: 'Browser anda tidak support copy to clipboard'
         })
      })
}

export const dateFormat = (date, opts = {}) => {
   if (!date) return ''
   let d = new Date(date);

   let options = {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      ...opts
   };

   return new Intl.DateTimeFormat('id', options).format(d);
}
function dateParse(date, opts = {}) {
   if (!date) return ''
   let d = new Date(date);

   let options = {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      ...opts
   };

   return new Intl.DateTimeFormat('id', options).format(d);
}

export {
   copyString,
   formatDateFromTimestamp,
   moneyIdr,
   moneyFormat,
   getOrderStatusColor,
   sortByKey,
   generateSku,
   replaceString,
   jumpTo,
   formatPhoneNumber,
   getRandomString,
   getStatusIcon,
   validPhoneRules,
   requiredRules,
   numberFormat,
   getRatio,
   getColorBadge,
   dateParse
}