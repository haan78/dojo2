<template>
  <div class="container">
    <el-form :model="form" :rules="rules" ref="FORM">
      <el-form-item label="Old Password" prop="pass1">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass1"
          placeholder="Old Password"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item label="New Password" prop="pass2">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass2"
          placeholder="New Password"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item label="New Password Repeat" prop="pass3">
        <el-input
          prefix-icon="el-icon-key"
          v-model="form.pass3"
          placeholder="New Password Repeat"
          show-password
        ></el-input>
      </el-form-item>
      <el-button type="primary" style="width:100%" @click="save()">Save</el-button>
    </el-form>
  </div>
</template>
<script>
export default {
  data() {
      let self = this;
    var p3v = (rule, value, callback) => {
            if (value !== self.form.pass2) {
              callback(new Error("New password and its repeat don't match."));
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
              callback(new Error("Please fill old password"));
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
                  "The new password can have a minimum of five and a maximum of eight characters"
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
              self.$message("Parola başarıyla değiştirildi");
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