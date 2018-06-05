@php
    $statusInfo = array('ing-color' => '#2ecc71',
                     'ing-msg' => '进行中',
                     'feature-color' => '#22A7F0',
                     'feature-msg' => '未开始',
                     'end-color' => '#FA2A00',
                     'end-msg' => '已结束',
                     'illegal-color' => '#f39c12',
                     'illegal-msg' => '非法的活动时间');

    if(strtotime($end_at) - strtotime($start_at) < 0) $status = 'illegal';
    elseif(time() < strtotime($start_at)) {
        $status = 'feature';
        $day = floor((strtotime($start_at) - time()) / 86400);
        $hour = floor(((strtotime($start_at) - time()) % 86400) / 3600);
        $min = floor(((strtotime($start_at) - time()) % 3600) / 60);
        $time = '距离开始'.$day.'天'.$hour.'时'.$min.'分';
    }
    elseif(time() >= strtotime($start_at) && time() <= strtotime($end_at)) {
        $status = 'ing';
        $day = floor((strtotime($end_at) - time()) / 86400);
        $hour = floor(((strtotime($end_at) - time()) % 86400) / 3600);
        $min = floor(((strtotime($end_at) - time()) % 3600) / 60);
        $time = '剩余'.$day.'天'.$hour.'时'.$min.'分';
    }
    elseif(time() > strtotime($end_at)) $status = 'end';

@endphp

<td style="color: @php echo $statusInfo[$status.'-color'] @endphp;font-size: 20px">
    <i style="position: relative;top: 3px" class="voyager-activity"></i> <span>@php echo $statusInfo[$status.'-msg'] @endphp</span>
    <br>
    @php if(isset($time)) echo "<span>$time</span>"; @endphp
</td>