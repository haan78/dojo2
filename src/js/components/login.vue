<template>
  <div class="div_center">
    <div class="div_inner">
      <div>
        <img src="/assets/img/logo.png" style="vertical-align:middle;" />
        <span class="title">Ankara Kendo Admin</span>
      </div>
      
      <el-form
        @submit="postForm('login')"
        :model="f_login"
        :rules="r_login"
        ref="login"
        action="index.php?a=form_authenticate"
        method="POST"
      >
        <el-form-item label="Kullanıcı Adı" prop="user_id" >
          <el-input name="user_id" prefix-icon="el-icon-user" v-model="f_login.user_id" placeholder="Kullanıcı Adı"></el-input>
        </el-form-item>
        <el-form-item label="Parola" prop="pass">
          <el-input name="pass"
            prefix-icon="el-icon-key"
            v-model="f_login.pass"
            placeholder="Parola"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-on:click="postForm('login')" style="width:100%">Giriş</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<style>
html {
  height: 100%;
}

.title {
  font-weight: bolder;
  font-size: larger;
}

body {
  background: url(/assets/img/bg1.jpg) no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}

.div_center {
  background-color : bisque;
  width: 20em;
  height: 21em;

  position: absolute;
  top: -10em;
  bottom: 0;
  left: 0;
  right: 0;

  margin: auto;
}

.div_center .div_inner {
  padding: 1em;
}
@import "~element-ui/lib/theme-chalk/index.css";
</style>
<script>
export default {
  name: "login",
  data() {
    var v_login_user_id = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("Kullanıcı adı gerekli"));
      } else {
        callback();
      }
    };
    var v_pass1 = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Parola gerekli"));
      } else if (value.length < 5 || value.length > 8) {
        callback(
          new Error("Parola en az 5 en fazla 8 karakter arasında olmalı.")
        );
      } else {
        callback();
      }
    };

    return {
      section: window.RouterMessage.section,
      class: window.RouterMessage.class,
      text: window.RouterMessage.text,
      f_login: {
        user_id: null,
        pass: null
      },
      r_login: {
        user_id: { trigger: "blur", validator: v_login_user_id },
        pass: { trigger: "blur", validator: v_pass1 }
      }
    };
  },
  created() {
    if ( window.RouterMessage.class === "danger" ) {
      this.$message.error(window.RouterMessage.text);
    } else {
      this.$message.success(window.RouterMessage.text);
    }
    
  },
  methods: {
    postForm(name) {
      this.$refs[name].validate(valid => {
        if (valid) this.$refs[name].$el.submit();
      });
    }
  }
};
</script>