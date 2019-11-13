import Welcome from '../components/welcome.vue'
import Pass from '../components/password.vue'
export default [
    {path:"/", component:Welcome, meta:{ title:"Welcome" }  },
    {path:"/pass", component:Pass, meta:{ title:"Change Password" } }
];