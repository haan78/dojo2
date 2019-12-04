<template>
    <div class="container">
        <el-button type="text" @click="link('/yoklamalar')" icon="el-icon-back">Yoklamalara Geri Dön ( {{ $date.toTurkish(tarih) }} )</el-button>                
        <el-tabs v-model="tab">
          <el-tab-pane :label="'Aktif Üyleler ('+aktifler.length+')'" name="aktifler">
              <div class="list">
                  <div class="item" v-for="(item,index) in aktifler" :key="index" v-loading="loading" title="Yoklamaya eklemek için tıklayın">
                      <button :disabled="loading" class="aktifler" @click="add(item.uye_id)">
                          <img :src="( item.photo !== null ? 'uploads/photos/'+item.photo : 'assets/img/kendoka.jpg' )" />
                          <span>{{ item.uye }}</span>
                      </button>
                  </div>
              </div>
          </el-tab-pane>
          <el-tab-pane :label="'Gelen Üyleler ('+gelenler.length+')'" name="gelenler">
              <div class="list">
                  <div class="item" v-for="(item,index) in gelenler" :key="index" v-loading="loading" title="Yoklamadan çıkarmak için tıklayın">
                      <button :disabled="loading" class="gelenler" @click="remove(item.uye_id)">
                          <img :src="( item.photo !== null ? 'uploads/photos/'+item.photo : 'assets/img/kendoka.jpg' )" />
                          <span>{{ item.uye }}</span>
                      </button>
                  </div>
              </div>
          </el-tab-pane>
        </el-tabs>
    </div>
</template>
<style>
    .aktifler {
        background-color: darkgreen;
        border: 0.2em solid black;
        cursor: pointer;
        color: beige;
    }
    .gelenler {
        background-color: darkred;
        border: 0.2em solid black;
        color: beige;
        cursor: pointer;
    }

    .list .item button span {
        padding-left: 1em;
    }

    .list .item button {
        width: 100%;
        text-align: left;
    }

    .list .item button img {
        width: 4em;
        height: 6em;
        vertical-align: middle;        
    }
    
</style>
<script>
export default {
    data() {
        return {
            loading:false,
            gelenler:[],
            aktifler:[],
            tab:"gelenler"
        }
    },
    created() {
        this.load();
    },
    props:["tarih"],
    methods:{
        load() {
            let self = this;
            self.WebMethod("yoklamadaki_uyeler",[ self.tarih ],response=>{
                self.gelenler = response.result.gelenler;
                self.aktifler = response.result.aktifler;
                if ( self.gelenler.length < 1 ) {
                    self.tab = "aktifler";
                }
            });
        },
        add(uye_id) {
            let self = this;
            self.WebMethod("yoklamaya_ekle",[self.tarih,uye_id],response=>{
                self.load();
            });
        },
        remove(uye_id) {
            let self = this;
            self.WebMethod("yoklamadan_sil",[self.tarih,uye_id],response=>{
                self.load();
            });
        }
    }
}
</script>