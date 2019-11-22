import Pass from '../components/password.vue';
import Uyeler from '../components/uyeler.vue';
import Gider from '../components/gider.vue';
import Tahsilat from '../components/Tahsilat.vue';
import UyeKayit from '../components/UyeKayit.vue';
import Seviye from '../components/Seviye.vue';
import UyeYoklama from '../components/UyeYoklama.vue';
import MaliDurum from '../components/MaliDurum.vue';
import Yoklamalar from '../components/yoklamalar.vue';
import Sabitler from '../components/Sabitler.vue';
import Kullanicilar from '../components/Kullanicilar.vue';
import Photo from '../components/Photo.vue';

export default [
    {path:"/", component:Uyeler, meta:{ title:"Üyeler" }  },
    {path:"/pass", component:Pass, meta:{ title:"Parola Değiştir" } },
    {path:"/gider", component:Gider, meta:{ title:"Gider" } },
    {path:"/tahsilat/:uye_id/:uye", component:Tahsilat, meta:{ title:"Tahsilat" },props: true },
    {path:"/uyekayit/:uye_id", component:UyeKayit, meta:{ title:"Üye Kayıt" },props:true },
    {path:"/uyeseviye/:uye_id/:uye", component:Seviye, meta:{ title:"Üye Seviye" },props:true },
    {path:"/uyeyoklama/:uye_id/:uye", component:UyeYoklama, meta:{ title:"Üye Yoklama" },props:true },
    {path:"/photo/:uye_id/:photo/:uye", component:Photo, meta:{ title:"Üye Fotografı" },props:true },
    {path:"/malidurum", component:MaliDurum, meta:{ title:"Mali Durum" } },
    {path:"/yoklamalar", component:Yoklamalar, meta:{ title:"Yoklamalar" } },
    {path:"/sabitler", component:Sabitler, meta:{ title:"Sabitler" } },
    {path:"/kullanicilar", component:Kullanicilar, meta:{ title:"Kullanıcılar" } }
];