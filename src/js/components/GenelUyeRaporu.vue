<template>
  <div>
     <!-- {{ window.width }} - {{ window.height }} - {{ height }} -->
    <el-input v-model="search" placeholder="Arama" prefix-icon="el-icon-search">
      <el-button slot="append" icon="el-icon-refresh" title="Boşalt" @click="refresh()"></el-button>
    </el-input>
    <el-table :data="uyeler" stripe style="width: 100%;" :height="height">
      <el-table-column label fixed="left">
        <template slot-scope="scope">
          <div class="uye">
            <img
              :src="( scope.row.photo !== null ? 'index.php?a=img_uye&file='+scope.row.photo : 'assets/img/kendoka.jpg' )"
            />
            <br />
            {{ scope.row.uye }}
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Seviye" prop="seviye"></el-table-column>
      <el-table-column label="Doğum Tarihi">
        <template slot-scope="scope">
          <div class="tarih">{{ $date.toTurkish(scope.row.dogum_tarihi) }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Cinsiyet" prop="cinsiyet"></el-table-column>
      <el-table-column label="EKF" prop="ekf_no"></el-table-column>
      <el-table-column label="İlk Keiko">
        <template slot-scope="scope">
          <div class="tarih">{{ $date.toTurkish(scope.row.ilk_keiko) }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Son Keiko">
        <template slot-scope="scope">
          <div class="tarih">{{ $date.toTurkish(scope.row.son_keiko) }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Aidat Eksiği" prop="aidat_eksigi"></el-table-column>
      <el-table-column label="Üç Aylık Devam" prop="uc_ayi_icinde_devam_sayisi"></el-table-column>
      <el-table-column label="Üç Aylık Ort." prop="uc_aylik_devam_yuzdesi"></el-table-column>
    </el-table>
  </div>
</template>
<style>
.uye {
  text-align: center;
}

.uye img {
  width: 4em;
  height: 6em;
  vertical-align: middle;
}

.tarih {
  white-space: nowrap;
}
</style>
<script>
export default {
  data() {
    return {
      search: null,
      loading: false,
      list: [],
      height:0,
      window: {
        width: 0,
        height: 0
      }
    };
  },
  computed:{

  },
  mounted() {
    window.addEventListener("resize", this.handleResize);
    this.handleResize();
  },
  destroyed() {
    window.removeEventListener("resize", this.handleResize);
  },
  created() {
      this.height = window.innerHeight - 140;
      this.load();
  },
  computed: {
    uyeler() {
      let self = this;
      return self.list.filter(item => {
        var str = (self.search === null ? "" : self.search).toLowerCase();
        return item.uye.toLowerCase().includes(str);
      });
    }
  },
  methods: {
    refresh() {
      this.search = null;
    },
    load() {
      let self = this;
      self.WebMethod("genel_uye_raporu", [], response => {
        self.list = response.result;
      });
    },
    handleResize() {
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight;
      this.height = this.window.height - 140;
    }
  }
};
</script>