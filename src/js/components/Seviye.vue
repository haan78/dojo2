<template>
  <div class="container">
    <h2>{{ uye }}</h2>
    <el-table :data="list">
      <el-table-column label="Tarih">
        <template slot-scope="scope">{{ $date.toTurkish(scope.row.tarih) }}</template>
      </el-table-column>
      <el-table-column label="Seviye" prop="tanim"></el-table-column>
      <el-table-column label="Detaylar">
        <template slot-scope="scope">
          <el-button size="small" type="primary" @click="open(scope.row)">Detaylar</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-button type="success" @click="open({})">Sınav Ekle</el-button>
    <el-dialog :title="uye" :visible.sync="dialogVisible" v-loading="loading" width="80%">
      <el-form :model="seviye">
        <el-form-item label="Sınav Tarihi">
          <el-date-picker
            v-model="seviye.tarih"
            :picker-options="{firstDayOfWeek:1}"
            format="dd.MM.yyyy"
            style="width:100%"
            value-format="yyyy-MM-dd"
            placeholder="Tahsilat Tarihi"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="Kazanılan Seviye">
          <el-select v-model="seviye.tanim">
            <el-option value="6 KYU">6 KYU</el-option>
            <el-option value="5 KYU">5 KYU</el-option>
            <el-option value="4 KYU">4 KYU</el-option>
            <el-option value="3 KYU">3 KYU</el-option>
            <el-option value="2 KYU">2 KYU</el-option>
            <el-option value="1 KYU">1 KYU</el-option>
            <el-option value="1 DAN">1 DAN</el-option>
            <el-option value="2 DAN">2 DAN</el-option>
            <el-option value="3 DAN">3 DAN</el-option>
            <el-option value="4 DAN">4 DAN</el-option>
            <el-option value="5 DAN">5 DAN</el-option>
            <el-option value="6 DAN">6 DAN</el-option>
            <el-option value="7 DAN">7 DAN</el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Açıklama">
          <el-input v-model="seviye.detaylar" placeholder="Açıklamalar"></el-input>
        </el-form-item>
      </el-form>
      <div class="form-inline">
        <div class="item" v-show="seviye.seviye_id">
          <el-button type="danger" style="width:100%" icon="el-icon-delete" @click="del()">Sil</el-button>
        </div>
        <div class="item">
          <el-button
            type="success"
            style="width:100%"
            icon="el-icon-success"
            @click="save()"
          >Tahsilat</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loading: false,
      dialogVisible: false,
      seviye: {
        seviye_id: null,
        tarih: null,
        tanim: null,
        detaylar: null
      },
      list: []
    };
  },
  props: ["uye_id", "uye"],
  created() {
    this.load();
  },
  methods: {
    load() {
      let self = this;
      self.WebMethod("uyenin_sinavlari", [self.uye_id], response => {
        self.list = response.result;
      });
    },
    open(obj) {
      this.dialogVisible = true;
      var defaults = obj === null ? {} : obj;
      for (var k in this.seviye) {
        if (typeof defaults[k] !== "undefined") {
          this.seviye[k] = defaults[k];
        } else {
          this.seviye[k] = null;
        }
      }
    },
    save() {},
    del() {}
  }
};
</script>