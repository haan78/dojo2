<template>
  <div class="container">
    <el-form :model="form" :rules="rules" ref="FORM">
      <el-form-item label="Eski Parola" prop="pass1">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass1"
          placeholder="Eski Parola"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item label="Yeni Parola" prop="pass2">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass2"
          placeholder="Yeni Parola"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item label="Tekrar Yeni Parola" prop="pass3">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass3"
          placeholder="Tekrar Yeni Parola"
          show-password
        ></el-input>
      </el-form-item>
      <el-button type="primary" style="width:100%" @click="save()">Kaydet</el-button>
    </el-form>
  </div>
</template>
<script>
export default {
  data() {
      let self = this;
    var p3v = (rule, value, callback) => {
            if (value !== self.form.pass2) {
              callback(new Error("Yeni parola ile tekrarı uyuşmuyor"));
            } else {
              callback();
            }
          };
    return {
      form: {
        pass1: null,
        pass2: null,
        pass3: null
      },
      rules: {
        pass1: {
          trigger: "blur",
          validator: (rule, value, callback) => {
            if (value === null || value === "") {
              callback(new Error("Lütfen eski parolayı girin"));
            } else {
              callback();
            }
          }
        },
        pass2: {
          trigger: "blur",
          validator: (rule, value, callback) => {
            if (
              value === null ||
              value === "" ||
              value.length < 5 ||
              value.length > 8
            ) {
              callback(
                new Error(
                  "Yeni parola en az beş en çok sekiz karakterden oluşmalı"
                )
              );
            } else {
              callback();
            }
          }
        },
        pass3: {
          trigger: "blur",
          validator: p3v
        }
      }
    };
  },
  methods: {
    save() {
      let self = this;

      self.$refs.FORM.validate(valid => {
        if (valid) {
          self.WebMethod(
            "pass",
            [self.form.pass1, self.form.pass2],
            () => {
              self.form.pass1 = null;
              self.form.pass2 = null;
              self.form.pass3 = null;
              self.$message.success("Parola başarıyla değiştirildi");
            },
            (type, detail) => {
              self.form.pass1 = null;
              self.form.pass2 = null;
              self.form.pass3 = null;
              self.$message.error(detail);
            }
          );
        }
      });
    }
  }
};
</script>