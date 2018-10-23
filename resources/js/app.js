require('./bootstrap');

window.Vue = require('vue');

Vue.component('sub-idx-languages',  require('./components/SubIdxLanguages.vue'));
Vue.component('file-group-jobs',    require('./components/FileGroupJobs.vue'));
Vue.component('sup-job',            require('./components/SupJob.vue'));
Vue.component('file-group-archive', require('./components/FileGroupArchive.vue'));
Vue.component('download-link',      require('./components/helpers/DownloadLink.vue'));

const app = new Vue({
    el: '#app'
});