<template>
    <div class="container">
        <div class="form-inline">
            <div class="item">
                <el-date-picker v-model="tarihler" size="mini" :loading="loading"
                :picker-options="{firstDayOfWeek:1}"
            format="dd.MM.yyyy"
            type="daterange"
      range-separator="-"
      start-placeholder="Başlangıç"
      end-placeholder="Bitiş"
            value-format="yyyy-MM-dd" @change="load()"></el-date-picker>
            </div>
        </div>
        <el-tabs v-model="tab" @tab-click="load()">
          <el-tab-pane label="Gelirler" name="gelirler">
              <el-table :data="gelirler.list">
                <el-table-column label="Tarih" >
            <template slot-scope="scope">
              {{ $date.toTurkish(scope.row.tarih) }}
                </template>
          </el-table-column>
                <el-table-column label="Üye" prop="uye"></el-table-column>
                <el-table-column label="Tutar" prop="tutar"></el-table-column>
                <el-table-column label="Tür" prop="tur"></el-table-column>
                <el-table-column label="Açıklama" prop="aciklama"></el-table-column>
              </el-table>
              <div class="form-inline">
                  <div class="item">
                      <el-select v-model="gelirler.page" @change="load()" :loading="loading" >
                <el-option v-for="p in gelirlerPageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
                </el-select>
                  </div>
                  <div class="item">Toplam Tutar:{{ gelirler.total }} TL</div>
              </div>
              
          </el-tab-pane>
          <el-tab-pane label="Giderler" name="giderler">
              <el-table :data="giderler.list">
                <el-table-column label="Tarih" >
            <template slot-scope="scope">
                <el-button icon="el-icon-camera" size="small" @click="belge(scope.row.belge)">
              {{ $date.toTurkish(scope.row.tarih) }}
              </el-button>
                </template>
          </el-table-column>
                <el-table-column label="Üye" prop="uye"></el-table-column>
                <el-table-column label="Tutar" prop="tutar"></el-table-column>
                <el-table-column label="Tür" prop="tur"></el-table-column>
                <el-table-column label="Açıklama" prop="aciklama"></el-table-column>
              </el-table>
              <div class="form-inline">
                  <div class="item">
                      <el-select v-model="giderler.page" @change="load()" :loading="loading" >
                <el-option v-for="p in giderlerPageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
            </el-select>
                  </div>
                  <div class="item">Toplam Tutar:{{ giderler.total }} TL</div>
              </div>            
          </el-tab-pane>
        </el-tabs>
    </div>
</template>
<script>
export default {
    data() {
        return {
            tarihler:null,
            tab:"gelirler",
            loading:false,
            gelirler:{
                page:1,
                maxrow:0,
                limit:20,
                total:0,
                list:[]
            },
            giderler:{
                page:1,
                maxrow:0,
                limit:20,
                total:0,
                list:[]
            }
        }
    },
    created() {
    
        var bas = new Date();        
        bas.setMonth(bas.getMonth() - 1);
        var bit = new Date();

        this.tarihler=[
            this.$date.toISO(bas),
            this.$date.toISO(bit)
        ];
        this.load();    
    },
    computed:{
        gelirlerPageCount() {
            return Math.ceil(this.gelirler.maxrow / this.gelirler.limit);
        },
        giderlerPageCount() {
            return Math.ceil(this.giderler.maxrow / this.giderler.limit);
        }
    },
    methods:{
        load() {
            /*this.gelirler.page = 1;
            this.gelirler.list = [];
            this.giderler.page = 1;
            this.giderler.list = [];*/
            if (this.tarihler !== null) {
                if (this.tab === "gelirler") {
                    this.load_gelirler();
                } else if ( this.tab === "giderler" ) {
                    this.load_giderler();
                }
            }
        },
        load_gelirler() {
            let self = this;
            self.WebMethod("gelirler",[ self.tarihler[0],self.tarihler[1],((self.gelirler.page - 1) * self.gelirler.limit),self.gelirler.limit ],response=>{
                self.gelirler.list = response.result.list;
                self.gelirler.maxrow = response.result.maxrow;
                self.gelirler.total = response.result.total;
            });
        },
        load_giderler() {
            let self = this;
            self.WebMethod("giderler",[ self.tarihler[0],self.tarihler[1],((self.giderler.page - 1) * self.gelirler.limit),self.giderler.limit ],response=>{
                self.giderler.list = response.result.list;
                self.giderler.maxrow = response.result.maxrow;
                self.giderler.total = response.result.total;
            });
        },
        belge(belge) {
            window.open("index.php?a=img_belge&file="+belge,"_blank");
        }
    }


}
</script>