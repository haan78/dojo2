<template>
  <div>
    <link type="text/css" rel="stylesheet" href="assets/default.css" />
    <Menu></Menu>
    <div style="margin: 0.5em">
      <div style="display: flex; align-items:center">
      <div style="display:inline-block;">
        <img src="assets/img/logo.png" style="width:3em; heigth:3em;" >        
      </div>
      <div style="display:inline-block; padding-left:1em; min-width:10em">
        <i class="el-icon-user"></i>{{ ($store.state.user != null ? $store.state.user.name : "-" ) }}
        <el-progress
          :width="80"
          :percentage="sessionCountdownPercent"
          type="line"
          :color="sessionCountdownColors"
          title="Session timeout"
        ></el-progress>
      </div>
      <div style="display:inline-block; padding-left:1em;">
        <b style="font-size: 1.5em;">{{ this.$router.currentRoute.meta.title }}</b>        
      </div>      
    </div>
    <div style="clear:both"></div>
    <div style="padding-top:1em;">
      <router-view />
    </div>
    </div>
  </div>
</template>
<style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
}

body a {
  cursor: pointer;
}

@import "~element-ui/lib/theme-chalk/index.css";
</style>
<script>
import Menu from "./menu.vue";

export default {
  name: "container",
  components: { Menu },
  data() {
    return {
      menu_class: "topnav",
      isMenuActive: false,
      sessionInterval: null,
      sessionCountdownLimit: 450,
      sessionCountdown: null,
      sessionCountdownPercent: 100,
      sessionCountdownColors: [
        { color: "#8B0000", percentage: 25 },
        { color: "#FF8C00", percentage: 50 },
        { color: "#006400", percentage: 100 }
      ]
    };
  },
  created() {
    let self = this;
    self.$store.commit(
      "setUser",
      typeof window.UserData === "object" ? window.UserData : null
    );
  },
  mounted() {
    let self = this;
    self.sessionCountdown = self.sessionCountdownLimit;
    self.sessionCountdownPercent = 100;
    self.sessionInterval = setInterval(() => {
      self.sessionCountdownPercent = Math.ceil(
        (self.sessionCountdown / self.sessionCountdownLimit) * 100
      );
      if (self.sessionCountdown > 0) {
        self.sessionCountdown--;
      } else {
        self.sessionEnd();
      }
    }, 1000);
  },
  destroyed() {
    clearInterval(this.sessionInterval);
  },

  methods: {
    swich_menu() {
      if (this.menu_class === "topnav") {
        this.menu_class = "topnav responsive";
      } else {
        this.menu_class = "topnav";
      }
    },
    exit() {
      window.location.href = "index.php?a=logout";
    }
  }
};
</script>