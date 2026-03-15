import { boot } from 'quasar/wrappers'

import {
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
    dateFormat,
    requiredRules,
    validPhoneRules,
    numberFormat,
    getRatio,
    getColorBadge,
    dateParse
} from 'src/utils';
import { computed } from 'vue';

// "async" is optional;
// more info on params: https://v2.quasar.dev/quasar-cli/boot-files
export default boot(async ({ app, router, store }) => {

    function guard (ability) {
        if (!ability) return false
        if(ability == 'all') return true
        const permissions = computed(() => store.state.user.permissions)
        
        if (permissions.value.includes('can-all')) {
            return true;
        }
        if (!permissions.value.length) {
            return false
        }
        return permissions.value.includes(ability)

    }
    function canAccess (ability) {
        if(!guard(ability)) {
            router.back()
        }
    }

    app.config.globalProperties.getColorBadge = getColorBadge
    app.config.globalProperties.getRatio = getRatio
    app.config.globalProperties.moneyIdr = moneyIdr
    app.config.globalProperties.numberFormat = numberFormat
    app.config.globalProperties.moneyFormat = moneyFormat
    app.config.globalProperties.getOrderStatusColor = getOrderStatusColor
    app.config.globalProperties.sortByKey = sortByKey
    app.config.globalProperties.generateSku = generateSku
    app.config.globalProperties.replaceString = replaceString
    app.config.globalProperties.jumpTo = jumpTo
    app.config.globalProperties.formatPhoneNumber = formatPhoneNumber
    app.config.globalProperties.getRandomString = getRandomString
    app.config.globalProperties.getStatusIcon = getStatusIcon
    app.config.globalProperties.dateFormat = dateFormat
    app.config.globalProperties.$dateParse = dateParse
    app.config.globalProperties.formatDateFromTimestamp = formatDateFromTimestamp
    app.config.globalProperties.copyString = copyString
    app.config.globalProperties.requiredRules = requiredRules
    app.config.globalProperties.validPhoneRules = validPhoneRules
    app.config.globalProperties.$guard = guard
    app.config.globalProperties.$can = guard
    app.config.globalProperties.$canAccess = canAccess
})
