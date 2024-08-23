import {reactive } from 'vue'

export const store = reactive({
    storeTest: 'Store!',
    forceReload(){
        window.location.reload();
    },
})