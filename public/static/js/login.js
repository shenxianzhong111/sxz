/**
 * Created by Administrator on 2019/10/9.
 */

$(function () {
    var account=$('#account')
    var password=$('#password')
    var account_regular=/^(13|15|17|18|19)[\d]{9}$/
    var password_regular=/^[0-9a-zA-Z]{2,6}$/
    var test_regular=/^[0-9a-zA-Z]{5}$/
    account.keyup(function () {
        isD(account,account_regular)
    })
    password.keyup(function () {
        isD(password,password_regular)
    })

    account.focus(function () {
        account.parent().css("border","2px solid #00f");
        account.parent().css("box-shadow","0 0 50px rgba(0,0,255,0.5) ");
    })
    password.focus(function () {
        password.parent().css("border","2px solid #00f");
        password.parent ().css("box-shadow","0 0 50px rgba(0,0,255,0.5)");
    })
    account.blur(function () {
        if(!$(this).val() || !account_regular.test($(this).val())||!localStorage.getItem($(this).val())) {
            $('#avatar2').css('display','block')
            $('#avatar1').css('display','none')
        }else {
            $('#avatar1').attr('src',ul()+localStorage.getItem(account.val()))
            $('#avatar2').css('display','none')
            $('#avatar1').css('display','block')
        }

        account.parent().css("border","2px solid rgba(255,255,255,0.3)");
        account.parent().css("box-shadow","0 0 30px rgba(0,0,0,0.3)");
    })
    password.blur(function () {
        password.parent().css("border","2px solid rgba(255,255,255,0.3)");
       password.parent ().css("box-shadow","0 0 30px rgba(0,0,0,0.5)");
    })

    $('body').keyup(function () {
        if (event.keyCode === 13) {
            $("#login_button").click();
        }
    })
    $('#test').keyup(function () {
        isD($('#test'),test_regular)
    })
    function isD(input,regular) {
            var test_=regular.test(input.val())
            if(test_==false){
                // box.css("border","1px solid #f00")
                input.parent().css("border","2px solid #f00");
                input.parent().css("box-shadow","0 0 30px rgba(255,0,0,0.5)");
                return false
            }
            else {
                input.parent().css("border","2px solid #0f0")
                input.parent().css("box-shadow","0 0 30px rgba(0,255,0,0.5)");
                return true
            }
    }
    $('#login_button').click(function () {
        var account_=isD(account,account_regular)
        var password_=isD(password,password_regular)
        var test=isD($('#test'),test_regular)
            if(account_ && password_){
                $.ajax({
                    url:ul('admin','login','login'),
                    dataType:'json',
                    type:'post',
                    data:{account:account.val(),password:password.val(),test:$('#test').val()},
                    success:function (data) {

                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.open({
                                content:data.data,
                                time: 3000,
                                anim:2,
                                icon:0,
                                closeBtn: 0,
                                success:function () {
                                    $('.act-but').css("background","gray")
                                },
                                end:function () {
                                    $('.act-but').css("background","#0096e6")
                                }
                            })
                        });
                        switch(data.code) {
                            case 201:
                                localStorage.setItem(account.val(),data.photo);
                                window.location.href='admin/index/index'
                                break;
                            case 202:
                                localStorage.setItem(account.val(),data.photo);
                                window.location.href='author/index/index'
                                break;
                            case 203:
                                localStorage.setItem(account.val(),data.photo);
                                window.location.href='boss/index/index'
                                break;
                            case 105:
                                $('#test').parent().css("display","block")
                                break;
                            default:

                        }
                    }
                })
            }
    })
})
