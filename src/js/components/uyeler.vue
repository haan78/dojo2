<template>
  <div>
    <div class="form-inline">            
      <div class="item">        
        <el-input v-model="search" placeholder="Üye adı" prefix-icon="el-icon-search" @change="load(true)"  >
          <el-select v-model="aktif" placeholder="Üyelik Dururmu" slot="prepend" @change="load(true)" >
          <el-option :value="true" label="Aktifler" ></el-option>
          <el-option :value="false" label="Pasifler" ></el-option>
          <el-option :value="null" label="Hepsi" ></el-option>
        </el-select>
          <el-button slot="append" icon="el-icon-refresh" title="Boşalt" @click="refresh()"></el-button>
        </el-input>
        
      </div>

<div class="item">

        <el-radio-group v-model="siralama" size="medium" @change="load(true)" >
          <el-radio-button label="AD" title="Ada göre sırala" >İsim</el-radio-button>
          <el-radio-button  label="GECIKME" title="Geciken aidatlara göre sırala" >Borç</el-radio-button>
          <el-radio-button label="SON_KEIKO" title="Son geldiği keikoya göre sırala" >Devam</el-radio-button>
          <el-radio-button label="SEVIYE" title="Seviyeye göre sırala" >Seviye</el-radio-button>
    </el-radio-group>
      </div>

      <div class="item">
        <el-button @click="link('uyekayit/0')" icon="el-icon-circle-plus-outline" title="Yeni üye kaydı">Yeni Üye</el-button>
      </div>

    </div>
    <div class="card">

        <div v-for="(item,index) in list" :key="index" class="gitem">
            <table  >
                    <tr>
                        <td rowspan="5" ><img :src="( item.photo !== null ? 'assets/photos/'+item.photo : 'assets/img/kendoka.jpg' ) " @click="link('/photo/'+item.uye_id+'/'+item.photo+'/'+item.uye)" ></td>
                        <td><el-button type="text" icon="el-icon-user-solid" @click="link('/uyekayit/'+item.uye_id)" >{{ item.uye }}</el-button></td>
                    </tr>
                    <tr><td><el-button :type="getButtonType('eksik',item.aidat_eksigi)" icon="el-icon-money" @click="link('/tahsilat/'+item.uye_id+'/'+item.uye)" >Aidatlar{{ item.aidat_eksigi > 0 ? ' ('+item.aidat_eksigi+')' : '' }}</el-button></td></tr>
                    <tr><td><el-button type="primary" icon="el-icon-sort-up" @click="link('/uyeseviye/'+item.uye_id+'/'+item.uye)" >Seviye( {{item.seviye}} )</el-button></td></tr>
                    <tr><td><el-button :type="getButtonType('keiko',item.son_geldigi_keiko)" icon="el-icon-finished" @click="link('/uyeyoklama/'+item.uye_id+'/'+item.uye)" >Keikolar( {{ $date.toTurkish(item.son_geldigi_keiko) }} )</el-button></td></tr>
                    <tr><td><el-button type="info" icon="el-icon-magic-stick" @click="link('/uyeharcamalari/'+item.uye_id+'/'+item.uye)">Harcamalar</el-button></td></tr>
                </table>
        </div>

    </div>
    <br/>
    <el-select v-model="page" @change="load()" :loading="loading" >
      <el-option v-for="p in pageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
    </el-select>
  </div>
</template>
<style>

    .form-inline .item .el-select .el-input {
      width: 110px;
    }

    .card {
        display: grid;
        grid-gap: 1em;
        grid-template-columns: auto;
    }

    .card .gitem {
      border: 0.2em solid black;
    }

    .card .gitem table {
        width: 100%;        
    }

    .card .gitem table tr td[rowspan="5"] {
      width: 5%;  
    }


    .card .gitem table tr td button {
        width: 100%;
    }

    .card .gitem table tr td img  {
        width: 10em;
        height: 15em;
        cursor: pointer;
    }

    @media (min-width: 750px) {
      .card {        
        grid-template-columns: auto auto;
      }
    }

    @media (min-width: 900px) {
      .card {        
        grid-template-columns: auto auto auto;
      }
    }

    @media (min-width: 1150px) {
      .card {        
        grid-template-columns: auto auto auto auto;
      }
    }

    @media (min-width: 1350px) {
      .card {        
        grid-template-columns: auto auto auto auto auto;
      }
    }



</style>
<script>
export default {
  data() {
    return {
      loading: false,
      start: 0,
      limit: 120,
      search: null,
      siralama: "AD",
      aktif: true,
      list: [],
      maxrow: 0,
      page:1
    };
  },
  created() {
    this.load(true);
  },
  computed:{
    pageCount() {
      return Math.ceil(this.maxrow / this.limit);
    }
  },
  watch:{
    search() {
      if ( this.search !== null  && this.search.length > 2 ) {
        this.load(true);
      }
    }
  },
  methods: {
    load(reset) {
      let self = this;
      if ( reset ) {
        self.page = 1;
      }
      self.WebMethod(
        "uyeler",
        [self.search, self.aktif, self.siralama, ((self.page - 1) * self.limit), self.limit],
        response => {
          self.list = response.result;
          self.maxrow = response.outputs.maxrow;
        }
      );
    },
    getButtonType(type,value) {
      if ( type == 'eksik' ) {
        if ( value > 2 ) {
          return "danger";
        } else if (value>0) {
          return "warning";
        } else {
          return "success";
        }
      } else if ( 'keiko' ) {
        if ( value === null ) {
          return "danger";
        }
        var sgk = this.$date.toDate(value);
        var sim = new Date();
        var diff = this.$date.diffDays(sgk,sim);
        if ( diff >= 90 ) {
          return "danger";
        } else if ( diff >= 30 ) {
          return "warning";
        } else {
          return "success";
        }
      }
    },
    refresh() {
      this.search=null;
      this.load(true);
    }

  }
};
</script>