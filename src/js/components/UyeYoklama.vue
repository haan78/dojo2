<template>
  <div class="container">
    <h2>{{ uye }}</h2>
    <el-button
      type="success"
      style="width:100%"
      icon="el-icon-circle-plus-outline"
      :loading="loading" @click="open()"
    >Yeni Yoklama</el-button>
    <el-table :data="list" :loading="loading">
      <el-table-column label="Keiko Tarihleri">
        <template slot-scope="scope">
          <el-button
            size="small"
            style="width:100%"
            type="danger"
            @click="del(scope.row)"
            icon="el-icon-delete"
          >Sil {{ $date.toTurkish( scope.row.tarih ) }}</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-select v-model="page" @change="load()" :loading="loading">
      <el-option v-for="p in pageCount" :key="p" :value="p" :label="'Sayfa ' + p"></el-option>
    </el-select>
    <el-dialog title="Keiko Tarihi" :visible.sync="dialogVisible" v-loading="loading">
      <el-form :model="keiko" ref="FORM" :rules="rules">
        <el-form-item label="Tarih" prop="tarih">
            <el-date-picker v-model="keiko.tarih" placeholder="Keiko Tarihi" :picker-options="{firstDayOfWeek:1}"
            format="dd.MM.yyyy"
            style="width:100%"
            value-format="yyyy-MM-dd"></el-date-picker>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="save()">Kaydet</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
export default {
  data() {
    var val_tarih = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Lütfen keiko tarihini seçin"));
      } else {
        callback();
      }
    };

    return {
      loading: false,
      dialogVisible: false,
      list: [],
      maxrow: 0,
      tarih: null,
      start: 0,
      limit: 12,
      page: 1,
      keiko: {
        tarih: null
      },
      rules: {
        tarih: {
          tarih: {
            trigger: "blur",
            validator: val_tarih
          }
        }
      }
    };
  },
  created() {
    this.load();
  },
  computed: {
    pageCount() {
      return Math.ceil(this.maxrow / this.limit);
    }
  },
  props: ["uye_id", "uye"],
  methods: {
    load() {
      let self = this;
      self.WebMethod(
        "uyenin_yoklamalari",
        [self.uye_id, (self.page - 1) * self.limit, self.limit],
        response => {
          self.list = response.result;
          self.maxrow = response.outputs.maxrow;
        }
      );
    },
    save() {
        let self = this;
        self.$refs.FORM.validate( valid =>{
            if ( valid ) {
                self.WebMethod("uye_yoklama_ekle",[ self.uye_id,self.keiko.tarih ],(response)=>{
                    self.dialogVisible = false;
                    self.load();
                });
            }
        } );
    },
    del( row ) {
        let self = this;
        this.$confirm("Bu kaydı silmek istediğinizden emin misiniz?", "Uyarı", {
          confirmButtonText: "Evet",
          cancelButtonText: "Hayır",
          type: "warning",
          customClass:"confirm-box"
        })
          .then(() => {
            self.WebMethod("uye_yoklama_sil", [row.yoklama_id], response => {
              self.load();              
            });
          })
          .catch(() => {});
    },
    open() {
        let self = this;
        self.dialogVisible = true;
        self.keiko.tarih = null;
    }
  }
};
</script>