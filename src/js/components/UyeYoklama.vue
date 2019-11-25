<template>
    <div class="container">
        <h2>{{ uye }}</h2>
        <el-table :data="list">
            <el-table-column label="Detaylar">
                <template slot-scope="scope">
                    <el-button size="small" type="primary" @click="open(scope.row)">Detaylar</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            loading:true,
            dialogVisible:true,
            list:[],
            tarih:null
        }
    },
    props:["uye_id","uye"],
    methods:{
        load() {

        },
        save() {

        },
        del() {

        },
        open() {
            
        }
    }
}
</script>