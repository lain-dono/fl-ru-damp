<?php

/**
 * Временный шаблон сообщения об успешной оплате ПРО
 * для нового попапа оплаты quickPaymentPopupPro.
 */
$quickpro_ok_default = 'quickpro_ok';

if (!isset($_SESSION['quickbuypro_success_opcode'])) {
    $_GET[$quickpro_ok_default] = false;
    //фиксируем URL проекта для последующего возврата после оплаты ПРО
    $_SESSION['quickbuypro_redirect'] = urldecode($_SESSION['ref_uri']);
}

?>
<div id="quick_pro_win_main_ok" class="b-shadow b-shadow_center b-shadow_width_520 b-shadow_zindex_11 b-shadow_bg_eeffe5 b-shadow__quick <?= $_GET[$quickpro_ok_default] ? '' : 'b-shadow_hide' ?>" style="display:block;">
    <div class="b-shadow__body b-shadow__body_pad_15_20">
        <div class="b-fon b-fon_bg_fpro">
            <div class="b-layout__title b-layout__title_padbot_5">
                <span class="b-icon b-page__desktop b-page__ipad <?php if ($quickPRO_type == 'profi') {
    ?>b-icon__profi<?php 
} else {
    ?>b-icon__spro b-icon__spro_<?=is_emp() ? 'e' : 'f'?> <?php 
} ?> b-icon_float_left b-icon_margtop_4 b-icon_margright_10"></span>
                <?php if (isset($quickpro_ok_title)): ?>
                    <?= $quickpro_ok_title ?>
                <?php else: ?>
                Вы успешно купили аккаунт PRO 
                <?php endif; ?>
                <div class="b-layout__txt b-layout__txt_fontsize_11 b-layout__txt_lineheight_1 b-page__desktop b-page__ipad">
                    <?php if (isset($quickpro_ok_subtitle)): ?>
                        <?= $quickpro_ok_subtitle ?>
                    <?php else: ?>
                        <?= is_emp() ? 'с выгодными скидками до 50% на дополнительные сервисы в проектах' : 'с неограниченными ответами в проектах и +20% к рейтингу'?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    
		<?php
        $pro_last = false;
        if ($_SESSION['freeze_from'] && $_SESSION['is_freezed']) {
            $pro_last = $_SESSION['payed_to'];
        } else {
            if ($_SESSION['pro_last']) {
                $pro_last = $_SESSION['pro_last'];
            }
        }
        ?>
        <div class="b-layout__txt b-layout__txt_padbot_15">Срок действия аккаунта — <span class="b-layout__txt b-layout__txt_color_<?= is_emp() ? '6db335' : 'fd6c30'?>">до <?= date('d.m.Y H:i', strtotime($pro_last)) ?></span></div>
        <div class="b-layout__txt b-layout__txt_padbot_20 b-layout__txt_fontsize_11">
            <?php if (is_emp()) {
    ?>
            Спасибо за покупку. <br>
            Желаем вам успешной работы на сайте и хороших исполнителей!
            <?php 
} else {
    ?>
                <?php if ($quickPRO_type == 'project') {
    ?>
                    <?php if ($project['is_blocked'] == 't' || $project['closed'] == 't') {
    ?>
                    К сожалению, выбранный вами проект уже закрыт, но вы можете посмотреть и ответить на другие проекты аналогичной тематики.<br><br>
                    Спасибо за покупку.<br>
                    Желаем вам успешной работы на сайте и множества выгодных заказов!
                    <?php 
} else {
    ?>
                        Спасибо за покупку, теперь вы можете ответить на проект. <br>
                        Желаем вам успешной работы на сайте и множества выгодных заказов!
                    <?php 
}
    ?>
                <?php 
} else {
    ?>
                    Спасибо за покупку. <br>
                    Желаем вам успешной работы на сайте и множества выгодных заказов!
                <?php 
}
    ?>
            <?php 
} ?>
        </div>
        <div class="b-buttons b-buttons_padbot_10"> 
            <?php if (is_emp()) {
    ?>
                <a class="b-button b-button_flat b-button_flat_green" href="#" onClick="$('quick_pro_win_main_ok').addClass('b-shadow_hide'); return false;">Закрыть</a> 
            <?php 
} else {
    ?>
                <?php if ($quickPRO_type == 'project') {
    ?>
                    <?php if ($project['is_blocked'] == 't' || $project['closed'] == 't') {
    ?>
                        <a class="b-button b-button_flat b-button_flat_green" href="/">Закрыть и посмотреть проекты</a> 
                    <?php 
} else {
    ?>
                        <a class="b-button b-button_flat b-button_flat_green" href="#" onClick="$('quick_pro_win_main_ok').addClass('b-shadow_hide'); window.location.hash = '#new_offer'; return false;">Закрыть и ответить на проект</a> 
                    <?php 
}
    ?>
                <?php 
} else {
    ?>
                    <a class="b-button b-button_flat b-button_flat_green" href="#" onClick="$('quick_pro_win_main_ok').addClass('b-shadow_hide'); return false;">Закрыть</a> 
                <?php 
}
    ?>
            <?php 
} ?>
        </div>
    </div>
    <span class="b-shadow__icon b-shadow__icon_close"></span>
</div>

<script type="text/javascript">
    <?php if ($_GET[$quickpro_ok_default]): ?>
    window.addEvent('load', function() {
        yaCounter6051055.reachGoal('<?= is_emp() ? 'r' : 'f'?>pro_bill_win');
        yaCounter6051055.reachGoal('buy_<?=is_emp() ? 'r' : 'f'?>pro_<?=$_SESSION['quickbuypro_success_opcode']?>');
        <?php unset($_SESSION['quickbuypro_success_opcode']); ?>
    });
    <?php endif; ?>
</script>