<template>
    <div class="container" >
        <el-table :data="list">
          <el-table-column label="Ödeme Türü" prop="odeme_tur"></el-table-column>
          <el-table-column label="Tutar" prop="tutar" width="60"></el-table-column>

          <el-table-column
      fixed="right"
      label="">
      <template slot-scope="scope">
        <el-button @click="btnEdit(scope.$index, scope.row)" >Düzelt</el-button>
      </template>
    </el-table-column>

        </el-table>

        <el-dialog
  title="Ödeme Türü Düzenle"
  :visible.sync="dialogVisible"
  >
  
  <el-form :model="detail">
    <el-form-item :label="detail.odeme_tur" prop="tutar">
        <el-input-number v-model="detail.tutar" :step="5" size="small" :precision="2"></el-input-number>
    </el-form-item>
  </el-form>
  
  <span slot="footer" class="dialog-footer">
    <el-button type="primary" @click="save()">Kaydet</el-button>
  </span>
</el-dialog>

    </div>
</template>
<script>
export default {
    data() {
        return {
            dialogVisible:false,
            loading:false,
            list:[],
            detail:{
                odeme_tur_id:null,
                odeme_tur:null,
                tutar:null
            }
        }
    },
    mounted() {
        console.log( this.isMobile );
    },
    methods:{
        load() {
            let self = this;
            self.WebMethod("odeme_turleri",[],(response)=>{
                self.list = response.result;
            });
        },
        btnEdit(ind,row) {
            this.detail.odeme_tur_id = row.odeme_tur_id;
            this.detail.odeme_tur = row.odeme_tur;
            this.detail.tutar = row.tutar;
            this.dialogVisible = true;
        },
        save() {
            let self = this;
            if ( self.detail.tutar >= 0 )  {
                self.WebMethod("odeme_tur",[ self.detail.odeme_tur,self.detail.tutar,self.detail.odeme_tur_id ],(response)=>{
                    self.dialogVisible = false;
                    self.load();
                });
            } else {
                self.$message.error("Tutar mantıklı değil");
            }
        }

    },
    created() {
        this.load();
    }
}
</script>