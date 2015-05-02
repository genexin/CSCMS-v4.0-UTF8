$(document).ready(function(){
            //返回上一页
            $("#pageback").click( function () { 
                history.back();
            });
            $("#topmenu_sub").click( function () { 
                $("#topmenu").slideToggle("fast");
            });
            $("#clearkey").click( function () {
                $("#username").val('');
            });
            $('.codes').click(function(){
                var codelink=$(this).attr('_src');
                $('.codes').attr('src', codelink+"?" + Math.random());
            });
            $(".user_login_but").click(function(){
                $("#login_form").submit();
            });
});
