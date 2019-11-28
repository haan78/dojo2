<template>
    <div class="container">
        <div class="list" >
            <div class="item" >
                <el-button :loading="loading" style="width:100%" type="success" icon="el-icon-plus" @click="link('/yoklama/'+$date.toISO( new Date() ) )" >Yeni Yooklama</el-button>
            </div>
            <div v-for="(y,iy) in list" :key="iy" class="item" >
                <el-button :loading="loading" style="width:100%" type="info" icon="el-icon-edit" @click="link('/yoklama/'+y.tarih)" >{{ $date.toTurkish(y.tarih) }}&nbsp;({{y.sayi}})</el-button>
            </div>
        </div>
        <el-select v-model="page" @change="load()" :loading="loading" >
      <el-option v-for="p in pageCount" :key="p" :value="p" :label="'Sayfa ' + p" ></el-option>
    </el-select>
    </div>
</template>
<script>
export default {
    data() {
        return {
            loading:false,
            page:1,
            limit:16,
            maxrow:0,
            list:[]
        }
    },
    created() {
        this.load();
    },
    computed:{
        pageCount() {
            return Math.ceil(this.maxrow / this.limit);
        }
    },
    methods:{
        load() {
            let self = this;
            self.WebMethod("yoklamalar",[null,null,((self.page - 1) * self.limit), self.limit], response=>{
                self.maxrow = response.outputs.maxrow;
                self.list = response.result;
            });
        }
    }
}
</script>