<template>
    <div>
        <el-table :data="list" stripe style="width: 100%; height:100%;">
          <el-table-column label="" fixed="left">
            <template slot-scope="scope">
                <div class="uye">
                    <img :src="( scope.row.photo !== null ? 'uploads/photos/'+scope.row.photo : 'assets/img/kendoka.jpg' )" /><br/>
                {{ scope.row.uye }}
                </div>
                
            </template>
          </el-table-column>
          <el-table-column label="Seviye" prop="seviye"></el-table-column>
          <el-table-column label="Doğum Tarihi">
            <template slot-scope="scope"><div class="tarih">{{ $date.toTurkish(scope.row.dogum_tarihi) }}</div></template>
          </el-table-column>
          <el-table-column label="Cinsiyet" prop="cinsiyet"></el-table-column>
          <el-table-column label="EKF" prop="ekf_no"></el-table-column>
          <el-table-column label="İlk Keiko">
            <template slot-scope="scope"><div class="tarih">{{ $date.toTurkish(scope.row.ilk_keiko) }}</div></template>
          </el-table-column>
          <el-table-column label="Son Keiko">              
            <template slot-scope="scope"><div class="tarih">{{ $date.toTurkish(scope.row.son_keiko) }}</div></template>
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
            loading:false,
            list:[]
        }
    },
    created() {
        this.load()
    },
    methods:{
        load() {
            let self = this;
            self.WebMethod("genel_uye_raporu",[],response=>{
                self.list = response.result;
            });
        }
    }
}
</script>