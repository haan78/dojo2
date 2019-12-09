<template>
    <div class="container">
        <h2>{{ uye }}</h2>
        <el-upload :action="'index.php?a=photo&uye_id='+uye_id" :limit="1" :file-list="fileList" :on-success="sonuc" list-type="picture">
          
          <el-image  fit="fill" :src="getPhoto()" ></el-image>
          <div slot="tip" class="el-upload__tip">Yüklemek istediğiniz fotoğrafı seçin</div>
        </el-upload>
        {{ photo_ }}

    </div>
</template>

<script>
export default {
    props:[ "uye_id","photo","uye" ],
    data() {
        return {
            photo_:null,
            fileList:[]
        }
    },
    created() {
        this.photo_ = (this.photo === 'null' ? null : this.photo );
    },
    methods:{
        sonuc(response, file, fileList) {
            var rl = response.split(" ");
            if ( rl[0] === "success" ) {
                this.photo_ = rl[1];
            } else {
                this.$message.error( ($rl[1] ? $rl[1] : 'Blinmeyen hata' ) );
            }
            
            this.$parent.sessionCountdown = this.$parent.sessionCountdownLimit;
        },
        getPhoto() {
            var p = this.photo_ === null ? 'assets/img/kendoka.jpg' : 'index.php?a=img_uye&file='+this.photo_;
            return p;
        }
    }
}
</script>