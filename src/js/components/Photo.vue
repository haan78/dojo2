<template>
    <div class="container">
        <h2>{{ uye }}</h2>
        <el-upload :action="'index.php?a=photo&uye_id='+uye_id" :limit="1" :file-list="fileList" :on-success="sonuc" list-type="picture">
          
          <el-image  v-if="photo_!== null" fit="fill" :src="'assets/photos/'+this.photo_" ></el-image>
          <el-button v-else type="primary" icon="el-icon-camera" >Fotograf Yükle</el-button>
          <div slot="tip" class="el-upload__tip">Yüklemek istediğiniz fotoğrafı seçin</div>
        </el-upload>
        {{ photo_ }}
        <!--
        <el-image :fit="fit" :src="'assets/photos/'+this.photo_" ></el-image>
        -->
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
        this.photo_ = this.photo;
    },
    methods:{
        sonuc(response, file, fileList) {
            var rl = response.split(" ");
            this.photo_ = rl[1];
            self.$parent.sessionCountdown = self.$parent.sessionCountdownLimit;
        }
    }
}
</script>