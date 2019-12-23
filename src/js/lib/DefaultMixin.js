import MobileDetect from 'mobile-detect';

export default {
    data() {
        return {
            isMobile:( (new MobileDetect(window.navigator.userAgent)).mobile() !== null ),             
        }        
    },    
    methods:{
        sessionEnd() {
            window.location.href = "index.php?a=logout";
        },
        confirm(title,text,ok_cb,cancel_cb) {
            this.$confirm(text, title, {
                confirmButtonText: 'Evet',
                cancelButtonText: 'Hayır',
                type: 'warning'
              }).then(() => {
                if ( typeof ok_cb === "function" ) {
                    ok_cb();
                }
              }).catch(() => {
                if ( typeof cancel_cb === "function" ) {
                    cancel_cb();
                }          
              });
        },
        link(path) {            
            if (this.$router) {
                if (this.$router.currentRoute.path != path ) {
                    this.$router.push(path);
                }
            }            
        },
        setLoading(value) {
            let self = this;
            if ( typeof self.$parent.loading === "boolean" ) {
                self.$parent.loading = value;
            }
            if ( typeof self.loading === "boolean" ) {
                self.loading = value;
            }
        },
        defaultError(type,message) {
            this.$message.error(message);
        },
        WebMethod(method,data,onSuccess,onError) {
            let self = this;
            var err = ( onError ? onError : self.defaultError );
            self.setLoading(true);
            self.$http.post("index.php/"+method+"?a=ajax",(data ? data : null )).then( (response)=>{
                self.$parent.sessionCountdown = self.$parent.sessionCountdownLimit;
                self.setLoading(false);
                if ( typeof response.data === "object" ) {
                    if ( response.data.success ) {
                        if ( typeof onSuccess === "function" ) {
                            onSuccess( response.data );
                        }
                    } else {
                        if ( response.data.text === "LOGOUT" ) {
                            window.location.reload(true);
                        } else if ( typeof err === "function" ) {
                            err( "Application",response.data.text );
                        }
                    }
                } else {
                    err( "Server",response.data );
                }                
            }).catch((error)=>{
                self.$parent.sessionCountdown = self.$parent.sessionCountdownLimit;
                self.setLoading(false);
                if ( typeof err === "function" ) {
                    err( "Network",error );
                }
            });
        }
    }
}