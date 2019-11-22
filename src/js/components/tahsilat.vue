<template>
  <div class="container">
    <el-tabs v-model="tab">
      <el-tab-pane label="Aidat Eksikleri" name="eksikler">
        <el-table :data="eksikler">
          <el-table-column label="Yıl" prop="yil"></el-table-column>
          <el-table-column label="Ay" prop="ay"></el-table-column>
          <el-table-column fixed="right" label>
            <template slot-scope="scope">
              <el-button @click="open({ay:scope.row.ay,yil:scope.row.yil})" size="small">Öde</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
      <el-tab-pane label="Yapılmış Özdemeler" name="odemeler">
        <el-table :data="odemeler">
          <el-table-column label="Tarih">
            <template slot-scope="scope">{{ $date.toTurkish(scope.row.tarih) }}</template>
          </el-table-column>

          <el-table-column label="Bilgi">
            <template slot-scope="scope">{{ odeme_bilgi(scope.row) }}</template>
          </el-table-column>

            <el-table-column label="Tutar">
            <template slot-scope="scope">
                <el-button @click="open(scope.row)" size="small" >{{ scope.row.tutar }}</el-button>
            </template>
          </el-table-column>

        </el-table>
        <div class="form-inline">
          <div class="item">
            <el-select v-model="od_page" @change="load_odemeler()" :loading="loading" size="small" >
      <el-option v-for="p in odPageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
    </el-select>
          </div>
          <div class="item">
            <el-button type="success" @click="open({})" size="small">Ödeme Al</el-button>
          </div>
        </div>
        
    
      </el-tab-pane>
    </el-tabs>
    <el-dialog title="Ödeme" :visible.sync="dialogVisible" v-loading="loading" width="80%">
        <el-form :model="odeme" label-width="5em" size="mini" ref="FORM">
          <el-form-item label="Türü">
            <el-select v-model="odeme.odeme_tur_id" placeholder="Türü" @change="turSelect">
              <el-option v-for="( ot,ind ) in odeme_turleri" :key="ind" :label="ot.odeme_tur" :value="ot.odeme_tur_id"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Ay">
            <el-input-number v-model="odeme.ay" :step="1" :precision="0"></el-input-number>
          </el-form-item>
          <el-form-item label="Yıl">
            <el-input-number v-model="odeme.yil" :step="1" :precision="0"></el-input-number>
          </el-form-item>
          <el-form-item label="Tutar">
            <el-input-number v-model="odeme.tutar" :step="5" :precision="2"></el-input-number>
          </el-form-item>
          <el-form-item label="Tarih">
            <el-date-picker v-model="odeme.tarih" :picker-options="{firstDayOfWeek:1}"
          format="dd.MM.yyyy" style="width:100%"
          value-format="yyyy-MM-dd" placeholder="Tahsilat Tarihi"></el-date-picker>
          </el-form-item>
          <el-form-item label="Açıklama">
            <el-input v-model="odeme.aciklama" placeholder="Açıklama"></el-input>
          </el-form-item>
          <el-button type="success" style="width:100%" >Tahsilat</el-button>
        </el-form>
    </el-dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loading: false,
      tab: null,
      eksikler: [],
      odemeler: [],
      odeme_turleri:[],
      od_limit: 10,
      od_maxrow:0,
      od_page:1,
      dialogVisible:false,
      odeme:{
          odeme_id:null,
          odeme_tur_id:null,
          tarih:null,
          tutar:null,
          ay:null,
          yil:null,
          aciklama:null
      }
    };
  },
  computed:{
    odPageCount() {
      return Math.ceil(this.od_maxrow / this.od_limit);
    }
  },
  props: ["uye_id","uye"],
  watch: {
    /*tab() {
      if (this.tab === "eksikler") {
        this.load_eksikler();
      } else {
        this.load_odemeler();
      }
    }*/
  },
  created() {    
    this.load_sabitler();
    this.load_eksikler();
    this.load_odemeler();
  },
  methods: {
    load_eksikler() {
      let self = this;
      self.WebMethod("aidat_eksigi", [self.uye_id], response => {
        self.eksikler = response.result;
        self.tab = (this.eksikler.length > 0  ? "eksikler" : "odemeler") ;
      });
    },
    load_sabitler() {
      let self = this;
      self.WebMethod("odeme_turleri",[],(response)=>{
        self.odeme_turleri = response.result;
      });
    },
    load_odemeler() {
      let self = this;
      self.WebMethod(
        "uye_odemeleri",
        [self.uye_id, null, ((self.od_page - 1) * self.od_limit), self.od_limit],
        response => {
          self.odemeler = response.result;
          self.od_maxrow = response.outputs.maxrow;
        }
      );
    },
    odeme_bilgi(o) {
      if (o.odeme_tur.includes("AIDAT")) {
        return o.ay + " / " + o.yil;
      } else if (o.odeme_tur.includes("SINAV")) {
        return "SINAV";
      } else {
        return "DİĞER";
      }
    },
    turSelect(obj) {
      console.log(obj);
      if (this.odeme.odeme_tur_id!==null) {
        this.odeme.tutar = this.odeme_turleri.find( (el)=>{  return el.odeme_tur_id === this.odeme.odeme_tur_id } ).tutar;
      } else {
        this.odeme.tutar = null;
      }
    },
    save(){
      let self = this;
    },
    open(obj) {
      this.dialogVisible = true;
      var defaults = (obj === null ? {} : obj);
      for( var k in this.odeme ) {
        if ( typeof defaults[k] !== "undefined" ) {
          this.odeme[k] = defaults[k];
        } else {
          if ( k === "ay" ) {
            this.odeme.ay = ( new Date() ).getMonth()+1;
          } else if (  k === "yil" ) {
            this.odeme.yil = ( new Date() ).getFullYear();
          } else {
            this.odeme[k] = null;
          }          
        }
      }
    }
  }
};
</script>