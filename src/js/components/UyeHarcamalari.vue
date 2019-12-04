<template>
  <div class="container">
      <h2>{{ uye }}</h2>
      <el-table :data="list" size="small">        
        <el-table-column label="Tarih" >
            <template slot-scope="scope">
              {{ $date.toTurkish(scope.row.tarih) }}<br/>{{  scope.row.gider_tur }}
                </template>
          </el-table-column>
          <el-table-column label="Tutar" prop="tutar" ></el-table-column>
          <el-table-column label="Tutar" >
            <template slot-scope="scope">
              <el-button size="small" type="primary" title="Kaydı züdeltmek için tıklayın" @click="open(scope.row)" icon="el-icon-edit"></el-button>
            </template>
          </el-table-column>
      </el-table>
      <div>
        <b>Toplam Harcamalar: {{ total }} TL</b>
      </div>
      <div class="form-inline">
        <div class="item">
          <el-select v-model="page" @change="load()" :loading="loading" size="small" >
      <el-option v-for="p in pageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
    </el-select>
        </div>
        <div class="item">
          <el-button type="success" @click="open()" size="small">Yeni Harcama</el-button>
        </div>
      </div>
      <el-dialog :title="uye" :visible.sync="dialogVisible" v-loading="loading" width="100%">
        <el-form :model="gider" label-position="top" ref="FORM" :rules="rules" size="mini" >
          <el-form-item label="Tarih" prop="tarih">
            <el-date-picker
            v-model="gider.tarih"
            :picker-options="{firstDayOfWeek:1}"
            format="dd.MM.yyyy"
            style="width:100%"
            value-format="yyyy-MM-dd"
            placeholder="Veriliş Tarihi"
          ></el-date-picker>

          </el-form-item>
          <el-form-item label="Tür" prop="gider_tur_id">
            <el-select v-model="gider.gider_tur_id" placeholder="">
              <el-option v-for="(g,i) in gider_turleri" :key="i" :label="g.gider_tur" :value="g.gider_tur_id" ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tutar" prop="tutar">
            <el-input-number v-model="gider.tutar" :step="5" :precision="2"></el-input-number>
          </el-form-item>
          <el-form-item label="Açıklama">
            <el-input v-model="gider.aciklama" placeholder="Açıklama"></el-input>
          </el-form-item>
          <el-form-item label="Belge">
            <el-upload action="index.php?a=upload" :limit="1" :file-list="fileList" :on-success="sonuc" list-type="picture">
              <el-button type="primary" icon="el-icon-camera" >Fotograf Yükle</el-button>
          <div slot="tip" class="el-upload__tip">Yüklemek istediğiniz fotoğrafı seçin</div>
            </el-upload>
          </el-form-item>
        </el-form>
        <div class="form-inline">
          <div class="item" v-show="gider.belge !== null">
            <el-button type="info" icon="el-icon-picture" @click="belge(gider.belge)">Belge</el-button>
          </div>

          <div class="item" v-show="gider.gider_id!==null">
            <!--Budan devam et-->
            <el-button type="danger" style="width:100%" icon="el-icon-delete" @click="del()">Sil</el-button>
          </div>          
          <div class="item">
            <el-button
              type="success"
              style="width:100%"
              icon="el-icon-success"
              @click="save()"
            >Ver</el-button>
          </div>
        </div>
      </el-dialog>
  </div>
</template>
<style>
</style>
<script>
export default {
  data() {

    var r_tarih = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Harcamanın yapıldığı tarihi girin"));
      } else {
        callback();
      }
    };

    var r_gider_tur_id = (rule, value, callback) => {
      if (value === null) {
        callback(new Error("Bir gider türü seçin"));
      } else {
        callback();
      }
    };

    var r_tutar = (rule, value, callback) => {
      if (value === null) {
        callback(new Error("Mantıklı bir rakam girin"));
      } else {
        callback();
      }
    };


    return {
      loading: false,
      dialogVisible: false,
      list: [],
      page: 1,
      limit: 15,
      maxrow:0,
      total:0,
      gider: {
        gider_id: null,
        aciklam: null,
        tarih: null,
        tutar: null,
        belge: null,
        gider_tur_id: null
      },
      rules:{
        tarih:{
          trigger: "blur",
          validator: r_tarih
        },
        gider_tur_id:{
          trigger: "blur",
          validator: r_gider_tur_id
        },
        tutar:{
          trigger: "blur",
          validator: r_tutar
        }
      },
      gider_turleri: [],
      fileList:[]
    };
  },
  created() {
    this.load_tur();
    this.load();
  },
  props: ["uye_id", "uye"],
  computed: {
    pageCount() {
      return Math.ceil(this.maxrow / this.limit);
    }
  },
  methods: {
    sonuc(response, file, fileList) {            
            var rl = response.split(" ");
            if ( rl[0]==="0" ) {
              this.gider.belge = rl[1];
              this.$parent.sessionCountdown = this.$parent.sessionCountdownLimit;
            } else {
              this.$message.error("UPLOAD ERROR / "+response);
            }            
        },
    load_tur() {
      let self = this;
      self.WebMethod("gider_turleri",[],response=>{
        self.gider_turleri = response.result;
      });
    },
    load() {
      let self = this;
      self.WebMethod("uye_harcamalari",[ self.uye_id,((self.page - 1) * self.limit), self.limit],response=>{
        self.list = response.result;
        self.total = response.outputs.total;
        self.maxrow = response.outputs.maxrow;
      });
    },
    save() {
      let self = this;
      self.$refs.FORM.validate(valid=>{
        if (valid) {
          //$tarih,$uye_id,$tutar,$gider_tur_id,$belge,$aciklama,$gider_id = false
          self.WebMethod("harcama",[self.gider.tarih,self.uye_id,self.gider.tutar,self.gider.gider_tur_id,self.gider.belge,self.gider.aciklama,self.gider.gider_id],response=>{
            self.dialogVisible = false;
            self.load();
          });
        }
      });
    },
    del() {
      let self = this;
      if ( self.gider.gider_id !==null ) {
        this.$confirm("Bu kaydı silmek istediğinizden emin misiniz?", "Uyarı", {
          confirmButtonText: "Evet",
          cancelButtonText: "Hayır",
          type: "warning",
          customClass:"confirm-box"
        })
          .then(() => {
            self.WebMethod("harcama_sil", [self.gider.gider_id], response => {
              self.dialogVisible = false;
              self.load();
            });
          })
          .catch(() => {});
      }
    },
    open(row) {
      if (row) {
        for(var e in row) {
          if (typeof this.gider[e] !== "undefined") {
            this.gider[e] = row[e];
          }
        }
        this.gider.uye_id = this.uye_id;
      } else {
        for(var e in this.gider) {
          this.gider[e] = null;
        }
      }
      this.dialogVisible = true;
    },
    belge(belge) {
      window.open("uploads/docs/"+belge,"_blank");
    }
  }
};
</script>