<template>
  <div class="container">
    <el-form :model="form" :rules="rules" label-width="8em" size="mini" ref="FORM" v-loading="loading" >

      <el-form-item label="Üye Adı" prop="uye">
        <el-input prefix-icon="el-icon-user" v-model="form.uye" placeholder="Üyenin tam adı"></el-input>
      </el-form-item>

      <el-form-item label="Üye Türü" prop="uye_tur">
        <el-radio-group v-model="form.uye_tur">
          <el-radio-button label="OGRENCI">Öğrenci</el-radio-button>
          <el-radio-button label="TAM">Tam</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="Doğum tarihi" prop="dogum_tarihi">
        <el-date-picker
          style="width:100%"
          v-model="form.dogum_tarihi"
          placeholder="Doğum Tarihi"
          :picker-options="{firstDayOfWeek:1}"
          format="dd.MM.yyyy"
          value-format="yyyy-MM-dd"
        ></el-date-picker>
      </el-form-item>

      <el-form-item label="Üyelik Durumu" prop="aktif">
        <el-radio-group v-model="form.aktif">
          <el-radio-button :label="1">Aktif</el-radio-button>
          <el-radio-button :label="0">Pasif</el-radio-button>
        </el-radio-group>
      </el-form-item>

        <el-form-item label="Cinsiyet" prop="cinsiyet">
        <el-radio-group v-model="form.cinsiyet">
          <el-radio-button label="KADIN">Kadın</el-radio-button>
          <el-radio-button label="ERKEK">Erkek</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="E-Posta adresi" prop="eposta">
        <el-input prefix-icon="el-icon-message" v-model="form.eposta" placeholder="E-Posta adresi"></el-input>
      </el-form-item>

      <el-form-item label="EKF No" prop="ekf_no">
        <el-input prefix-icon="el-icon-medal" v-model="form.ekf_no" placeholder="EKF No"></el-input>
      </el-form-item>

    </el-form>

    <el-button type="primary" @click="kaydet()" style="width:100%" :loading="loading" >Kaydet</el-button>
  </div>
</template>
<script>
export default {
  data() {
    let self = this;

    var v_uye = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Üye adını girmeniz gerekiyor"));
      } else {
        callback();
      }
    };

    var v_uye_tur = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Üye türünü belirtmeniz gerekiyor"));
      } else {
        callback();
      }
    };

    var v_dogum_tarihi = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Doğum tarihini belirtmeniz gerekiyor"));
      } else {
        callback();
      }
    };

    var v_cinsiyet = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Cinsiyeti belirtmeniz gerekiyor"));
      } else {
        callback();
      }
    };

    var v_eposta = (rule, value, callback) => {
        if ( value === null || value === "" ) {
            callback();
        } else if ( !self.$is.anEmail(value) ) {
            callback(new Error("E-Posta adresi doğru formatta değil"));
        } else {
            callback();
        }
    };

    return {
        loading:false,
      form: {
        uye: null,
        uye_tur: null,
        dogum_tarihi: null,
        uyelik_tarihi: null,
        aktif: 1,
        cinsiyet: null,
        eposta: null,
        photo: null,
        ekf_no: null,
        uye_id:0
      },
      rules: {
        uye: {
          trigger: "blur",
          validator: v_uye
        },
        uye_tur: {
          trigger: "blur",
          validator: v_uye_tur
        },
        dogum_tarihi: {
          trigger: "blur",
          validator: v_dogum_tarihi
        },
        cinsiyet: {
          trigger: "blur",
          validator: v_cinsiyet
        },
        eposta: {
          trigger: "blur",
          validator: v_eposta
        }
      }
    };
  },
  props: ["uye_id"],
  created() {
      this.form.uye_id = this.uye_id;
      this.load();
  },
  methods:{
      load() {
        let self = this;
        if ( self.form.uye_id > 0 ) {
            self.WebMethod("uye_detay",[self.uye_id],(response)=>{
                self.form.uye = response.result.uye;
                self.form.uye_tur = response.result.uye_tur;
                self.form.dogum_tarihi = response.result.dogum_tarihi;
                self.form.uyelik_tarihi = response.result.uyelik_tarihi;
                self.form.aktif = response.result.aktif;
                self.form.cinsiyet = response.result.cinsiyet;
                self.form.eposta = response.result.eposta;
                self.form.photo = response.result.photo;
                self.form.ekf_no = response.result.ekf_no;
            });
        } else {
            //insert
            self.form.uye = null;
            self.form.uye_tur = null;
            self.form.dogum_tarihi = null;
            self.form.uyelik_tarihi = null;
            self.form.aktif = 1;
            self.form.cinsiyet = null;
            self.form.eposta = null;
            self.form.photo = null;
            self.form.ekf_no = null;
            self.form.uye_id = 0;
        }
      },
      kaydet() {
        let self = this;
        self.$refs.FORM.validate(valid => {
            if ( valid ) {
                self.WebMethod("uye",[
                    self.form.uye,
                    self.form.uye_tur,
                    self.form.dogum_tarihi,
                    self.form.uyelik_tarihi,
                    self.form.aktif,
                    self.form.cinsiyet,
                    self.form.eposta,
                    self.form.ekf_no,
                    self.form.uye_id
                    ],(response)=>{
                      if ( self.form.uye_id === 0 && response.result.id > 0 ) {
                        self.form.uye_id = response.result.id;
                      }
                      self.$message.success("Kayıt işlemi başarıyla gerçekleşti");
                });
            }
        });
      }
  }
};
</script>