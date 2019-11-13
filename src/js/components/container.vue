<template>
  <div>
    <div :class="menu_class" id="myTopnav">      
 <div @click="link('/')" class="logo">
    <img src="assets/img/logo.png" class="logo" >
  </div>
  <div class="item"><a v-on:click="link('/pass')"><i class="el-icon-key"></i>Change Password</a></div>
  <div class="item"><a href="index.php?a=logout"><i class="el-icon-close"></i>Exit</a></div>
  <div class="icon" @click="swich_menu()">&#9776;</div>
</div>
<div style="padding-top:1em; vertical-align: middle;">
  <div style="display:inline-block; float:left;" >
    <span style="font-size: 2em" ><b>{{ this.$router.currentRoute.meta.title }}</b></span>

    </div>
  <div style="display:inline-block; float:right; width:150px;">
    <span>
      <i class="el-icon-user"></i>{{ ($store.state.user != null ? $store.state.user.name : "-" ) }}
    </span>
    <el-progress
        :width="100"
        :percentage="sessionCountdownPercent"
        type="line"
        :color="sessionCountdownColors"
        title="Session timeout"
      ></el-progress>
  </div>
</div>
<div style="clear:both"></div>
<div style="padding-top:1em;">
  <router-view />
</div>

  
  </div>
</template>
<style>

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #333;
}

.topnav div {
  text-decoration: none;
  text-align: center;
  vertical-align: middle;
  position: relative;
  display: inline-block;
}

.topnav div.item {
  color: #f2f2f2;  
  padding-left: 5px;    
  cursor: pointer;
}

.topnav div:hover {  
  
}

.topnav div.logo {
    color: white;
}

.topnav .icon {
  display: none;
}

@media screen and (max-width: 600px) {
  /*.topnav a:not(:first-child) {display: none;}*/
  /*.topnav div:not(:first-child) {display: none;}*/
  .topnav div.item {display: none;}
  .topnav div.icon {
    float: right;
    display: block;
    cursor: pointer;
    color: #f2f2f2;
    height: 60px;
    font-size: 2em;    
  }
  .topnav.responsive {position: relative;}
  .topnav.responsive .icon {
    position: absolute;
    font-size: 2em;
    right: 0;
    top: 0;
  }
  .topnav.responsive div.item {
    display: block;
    text-align: left;
    padding-top: 0.5em;
    padding-bottom: 0.5em;
  }
}

.logo {
  width: 150px;
  height: 60px;
  cursor: pointer;
}

.topnav div.item a:link {
  color: rgb(54, 197, 59);
}

/* visited link */
.topnav div.item a:visited {
  color:  rgb(54, 197, 59);
}

/* mouse over link */
.topnav div.item a:hover {
  color: lightgreen;
}

/* selected link */
.topnav div.item a:active {
  color: rgb(54, 197, 59);
}

.topnav div.item a {
  color: rgb(54, 197, 59);
}

@import "~element-ui/lib/theme-chalk/index.css";
</style>
<script>



export default {
  name: "container",
  data() {
    return {
      menu_class:"topnav",
      isMenuActive: false,
      sessionInterval: null,
      sessionCountdownLimit: 600,
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
      (typeof window.UserData === "object" ? window.UserData : null)
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
      if (this.menu_class==="topnav") {
        this.menu_class = "topnav responsive";
      } else {
        this.menu_class = "topnav";
      }
    }
  }
};
</script>