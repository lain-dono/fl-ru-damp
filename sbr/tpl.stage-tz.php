<?php $size_ln_descr = substr_count(reformat($stage->data['descr'], 70, 0, 0, 1), '<br') + 1 + ($stage->data['attach'] ? (count($stage->data['attach']) + 2) : 0); // Количество переносов строк?>
<div>
<table class="b-layout__table b-layout__table_width_full" cellpadding="0" cellspacing="0" border="0">
    <tr class="b-layout__tr">
        <td class="b-layout__left b-layout__left_width_72ps">
            <div class="b-post">
                <h2 class="b-post__title b-post__title_padbot_15">
                    
                    <?php if ($stage->isNewVersionTZ() && ($stage_changed || $stage_changed_for_frl)) {
    ?>
                    <span class="sbr-tz">Новое техническое задание от <?= date('d.m.Y', $stage->data['date_version_tz'][1])?> &#160;&#160;&#160;&#160;&#160;</span>
                    <span class="sbr-old-tz b-post__txt_hide">Техническое задание от <?= date('d.m.Y', $stage->data['date_version_tz'][0])?> &#160;&#160;&#160;&#160;&#160;</span>
                    <span class="b-post__txt"><a class="b-post__link b-post__link_dot_0f71c8" id="toggle-tz-link" href="javascript:void(0)" onclick="toggle_tz();">Посмотреть старое</a></span>
                    <?php 
} else {//if?>
                    Техническое задание
                    <?php 
}?>
                </h2>
                <div class="b-post__body b-post__body_relative b-post__body_overflow_hidden <?= $size_ln_descr > 5 && !($stage->isNewVersionTZ() && ($stage_changed || $stage_changed_for_frl)) ? 'b-post__body_height_100' : ''?>">
                    <div class="b-post__txt <?= $stage->data['attach'] ? 'b-post__txt_padbot_15' : ''; ?> b-post__txt_fontsize_15 sbr-tz">
                        <?php if ($stage->status == sbr_stages::STATUS_INARBITRAGE || $stage->status == sbr_stages::STATUS_ARBITRAGED) {
    $attached = $frl_version['attach'];
    ?>
                            <?= reformat($frl_version['descr'], 70, 0, 0, 1);
    ?>
                        <?php 
} else {
    $attached = $stage->data['attach']; //if?>
                            <?= reformat($stage->data['descr'], 70, 0, 0, 1)?>
                        <?php 
}//if?>
                    </div>
                    <?php if ($attached) {
    ?>
                    <span id="new_attach">
                        <div class="b-post__txt b-post__txt_padbot_10 b-post__txt_fontsize_15 b-post__txt_bold">Вложения</div>
                        <table class="b-layout__table" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                                <?php foreach ($attached as $id => $a) {
    if ($a['is_deleted'] === 't' && ($stage->status == sbr_stages::STATUS_INARBITRAGE || $stage->status == sbr_stages::STATUS_ARBITRAGED)) {
        continue;
    }
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                                <tr class="b-layout__tr">
                                    <td class="b-layout__middle b-layout__middle_padbot_5">
                                        <div class="b-layout__txt">
                                            <i class="b-icon b-icon_attach_<?= $aData['class_ico'] === 'unknown' ? 'unknown' : $a['ftype']?>"></i> 
                                            <a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" class="b-layout__link" target="_blank"><?= reformat($a['orig_name'] ? $a['orig_name'] : $aData['orig_name'], 30)?></a>, <?= ConvertBtoMB($a['size'])?>
                                        </div>
                                    </td>
                                    <td class="b-layout__right b-layout__right_padleft_20 b-layout__right_padbot_5">
                                        <div class="b-layout__txt"><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" class="b-layout__link" target="_blank">Скачать</a></div>
                                    </td>
                                </tr>
                                <?php 
}//foreach?>
                            </tbody>
                        </table>
                    </span>
                    <?php 
} ?>
                    
                    <?php if ($stage->isNewVersionTZ() && ($stage_changed || $stage_changed_for_frl)) {
    ?>
                    <div class="b-post__txt b-post__txt_fontsize_15 <?= $stage->v_data['attach'] ? 'b-post__txt_padbot_15' : '';
    ?> b-post__txt_hide sbr-old-tz">
                        <?= reformat($stage->v_data['descr'], 70, 0, 0, 1)?>
                    </div>
                        <?php if ($stage->v_data['attach']) {
    ?>
                        <span id="old_attach" style="display:none">
                            <div class="b-post__txt b-post__txt_padbot_10 b-post__txt_fontsize_15 b-post__txt_bold">Вложения</div>
                            <table class="b-layout__table" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <?php foreach ($stage->v_data['attach'] as $id => $a) {
    if ($a['is_deleted'] === 't') {
        continue;
    }
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                                    <tr class="b-layout__tr">
                                        <td class="b-layout__middle b-layout__middle_padbot_5">
                                            <div class="b-layout__txt">
                                                <i class="b-icon b-icon_attach_<?= $aData['class_ico'] === 'unknown' ? 'unknown' : $a['ftype']?>"></i> 
                                                <a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" class="b-layout__link" target="_blank"><?= reformat($a['orig_name'] ? $a['orig_name'] : $aData['orig_name'], 30)?></a>, <?= ConvertBtoMB($a['size'])?>
                                            </div>
                                        </td>
                                        <td class="b-layout__right b-layout__right_padleft_20 b-layout__right_padbot_5">
                                            <div class="b-layout__txt"><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" class="b-layout__link" target="_blank">Скачать</a></div>
                                        </td>
                                    </tr>
                                    <?php 
}//foreach?>
                                </tbody>
                            </table>
                        </span>
                        <?php 
}//if?>
                    <?php 
} //if?>
                    <?php if ($size_ln_descr > 5 && !($stage->isNewVersionTZ() && ($stage_changed || $stage_changed_for_frl))) {
    ?>
                    <div class="b-post__weaken"></div>
                    <?php 
} //if?>
                </div>
                <?php if ($size_ln_descr > 5 && !($stage->isNewVersionTZ() && ($stage_changed || $stage_changed_for_frl))) {
    ?>
                <div class="b-post__txt b-post__txt_padtop_20"><a id="toggler-tz" class="b-post__link b-post__link_dot_0f71c8" href="javascript:void(0)">Развернуть задание</a></div>
                <?php 
}//if?>
            </div>
        </td>
        <td class="b-layout__right"></td>
    </tr>
</table>
</div>