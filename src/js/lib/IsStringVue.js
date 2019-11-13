import IsString from "IsString.js";
export default {
    install(Vue,name ="$is") {
        Object.defineProperty(Vue.prototype,name,{ value:IsString })
    }
}