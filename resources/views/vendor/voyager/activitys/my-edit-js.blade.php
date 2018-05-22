<script>

    // 限制周期和票数的最小值为1
    $(function(){
        $("[type='number']").attr('min', 1);
    });

    // 检查开始时间和结束时间的合法性
    $(".save").click(function () {
        var start = $("[name='start_at']").val();
        var end   = $("[name='end_at']").val();
        if(end < start) {
            alert("结束时间不能早于开始时间");
            return false;
        }
    });

</script>