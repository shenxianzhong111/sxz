$(function () {
    // console.log(f.user)

    var login = new Vue({
        el:'#login',
        data:{
            user:{
                data:'',
                tishi:'',
                color:{
                    redColor:false,
                    greenColor:true
                }
            },
            pwd:{
                data:'',
                tishi:'',
                color:{
                    redColor:true,
                    greenColor:false
                }
            },
            yzm:''
        },watch:{
                'user.data':function (val) {
                if(this.isuser(val)){
                    this.user.tishi='账号正确'
                    this.user.color.redColor=false
                }else{
                    this.user.tishi='账号错误'
                    this.user.color.redColor=true
                }
            },
            'user.color.redColor':function () {
                this.user.color.greenColor=!this.user.color.redColor;
            },
            'pwd.data':function (val) {
                if(this.ispwd(val)){
                    this.pwd.tishi='密码正确'
                    this.pwd.color.redColor=false
                }else{
                    this.pwd.tishi='密码错误'
                    this.pwd.color.redColor=true
                }
            },'pwd.color.redColor':function () {
                this.pwd.color.greenColor=!this.pwd.color.redColor;
            },
        },methods:{
                tijiao:function () {
                   if (!(this.ispwd(this.user.data) && this.ispwd(this.pwd.data))) return false;
                   $.ajax({
                       url:'http://127.0.0.1/tp/public/index.php/index/index/login',
                       data:{user:this.user.data,pwd:this.pwd.data,yzm:this.yzm},
                       type:'post',
                       success:function (data) {
                           alert(data.type)
                       }
                   })
                },
                isuser:function (data) {
                    if(/^[0-9]{3,6}$/.test(data)){
                        return true;
                    }
                    return false;
                },
                ispwd:function (data) {
                    if(/^[0-9]{3,6}$/.test(data)){
                        return true;
                    }
                    return false;
                }
        }

    })
})