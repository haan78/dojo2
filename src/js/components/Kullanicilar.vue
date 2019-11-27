<template>
  <div class="container">
    <el-button type="success" @click="open()" icon="el-icon-plus">Yeni Kullanıcı</el-button>
    <el-table :data="list">
      <el-table-column label="Kullanıcı" prop="kullanici"></el-table-column>
      <el-table-column label="Detaylar">
        <template slot-scope="scope">
          <el-button size="small" type="info" @click="open(scope.row)" icon="el-icon-edit">Düzelt</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog title="Kullanıcı" :visible.sync="dialogVisible" v-loading="loading" width="80%">
      <el-form :model="kullanici" ref="FORM" :rules="rules" label-position="top">
        <el-form-item label="Kullanıcı" prop="kullanici">
          <el-input v-model="kullanici.kullanici" placeholder="Kullanıcı Adı"></el-input>
        </el-form-item>
        <el-form-item label="Yetki" prop="yetki">
          <el-select v-model="kullanici.yetki" placeholder="Yetki Seviyesi">
            <el-option label="Gözlemci" value="OBSERVER"></el-option>
            <el-option label="Kullanıcı" value="USER"></el-option>
            <el-option label="Yönetici" value="ADMIN"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Parola" prop="parola">
          <el-input v-model="kullanici.parola" placeholder="Parola" show-password></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="save()" :loading="loading">Kaydet</el-button>
        <el-button type="danger" @click="del()" :loading="loading">Sil</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<style>
.el-dialog {
  max-width: 30em;
}
</style>
<script>
export default {
  data() {
    var val_kullanici = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Kullanıcı adı gerekli"));
      } else {
        callback();
      }
    };
    var val_yetki = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Yetki seviyesi seçilmemiş"));
      } else {
        callback();
      }
    };
    var val_parola = (rule, value, callback) => {
      if (value === null || value === "" || value.length < 5) {
        callback(new Error("En az 5 karakter uzunluğunda bir parola olmalı"));
      } else {
        callback();
      }
    };
    return {
      loading: false,
      dialogVisible: false,
      list: [],
      kullanici: {
        kullanici_id: null,
        kullanici: null,
        yetki: null,
        parola: null
      },
      rules: {
        kullanici: { trigger: "blur", validator: val_kullanici },
        yetki: { trigger: "blur", validator: val_yetki },
        parola: { trigger: "blur", validator: val_parola }
      }
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      let self = this;
      self.WebMethod("kullanicilar", [], response => {
        self.list = response.result;
      });
    },
    del() {
      let self = this;
      if (self.kullanici.kullanici_id !== null) {
        this.$confirm("Bu kaydı silmek istediğinizden emin misiniz?", "Uyarı", {
          confirmButtonText: "Evet",
          cancelButtonText: "Hayır",
          type: "warning",
          customClass: "confirm-box"
        })
          .then(() => {
            self.WebMethod(
              "kullanici_sil",
              [self.kullanici.kullanici_id],
              response => {
                  self.dialogVisible = false;
                  self.load();
              }
            );
          })
          .catch(() => {});
      }
    },
    save() {
      let self = this;
      self.$refs.FORM.validate(valid => {
        if (valid) {
          self.WebMethod(
            "kullanici",
            [
              self.kullanici.kullanici,
              self.kullanici.yetki,
              self.kullanici.parola,
              self.kullanici.kullanici_id
            ],
            response => {
              self.dialogVisible = false;
              self.load();
            }
          );
        }
      });
    },
    open(row) {
      if (row) {
        this.kullanici.kullanici_id = row.kullanici_id;
        this.kullanici.kullanici = row.kullanici;
        this.kullanici.yetki = row.yetki;
      } else {
        this.kullanici.kullanici_id = null;
        this.kullanici.kullanici = null;
        this.kullanici.yetki = null;
      }
      this.kullanici.parola = null;
      this.dialogVisible = true;
    }
  }
};
</script>