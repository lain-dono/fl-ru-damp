<?php
if (!defined('IN_STDF')) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
/* deprecated: убить, когда уйдут сделки с налогом на прибыль */ ?>
<div class="form form-rez">
    <b class="b1"></b>
    <b class="b2"></b>
    <div class="form-in">
        <div class="form-block first last">
            <div class="form-el">
                <label class="form-l"><strong>Справка о резидентстве:</strong></label>
                <div class="form-value">
                    <?php if ($reqvs['rezdoc_status'] == sbr::RS_WAITING) {
    ?>
                      <span>ожидается</span>
                    <?php 
} else {
    if ($reqvs['rezdoc_status'] == sbr::RS_DENIED) {
        ?>
                      <span class="form-merr">аннулирована</span>
                    <?php 
    } else {
        if ($reqvs['rezdoc_status'] == sbr::RS_ACCEPTED) {
            ?>
                      <span class="form-mvalid">получена</span>
                    <?php 
        } else {
            ?>
                      <span>нет данных</span>
                    <?php 
        }
    }
} ?>
                    <span id="rezdoc_comment_out"><?=($reqvs['rezdoc_comment'] ? '('.reformat($reqvs['rezdoc_comment'], 40, 0, 1).')' : '')?></span>
                    <?php if (hasPermissions('users')) {
    ?>
                        <?php if ($reqvs['rezdoc_status']) {
    ?>
                          <a href="javascript:;" class="lnk-dot-blue" onclick="$('rezdoc_comment').innerHTML=$('rezdoc_comment_out').innerHTML.replace(/^\(/,'').replace(/\)$/,'');SBR.rezDocOpenWin()">изменить комментарий</a>
                        <?php 
}
    ?>
                        <br /><br />
                        <div>
                            <input type="button" onclick="SBR.rezDocOpenWin(<?=sbr::RS_WAITING?>)" <?=$reqvs['rezdoc_status'] == sbr::RS_WAITING ? ' disabled="disabled"' : ''?> value="Справка ожидается" />
                            <input type="button" onclick="SBR.rezDocOpenWin(<?=sbr::RS_ACCEPTED?>)" <?=$reqvs['rezdoc_status'] == sbr::RS_ACCEPTED ? ' disabled="disabled"' : ''?> value="Справка принята" />
                            <input type="button" onclick="SBR.rezDocOpenWin(<?=sbr::RS_DENIED?>)" <?=$reqvs['rezdoc_status'] == sbr::RS_DENIED ? ' disabled="disabled"' : ''?> value="Справка отклонена" />
                        </div>
                    <?php 
} ?>
                </div>
            </div>
        </div>
    </div>
    <b class="b2"></b>
    <b class="b1"></b>
</div>
