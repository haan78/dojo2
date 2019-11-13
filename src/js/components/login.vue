<template>
  <div class="div_center">
    <div class="div_inner">
      <h1 class="title is-large">Login Form</h1>
      <el-form
        :model="f_login"
        :rules="r_login"
        ref="login"
        v-show="section == 'login'"
        action="index.php?a=form_authenticate"
        method="POST"
      >
        <el-form-item label="User ID" prop="user_id" >
          <el-input name="user_id" prefix-icon="el-icon-user" v-model="f_login.user_id" placeholder="User ID"></el-input>
        </el-form-item>
        <el-form-item label="Password" prop="pass">
          <el-input name="pass"
            prefix-icon="el-icon-key"
            v-model="f_login.pass"
            placeholder="Password"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-on:click="postForm('login')">Login</el-button>
          <el-button v-on:click="setForm('code')" icon="el-icon-question" circle></el-button>
        </el-form-item>
      </el-form>
      <el-form
        :model="f_code"
        :rules="r_code"
        ref="code"
        v-show="section == 'code'"
        action="index.php?a=send_password_reset_code"
        method="POST"
      >
        <el-form-item label="User ID" prop="user_id">
          <el-input name="user_id" prefix-icon="el-icon-user" v-model="f_code.user_id" placeholder="User ID"></el-input>
        </el-form-item>
        <el-button v-on:click="setForm('login')" icon="el-icon-back" circle></el-button>
        <el-button type="primary" v-on:click="postForm('code')" >Send Code</el-button>
      </el-form>
      <el-form
        :model="f_reset"
        :rules="r_reset"
        ref="reset"
        v-show="section == 'reset'"
        action="index.php?a=password_reset"
        method="POST"
      >
      <input type="hidden" name="code" :value="f_reset.code" />
        <el-form-item label="New Password" prop="pass1">
          <el-input
            name="pass"
            prefix-icon="el-icon-key"
            v-model="f_reset.pass1"
            placeholder="Password"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item label="New Password Again" prop="pass2">
          <el-input
            prefix-icon="el-icon-key"
            v-model="f_reset.pass2"
            placeholder="Password"
            show-password
          ></el-input>
        </el-form-item>
        <el-button type="primary" v-on:click="postForm('reset')" >Reset Password</el-button>
      </el-form>
    </div>
  </div>
</template>
<style>
html {
  height: 100%;
  background-color: rgb(34, 88, 48);
}

.div_center {
  background-color :rgb(193, 233, 187);
  width: 20em;
  height: 25em;

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
        callback(new Error("Please fill User ID"));
      } else {
        callback();
      }
    };
    var v_pass1 = (rule, value, callback) => {
      if (value === null || value === "") {
        callback(new Error("Please fill the password"));
      } else if (value.length < 5 || value.length > 8) {
        callback(
          new Error("The password must be maximum 8 minimum 5 characters")
        );
      } else {
        callback();
      }
    };
    var v_pass2 = (rule, value, callback) => {
      if (value !== this.f_reset.pass1) {
        callback(new Error("Password and its repeat don't match"));
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
      f_code: {
        user_id: null
      },
      f_reset: {
        code: ( typeof window.code === "string" ? window.code : null),
        pass1: null,
        pass2: null
      },
      r_login: {
        user_id: { trigger: "blur", validator: v_login_user_id },
        pass: { trigger: "blur", validator: v_pass1 }
      },
      r_code: {
        user_id: { trigger: "blur", validator: v_login_user_id }
      },
      r_reset: {
        pass1: { trigger: "blur", validator: v_pass1 },
        pass2: { trigger: "blur", validator: v_pass2 }
      }
    };
  },
  created() {
    this.setForm(window.RouterMessage.section);
    if ( window.RouterMessage.class === "danger" ) {
      this.$message.error(window.RouterMessage.text);
    } else {
      this.$message.success(window.RouterMessage.text);
    }
    
  },
  methods: {
    setForm(name) {
      this.section = name;
    },
    postForm(name) {
      this.$refs[name].validate(valid => {
        if (valid) this.$refs[name].$el.submit();
      });
    }
  }
};
</script>