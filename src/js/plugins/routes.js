import Pass from '../components/password.vue';
import Uyeler from '../components/Uyeler.vue';
import GelirGider from '../components/GelirGider.vue';
import Tahsilat from '../components/Tahsilat.vue';
import UyeKayit from '../components/UyeKayit.vue';
import Seviye from '../components/Seviye.vue';
import UyeYoklama from '../components/UyeYoklama.vue';
import GenelDurum from '../components/GenelDurum.vue';
import Yoklamalar from '../components/Yoklamalar.vue';
import Yoklama from '../components/Yoklama.vue';
import UyeHarcamalari from '../components/UyeHarcamalari.vue';
import GenelUyeRaporu from '../components/GenelUyeRaporu.vue';
import Sabitler from '../components/Sabitler.vue';
import Kullanicilar from '../components/Kullanicilar.vue';
import Photo from '../components/Photo.vue';

export default [
    {path:"/", component:Uyeler, meta:{ title:"Üyeler" }  },
    {path:"/pass", component:Pass, meta:{ title:"Parola Değiştir" } },
    {path:"/gelirgider", component:GelirGider, meta:{ title:"Gelir Gider Dökümü" } },
    {path:"/tahsilat/:uye_id/:uye", component:Tahsilat, meta:{ title:"Tahsilat" },props: true },
    {path:"/uyekayit/:uye_id", component:UyeKayit, meta:{ title:"Üye Kayıt" },props:true },
    {path:"/uyeseviye/:uye_id/:uye", component:Seviye, meta:{ title:"Üye Seviye" },props:true },
    {path:"/uyeyoklama/:uye_id/:uye", component:UyeYoklama, meta:{ title:"Üye Yoklama" },props:true },
    {path:"/uyeharcamalari/:uye_id/:uye", component:UyeHarcamalari, meta:{ title:"Üye Harcamaları" },props:true },
    {path:"/photo/:uye_id/:photo/:uye", component:Photo, meta:{ title:"Üye Fotografı" },props:true },
    {path:"/geneldurum", component:GenelDurum, meta:{ title:"Genel Durum" } },
    {path:"/yoklamalar", component:Yoklamalar, meta:{ title:"Yoklamalar" } },
    {path:"/yoklama/:tarih", component:Yoklama, meta:{ title:"Yoklama"},props:true },
    {path:"/sabitler", component:Sabitler, meta:{ title:"Sabitler" } },
    {path:"/kullanicilar", component:Kullanicilar, meta:{ title:"Kullanıcılar" } },
    {path:"/geneluyeraporu",component:GenelUyeRaporu,meta:{title:"Genel Üye raporu"} }
];