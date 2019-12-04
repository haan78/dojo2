<template>
    <div>
        <el-date-picker v-model="tarihler" size="mini" :loading="loading"        
            format="yyyy-MM"
            type="monthrange"
            range-separator="-"
            start-placeholder="Başlangıç"
            end-placeholder="Bitiş"
            value-format="yyyy-MM" @change="load()"
        ></el-date-picker>

        <div class="panel">
            <h3>Aylık Toplanan Para</h3>
            <bar-graph title="Gelirler" :points="gelirler" :showValues="true" :labels="donemler" />
        </div>
        <div class="panel">
            <h3>Aylık Harcanan Para</h3>
            <bar-graph title="Giderler" :points="giderler" :showValues="true" :labels="donemler" />
        </div>
        <div class="panel">
            <h3>Aylık Katılım Ortalamaları</h3>
            <bar-graph title="Katılım Dursayıları" :points="katilim" :showValues="true" :labels="donemler" />
        </div>
        
        
        

    </div>
</template>
<script>
import BarGraph from "vue-svg-charts/src/components/bar.vue";
export default {
    data() {
        return {
            loading:false,
            tarihler:null,
            gelirler:[],
            giderler:[],
            katilim:[],
            donemler:[],

        }        
    },
    components:{ BarGraph },
    computed:{
        
    },
    created(){
        var d = new Date();
        var bas = (d.getFullYear()-1) + "-" + ( d.getMonth() < 9 ? "0"+(d.getMonth()+1) : d.getMonth()+1 );
        var bit = d.getFullYear() + "-" + ( d.getMonth() < 9 ? "0"+(d.getMonth()+1) : d.getMonth()+1 );
        this.tarihler=[bas,bit];

        this.load();
    },
    methods:{
        find(arr,donem) {
            for(var i=0; i<arr.length; i++) {
                if (arr[i].donem == donem) {
                    return arr[i].deger;
                }
            }
            return 0;
        },
        cizelge_hazirla(bas,bit,gel,git,kat) {
            var labels=[];
            var gelirler=[];
            var giderler=[];
            var katilim=[];
            if ( this.tarihler !== null ) {
                var bas_yil = parseInt(bas.split("-")[0]);
                var bas_ay = parseInt(bas.split("-")[1]);
                var bit_yil = parseInt(bit.split("-")[0]);
                var bit_ay = parseInt(bit.split("-")[1]);
                var i_yil = bas_yil;
                var i_ay = bas_ay;
                while( ( i_yil*12 )+i_ay <= (bit_yil*12)+bit_ay ) {
                    var label = i_yil + "-" + (i_ay > 9 ? i_ay : '0'+i_ay );
                    var ge = this.find(gel,label);
                    var gi = this.find(git,label);
                    var k = this.find(kat,label);

                    labels.push(label);
                    gelirler.push( ge );
                    giderler.push( gi );
                    katilim.push( k );
                    if ( i_ay === 12 ) {
                        i_ay = 1;
                        i_yil++;
                    } else {
                        i_ay++;
                    }                    
                }
            }
            return {
                donemler:labels,
                gelirler:gelirler,
                giderler:giderler,
                katilim:katilim
            };
        },        
        load() {
            let self = this;
            if ( self.tarihler!==null ) {
                self.WebMethod("genel_durum",[ self.tarihler[0],self.tarihler[1]  ],response=>{
                    var cizelge = self.cizelge_hazirla(self.tarihler[0],self.tarihler[1],response.result.gelirler,response.result.giderler,response.result.katilim);                    
                    self.gelirler = cizelge.gelirler;
                    self.giderler = cizelge.giderler;
                    self.katilim = cizelge.katilim;
                    self.donemler = cizelge.donemler;
                });    
            }            
        }
    }
}
</script>