<script type="text/javascript">
var SBR = new Sbr();
<?php if ($sbr->isAdmin()) {
    ?>
window.addEvent('domready', function() { SBR = new Sbr('arbitragedFrm'); } );
Sbr.prototype.ERRORS=<?=sbr_meta::jsInputErrors($stage->error['arbitrage'])?>;
Sbr.prototype.ARB_REASONS={<?php
    $i = 0;
    foreach (sbr_stages::getArbReasons($stage) as $p => $r) {
        echo ($i++ ? ',' : '')."'$p':".'"'.preg_replace('/\r?\n/', '<br/>', addslashes($r)).'"';
    }
    ?>};
Sbr.prototype.ARB_INITS={<?php
    $i = 0;
    foreach (sbr_stages::getArbInit($stage) as $p => $r) {
        echo ($i++ ? ',' : '')."'$p':".'"'.preg_replace('/\r?\n/', '<br/>', addslashes($r)).'"';
    }
    ?>};
Sbr.prototype.ARB_RESULTS={<?php
    $i = 0;
    foreach (sbr_stages::getArbResults($stage) as $p => $r) {
        echo ($i++ ? ',' : '')."'$p':".'"'.preg_replace('/\r?\n/', '<br/>', addslashes($r)).'"';
    }
    ?>};
<?php
// Зависимости в схемах. Используется только на новых СБР #0019924
if ($stage->sbr->isNewVersionSbr()) {
    $key_init = array_keys(sbr_stages::getArbInit($stage));
    $key_reason = array_keys(sbr_stages::getArbReasons($stage));

    $disabled_reason = ($stage->sbr->isNewVersionSbr() && $stage->request['pp_init'] == $key_init[count($key_init) - 1]);
    $disabled_result = ($stage->sbr->isNewVersionSbr() && $stage->request['pp_reason'] == $key_reason[count($key_reason) - 1]);
    ?>
    Sbr.prototype.STAGE_ID = '<?=$stage->id?>';
    
    Sbr.prototype.DEPEND_PAY = {
        'pp_reason': [{emp:0, frl:0}, 
                      {emp:0, frl:0}, 
                      {emp:100, frl:0}],
        'pp_result': [{emp:100, frl:0} , 
                      {emp:0, frl:100}, 
                      {emp:0, frl:0}, 
                      {emp:100, frl:0}, 
                      {emp:100, frl:0}, 
                      {emp:100, frl:0}, 
                      {emp:0, frl:100},
                      {emp:0, frl:0}]
    
    }            
                 
    Sbr.prototype.DEPEND_SCHEME={ 
        'pp_init' : {
            select:      '<?= $key_init[count($key_init) - 1];
    ?>',
            textid:      ['bx_arb_reason', 'bx_arb_result'], 
            select_name: ['pp_reason', 'pp_result'],
            depend:     {
                '<?= $key_reason[0]?>' : '<?= $key_init[0]?>',
                '<?= $key_reason[1]?>' : '<?= $key_init[1]?>'
            }
        },
        'pp_reason' : {
            select:      '<?= $key_reason[count($key_reason) - 1]?>',
            textid:      ['bx_arb_result'], 
            select_name: ['pp_result'],
            depend:     null
        }
    };
<?php 
}//if?>
<?php 
} else {
    ?>
window.addEvent('domready', function() { 
    SBR = new Sbr('changedFrm'); 
    <?php if ($feedback_sent) {
    ?>
    new Fx.Scroll(window).toElement($(document).getElement('.nr-prj-title'));
    <?php 
}
    ?>
} );
<?php 
} ?>
</script>
<div class="tabs-in nr-tabs-in2">
    <?php include('tpl.stage-header.php') ?>
    <?php if ($sbr->isAdmin() && $stage->status == sbr_stages::STATUS_INARBITRAGE) { // !!! функции ?>

        <div class="form nr-prj nr-prj-arb">
            <div class="form-h">
                <b class="b1"></b>
                <b class="b2"></b>
                <div class="form-h-in">
                    <h3>Задача на рассмотрении у Арбитража</h3>
                </div>
            </div>
            <div class="form-in">
                <div class="nr-arb-info">
                    <strong>Причина:</strong>
                    <p><?=reformat($stage->arbitrage['descr'], 70, 0, 1, 1)?></p>
                    <?php if ($stage->arbitrage['attach']) {
    ?>
                    <br/>
                    <ul class="list-files">
                        <?php foreach ($stage->arbitrage['attach'] as $id => $a) {
    if ($a['is_deleted'] == 't') {
        continue;
    } 
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                        <li><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" target="_blank"><?=($a['orig_name'] ? $a['orig_name'] : $a['name'])?></a>, <span><?=ConvertBtoMB($a['size'])?></span><span class="avs-norisk <?=$aData['virus_class']?>" <?=($aData['virus_class'] == 'avs-nocheck' ? 'title="Антивирусом проверяются файлы, загруженные после 1&nbsp;июня&nbsp;2011&nbsp;года"' : '')?>><nobr><?=$aData['virus_msg']?></nobr></span></li>
                        <?php 
}
    ?>
                    </ul>
                    <?php 
}
    ?>
                </div>
                <?php if ($sbr->isAdmin() && $stage->status == sbr_stages::STATUS_INARBITRAGE) {
    ?>
                    <div class="nr-prj-arb-form">
                        <form action="." method="post" id="arbitragedFrm">
                            <p class="nr-prj-arb-form-warning">Обратите внимание, любые действия необратимы, пожалуйста, внимательно отнеситесь к принятию решения.</p>
                            <table>
                                <tr>
                                    <th>Заказчик:</th>
                                    <td><input type="text" disabled="true" id="emp_sum" name="e_sum" style="text-align:right" /> <?=$EXRATE_CODES[$sbr->cost_sys][0]?>
                                    <span><input type="text" maxlength="3" id="emp_percent" name="emp_percent" onchange="SBR.setArbPercent(this,<?=$stage->id?>)" onkeydown="if(event.keyCode==13){SBR.setArbPercent(this,<?=$stage->id?>);return false;}" style="text-align:right" /> <strong>%</strong></span>
                                    <div class="tip tip-t2" style="top:auto;margin-top:-8px;margin-left:100px"></div>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp;&nbsp;&nbsp; <a href="/users/<?=$sbr->emp_login?>/"><?=$sbr->emp_uname?> <?=$sbr->emp_usurname?> [<?=$sbr->emp_login?>]</a></td>
                                </tr>
                                <tr>
                                    <th>Исполнитель:</th>
                                    <td><input type="text" disabled="true" id="frl_sum" name="f_sum" style="text-align:right" /> <?=$EXRATE_CODES[$sbr->cost_sys][0]?>
                                    <span><input type="text" maxlength="3" id="frl_percent" name="frl_percent" onchange="SBR.setArbPercent(this,<?=$stage->id?>)" onkeydown="if(event.keyCode==13){SBR.setArbPercent(this,<?=$stage->id?>);return false;}" value="<?=$stage->request['frl_percent']?>" style="text-align:right" /> <strong>%</strong></span>
                                    <div class="tip tip-t2" style="top:auto;margin-top:-8px;margin-left:100px"></div>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp;&nbsp;&nbsp; <a href="/users/<?=$sbr->frl_login?>/"><?=$sbr->frl_uname?> <?=$sbr->frl_usurname?> [<?=$sbr->frl_login?>]</a></td>
                                </tr>
                            </table>
                            <div class="nr-prj-arb-inf">
                                <div class="nr-prj-arb-inf-sel">
                                    <label for="">Инициализация:</label>
                                    <select onchange="<?= $stage->sbr->scheme_type == sbr::SCHEME_LC ? 'SBR.changeRepReasonNew' : 'SBR.changeRepReason';
    ?>(this, 'bx_arb_init', SBR.ARB_INITS)" name="pp_init">
                                      <?php if ($stage->sbr->scheme_type != sbr::SCHEME_LC) {
    ?>
                                        <option>Другое (указать)</option>
                                      <?php 
} else {
    $stage->request['init'] = ($stage->request['init'] == '' ? current(sbr_stages::getArbInit($stage)) : $stage->request['init']);
}
    ?>  
                                      <?php foreach (sbr_stages::getArbInit($stage) as $pp => $r) {
    ?>
                                         <option value="<?=$pp?>"<?=$stage->request['pp_init'] == $pp ? ' selected="true"' : ''?>><?=$pp?></option>
                                      <?php 
}
    ?>
                                    </select>
                                </div>
                                <span><textarea rows="5" cols="40" id="bx_arb_init" name="init"><?=$stage->request['init']?></textarea></span>
                                <div class="tip tip-t2" style="top:auto;margin-top:-7px;z-index:1"></div>
                                <br />
                                <div class="nr-prj-arb-inf-sel">
                                    <label for="">Причина: </label>
                                    <select onchange="<?= $stage->sbr->scheme_type == sbr::SCHEME_LC ? 'SBR.changeRepReasonNew' : 'SBR.changeRepReason';
    ?>(this, 'bx_arb_reason', SBR.ARB_REASONS)" name="pp_reason" <?=  $disabled_reason ? 'disabled' : '';
    ?>>
                                      <option>Другая причина</option>
                                      <?php foreach (sbr_stages::getArbReasons($stage) as $pp => $r) {
    ?>
                                         <option value="<?=$pp?>"<?=$stage->request['pp_reason'] == $pp ? ' selected="true"' : ''?>><?=$pp?></option>
                                      <?php 
}
    ?>
                                    </select>
                                </div>
                                <span><textarea rows="5" cols="40" <?=  $disabled_reason ? 'disabled' : '';
    ?> id="bx_arb_reason" <?= ($stage->sbr->scheme_type == sbr::SCHEME_LC) && ($stage->request['pp_reason'] == 'Другая причина' || $stage->request['pp_reason'] == '') ? 'style="display:none"' : '';
    ?> name="reason"><?=$stage->request['reason']?></textarea></span>
                                <div class="tip tip-t2" style="top:auto;margin-top:-7px;z-index:1"></div>
                                <br />
                                <div class="nr-prj-arb-inf-sel">
                                    <label for="">Решение:</label>
                                    <select onchange="<?= $stage->sbr->scheme_type == sbr::SCHEME_LC ? 'SBR.changeRepReasonNew' : 'SBR.changeRepReason';
    ?>(this, 'bx_arb_result', SBR.ARB_RESULTS)" name="pp_result" <?=  $disabled_result ? 'disabled' : '';
    ?>>
                                      <?php if ($stage->sbr->scheme_type != sbr::SCHEME_LC) {
    ?>
                                        <option>Другое (указать)</option>
                                      <?php 
} else {
    $stage->request['result'] = ($stage->request['result'] == '' ? current(sbr_stages::getArbResults($stage)) : $stage->request['result']);
}//if?>  
                                      <?php foreach (sbr_stages::getArbResults($stage) as $pp => $r) {
    ?>
                                         <option value="<?=$pp?>"<?=$stage->request['pp_result'] == $pp ? ' selected="true"' : ''?>><?=$pp?></option>
                                      <?php 
}
    ?>
                                    </select>
                                </div>
                                <span><textarea rows="5" cols="40" <?=  $disabled_result ? 'disabled' : '';
    ?> id="bx_arb_result" name="result"><?=$stage->request['result']?></textarea></span>
                                <div class="tip tip-t2" style="top:auto;margin-top:-7px;z-index:1"></div>
                                <br />
                                <?php if ($stage->sbr->scheme_type != sbr::SCHEME_LC) {
    ?>
                                <label class="both c"><span class="i-chk"><input type="checkbox" name="by_consent"<?=$stage->request['by_consent'] ? ' checked="true"' : ''?>/></span> По взаимному согласию</label>
                                <?php 
} else {
    ?>
                                <input type="hidden" value="1" name="by_consent"/>
                                <?php 
}//if?>
                            </div>
                            <div class="nr-prj-arb-txt">
                                <label for="wfeasz"><strong>Громогласно</strong> прокомментировать:</label>
                                <span><textarea rows="5" cols="40" id="" name="descr_arb"><?=$stage->request['descr_arb']?></textarea></span>
                                <div class="tip tip-t2" style="top:auto;margin-top:-7px"></div>
                            </div>
                            <div class="nr-prj-btns c">
                                <label><input type="checkbox" name="iagree" onclick="SBR.form.sendform.disabled=!this.checked" /> Подтверждаю, что я в состоянии вынести адекватное решение.</label>
                                <input type="submit" name="sendform" value="Принять решение (необратимо)" class="i-btn" disabled="true" /> 
                                <input type="submit" name="cancel" value="Отменить Арбитраж" class="i-btn nr-draft-del" /> 
                            </div>
                            <input type="hidden" name="site" value="<?=$site?>" /> 
                            <input type="hidden" name="id" value="<?=$stage->id?>" /> 
                            <input type="hidden" name="action" value="arb_resolve" /> 
                        </form>
                    </div>
                <?php 
}
    ?>
            </div>
            <b class="b2"></b>
            <b class="b1"></b>
        </div>
        <?php if ($stage->sbr->scheme_type == sbr::SCHEME_LC) {
    ?>
        <script type="text/javascript">
            window.addEvent('domready', 
                function() {
                    if($('arbitragedFrm').getElement('select[name=pp_init]').selectedIndex == 0) {
                        $('arbitragedFrm').getElement('select[name=pp_reason]').options[1].style.display = 'none';
                    } else if($('arbitragedFrm').getElement('select[name=pp_init]').selectedIndex == 1) {
                        $('arbitragedFrm').getElement('select[name=pp_reason]').options[2].style.display = 'none';
                    }
                }
            );
        </script>
        <?php 
} //if ?>
    <?php 
} ?>

    <?php if ($stage_changed) {
    $vis_changes = 0;
    ?>
        <div class="form form-changed">
            <div class="form-h">
                <b class="b1"></b>
                <b class="b2"></b>
                <div class="form-h-in">
                    <h3>Изменения в задаче</h3>
                </div>
            </div>
            <div class="form-in">
                <?php 
                   // !!!
                   $new_dead_time_ex = ($stage->v_data['dead_time'] && $stage->data['dead_time'] && $stage->v_data['dead_time'] != $stage->data['dead_time']);
    $old_tm = (!$stage->v_data['dead_time'] ? (int) $stage->v_data['work_time'].' '.ending(abs((int) $stage->v_data['work_time']), 'день', 'дня', 'дней') : date('d '.strtolower($MONTHA[date('n', strtotime($stage->v_data['dead_time']))]).' Y H:i', strtotime($stage->v_data['dead_time'])));
    $new_tm = (!$stage->data['dead_time'] ? (int) $stage->data['work_time'].' '.ending(abs((int) $stage->data['work_time']), 'день', 'дня', 'дней') : date('d '.strtolower($MONTHA[date('n', strtotime($stage->data['dead_time']))]).' Y H:i', strtotime($stage->data['dead_time'])));

    if ($new_tm != $old_tm) {
        if (!$new_dead_time_ex) {
            $new_work_time_ex = true;
        }
        ?>
                <div class="form-block block-new">
                    <span class="nr-prj-ch-num"><?=++$vis_changes?>.</span>
                    <div class="form-el">
                        <table>
                            <col width="175" />
                            <col width="145" />
                            <col />
                            <col />
                            <tr>
                                <th>Срок</th>
                                <td class="date-old"><?=$old_tm?></td>
                                <td><span class="rarr">&nbsp;&nbsp;&nbsp;&rarr;&nbsp;&nbsp;&nbsp;</span></td>
                                <td><span class="date-new"><?=$new_tm?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php 
    }
    ?>
                <?php if ($stage->data['status'] != $stage->v_data['status']) {
    ?>
                <div class="form-block block-new">
                    <span class="nr-prj-ch-num"><?=++$vis_changes?>.</span>
                    <div class="form-el">
                        <table>
                            <col width="175" />
                            <col width="145" />
                            <col />
                            <col />
                            <tr>
                                <th>Статус</th>
                                <td class="date-old"><?=sbr_stages::$ss_classes[$stage->v_data['status']][1]?></td>
                                <td><span class="rarr">&nbsp;&nbsp;&nbsp;&rarr;&nbsp;&nbsp;&nbsp;</span></td>
                                <td><span class="date-new"><?=sbr_stages::$ss_classes[$stage->data['status']][1]?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php 
}
    ?>
                <?php if ($new_cost_ex = ($stage->cost != $stage->v_data['cost'] || $sbr->cost_sys != $sbr->v_data['cost_sys'] || $sbr->scheme_type != $sbr->v_data['scheme_type'])) {
    ?>
                <div class="form-block block-new">
                    <span class="nr-prj-ch-num"><?=++$vis_changes?>.</span>
                    <div class="form-el">
                        <table>
                            <col width="175" />
                            <col width="145" />
                            <col />
                            <col />
                            <tr>
                                <th>Бюджет</th>
                                <td class="date-old"><?=sbr_meta::view_cost($stage->v_data['cost'], $sbr->v_data['cost_sys'])?></td>
                                <td><span class="rarr">&nbsp;&nbsp;&nbsp;&rarr;&nbsp;&nbsp;&nbsp;</span></td>
                                <td><span class="date-new"><?=sbr_meta::view_cost($stage->cost, $sbr->cost_sys)?></span></td>
                            </tr>
                        </table>
                        <?=$sbr->view_scheme_info($stage->cost);
    ?>
                    </div>
                </div>
                <?php 
}
    ?>
                <?php if ($new_descr_ex = ($stage->data['descr'] != $stage->v_data['descr']
                      || $stage->data['attach_diff'] != $stage->v_data['attach_diff']
                      || $stage->data['attach_diff'] && $stage->v_data['attach_diff'] && (array_diff_assoc($stage->v_data['attach_diff'], $stage->data['attach_diff']) || array_diff_assoc($stage->data['attach_diff'], $stage->v_data['attach_diff'])))) { // !!! ?>
                <div class="form-block block-new">
                    <span class="nr-prj-ch-num"><?=++$vis_changes?>.</span>
                    <div class="form-el">
                        <p><strong>Техническое задание (новая версия)</strong></p>
                        <p><?=reformat($stage->data['descr'], 70)?></p>
                        <?php if ($stage->data['attach']) {
    ?>
                        <ul class="list-files">
                            <?php foreach ($stage->data['attach'] as $id => $a) {
    if ($a['is_deleted'] == 't') {
        continue;
    } 
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                            <li><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" target="_blank"><?=($a['orig_name'] ? $a['orig_name'] : $a['name'])?></a>, <span><?=ConvertBtoMB($a['size'])?></span><span class="avs-norisk <?=$aData['virus_class']?>" <?=($aData['virus_class'] == 'avs-nocheck' ? 'title="Антивирусом проверяются файлы, загруженные после 1&nbsp;июня&nbsp;2011&nbsp;года"' : '')?>><nobr><?=$aData['virus_msg']?></nobr></span></li>
                            <?php 
}
    ?>
                        </ul>
                        <?php 
}
    ?>
                    </div>
                </div>
                <div class="form-block block-old last <?=$stage->isTzOpened() ? 'flt-show' : 'flt-hide'?>" id="nr-tz" page="<?=$stage->getCCTZKey()?>">
                    <div class="form-el">
                        <div><strong>Техническое задание<?=($new_descr_ex ? ' (старая версия)' : '')?></strong> <a href="javascript: void(0);" class="lnk-dot-blue flt-tgl-lnk"><?=$stage->isTzOpened() ? 'Скрыть' : 'Показать'?></a></div>
                        <div class="flt-cnt">
                            <div class="utxt"><p><?=reformat($stage->v_data['descr'], 70, 0, 0, 1)?></p></div>
                            <?php if ($stage->v_data['attach']) {
    ?>
                            <ul class="list-files">
                                <?php foreach ($stage->v_data['attach'] as $id => $a) {
    if ($a['is_deleted'] == 't') {
        continue;
    }  
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                                <li><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" target="_blank"><?=($a['orig_name'] ? $a['orig_name'] : $a['name'])?></a>, <span><?=ConvertBtoMB($a['size'])?></span><span class="avs-norisk <?=$aData['virus_class']?>" <?=($aData['virus_class'] == 'avs-nocheck' ? 'title="Антивирусом проверяются файлы, загруженные после 1&nbsp;июня&nbsp;2011&nbsp;года"' : '')?>><nobr><?=$aData['virus_msg']?></nobr></span></li>
                                <?php 
}
    ?>
                            </ul>
                            <?php 
}
    ?>
                        </div>
                    </div>
                </div>
                <b class="b2"></b>
                <b class="b1"></b>
                <?php 
}
    ?>
                <?php if (!$vis_changes) {
    ?>
                    <div class="form-block block-new">
                        <div class="form-el">
                          Видимых изменений нет. Возможно, заказчик передумал и вернул предыдущую версию.<br/>
                          Пожалуйста, ознакомьтесь с <a href="?site=history&id=<?=$sbr->id?>&filter[stage_id]=<?=$stage->id?>">историей изменений</a> задачи.
                        </div>
                    </div>
                <?php 
}
    ?>
            </div>
        </div>
        <?php include($fpath.'tpl.stage-changed.php') ?>
    <?php 
} ?>
    <?php if (!$new_descr_ex) {
    if (($stage->status == sbr_stages::STATUS_INARBITRAGE || $stage->status == sbr_stages::STATUS_ARBITRAGED) && hasPermissions('sbr')) {
        $frl_version = $stage->getVersion($stage->frl_version, $stage->data);

        $descr = $frl_version['descr'];
        $attach = $frl_version['attach'];
    } else {
        $descr = $stage->data['descr'];
        $attach = $stage->data['attach'];
    }
    ?>
        <div class="form flt-out <?=$stage->isTzOpened() ? 'flt-show' : 'flt-hide'?> form-tz" id="nr-tz" page="<?=$stage->getCCTZKey()?>">
            <b class="b1"></b>
            <b class="b2"></b>
            <div class="flt-bar">
                <a href="javascript: void(0);" class="flt-tgl-lnk"><?=$stage->isTzOpened() ? 'Скрыть' : 'Показать'?></a>
                <h4>Техническое задание</h4>
            </div>
            <div class="form-in flt-cnt">
                <div class="form-block first last">
                    <div class="form-el">
                        <div class="utxt"><p><?=reformat($descr, 70, 0, 0, 1)?></p></div>
                        <?php if ($attach) {
    ?>
                        <ul class="list-files">
                            <?php foreach ($attach as $id => $a) {
    if ($a['is_deleted'] === 't' && ($stage->status == sbr_stages::STATUS_INARBITRAGE || $stage->status == sbr_stages::STATUS_ARBITRAGED) && hasPermissions('sbr')) {
        continue;
    }
    $aData = getAttachDisplayData(null, $a['name'], $a['path']);
    ?>
                            <li><a href="<?=WDCPREFIX.'/'.$a['path'].$a['name']?>" target="_blank"><?=($a['orig_name'] ? $a['orig_name'] : $a['name'])?></a>, <span><?=ConvertBtoMB($a['size'])?></span><span class="avs-norisk <?=$aData['virus_class']?>" <?=($aData['virus_class'] == 'avs-nocheck' ? 'title="Антивирусом проверяются файлы, загруженные после 1&nbsp;июня&nbsp;2011&nbsp;года"' : '')?>><nobr><?=$aData['virus_msg']?></nobr></span></li>
                            <?php 
}
    ?>
                        </ul>
                        <?php 
}
    ?>
                    </div>
                </div>
            </div>
        </div>
    <?php 
} ?>

      
    <?php if ($feedback_sent) {
    ?>
        <div class="form form-complite resp-thanks">
            <div class="form-h">
                <b class="b1"></b>
                <b class="b2"></b>
                <div class="form-h-in">
                    <?php if ($sbr->isEmp() && $stage->status != sbr_stages::STATUS_ARBITRAGED) {
    ?>
                        <h3>Поздравляем, вы завершили задачу!</h3>
                    <?php 
} else {
    ?>
                        <h3>Спасибо, ваш отзыв отправлен!</h3>
                    <?php 
}
    ?>
                </div>
            </div>
            <div class="form-in">
                <?php if ($sbr->isEmp()) {
    ?>
                    <?php if ($stage->status != sbr_stages::STATUS_ARBITRAGED) {
    ?>
                    <em>Спасибо, ваш отзыв отправлен!</em><br /><br />
                    <?php 
}
    ?>
                    <em>Обратите внимание:</em> Сделка будет считаться незавершенной до получения всех необходимых документов обеих сторон. Пожалуйста, пришлите необходимые документы, которые будут сформированы и загружены в раздел "<a href="/norisk2/?site=docs&id=<?=$sbr->id?>&sid=<?= $stage->id?>" target="_blank">Документы</a>" вашей сделки сразу после выплаты исполнителю.
                <?php 
} else {
    ?>
                    <strong>Обратите внимание, это очень важно:</strong>
                    «Безопасная Сделка» считается завершенной только после подписания <a href="/norisk2/?site=docs&id=<?= $sbr->id ?>">Акта</a> каждого из участников сделки. 
                    <?php if (!$sbr->checkUserReqvs()) {
    ?>
                    Вам необходимо заполнить реквизиты на странице <a href="/users/<?=$sbr->login?>/setup/finance/">Финансов</a>. 
                    <?php 
}
    ?>
                    Только после подписания всех необходимых документов вы можете получить денежные средства.
                <?php 
}
    ?>
            </div>
            <b class="b2"></b>
            <b class="b1"></b>
        </div>
    <?php 
} ?>
    
    <?php include 'tpl.stage-msgs.php'; ?>
</div>
