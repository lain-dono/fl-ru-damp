<?php if (!defined('IS_SITE_ADMIN')) {
    header('Location: /404.php');
    exit;
} ?>
<h2>Поиск</h2>
<div class="admin">
<div class="lm-col">
    <div class="admin-menu">
        <h3>Поиск</h3>

        <?php include ($rpath.'/siteadmin/leftmenu.php') ?>

    </div>
</div>
</div>
<div class="r-col">
    <div class="ban-razban">
        <h3>Запросы пользователей</h3>
        <?php include_once 'tpl.navigation.php'; ?>

        <br/>
        <div class="form form-nr-docs-sort ">
            <b class="b1"></b>
            <b class="b2"></b>
            <div class="form-in">
                <div class="form-b">
                    <div class="form-block first">
                        <div class="form-el" style="text-transform: uppercase; font-size: 14px;">
                            <?php foreach ($rus as $k) {
    ?>
                                <?php if ($k != $start) {
    ?>
                                    <a href="?s=<?= $k ?>"><?= $k ?></a>
                                <?php 
} elseif ($k == $start) {
    ?>
                                    <strong><?= $k ?></strong>
                                <?php 
} else {
    ?>
                                    <?= $k ?>
                                <?php 
}
    ?>
                            <?php 
} ?>
                        </div>
                    </div>
                    <div class="form-block">
                        <div class="form-el" style="text-transform: uppercase; font-size: 14px;">
                            <?php foreach ($eng as $k) {
    ?>
                                <?php if ($k != $start) {
    ?>
                                    <a href="?s=<?= $k ?>"><?= $k ?></a>
                                <?php 
} elseif ($k == $start) {
    ?>
                                    <strong><?= $k ?></strong>
                                <?php 
} else {
    ?>
                                    <?= $k ?>
                                <?php 
}
    ?>
                            <?php 
} ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($start != 'num') {
    ?>
                            <a href="?s=num">0 - 9</a>
                            <?php 
} else {
    ?>
                            <strong>0 - 9</strong>
                            <?php 
} ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($start != 'others') {
    ?>
                            <a href="?s=others">Другие</a>
                            <?php 
} else {
    ?>
                            <strong>Другие</strong>
                            <?php 
} ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($start != 'all') {
    ?>
                            <a href="?s=all">Все</a>
                            <?php 
} else {
    ?>
                            <strong>Все</strong>
                            <?php 
} ?>
                        </div>
                    </div>
                    <div class="form-block last">
                        <div class="form-el" style="text-transform: uppercase; font-size: 14px;">
                            <?php if ($start != 'users') {
    ?>
                            <a href="?s=users">По исполнителям</a>
                            <?php 
} else {
    ?>
                            <strong>По исполнителям</strong>
                            <?php 
} ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($start != 'projects') {
    ?>
                            <a href="?s=projects">По проектам</a>
                            <?php 
} else {
    ?>
                            <strong>По проектам</strong>
                            <?php 
} ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($start != 'more') {
    ?>
                            <a href="?s=more">По разделам сайта</a>
                            <?php 
} else {
    ?>
                            <strong>По разделам сайта</strong>
                            <?php 
} ?>
                        </div>
                    </div>
                </div>
            </div>
            <b class="b2"></b>
            <b class="b1"></b>
        </div>

        <!-- Таблица клиентов и кампаний -->
        <table class="tbl-cnc">
            <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th width="230">
                        Строка запроса
                    </th>
                    <th>
                        Индекс
                    </th>
                    <th width="60">
                        Кол-во повторов (N)
                    </th>
                    <th width="60">
                        Кол-во совпадений (M)
                    </th>
                    <th width="80">
                        N*M
                    </th>
                    <th width="50">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) {
    ?>
                    <tr id="query<?= $row['id'] ?>">
                        <td class="c-st">
                            &bull;
                        </td>
                        <td>
                            <?= change_q_x($row['query'], true, false) ?>
                        </td>
                        <td><?= $row['idname'] ?></td>
                        <td>
                            <?= $row['cnt'] ?>
                        </td>
                        <td>
                            <?= $row['match_cnt'] ?>
                        </td>
                        <td>
                            <strong><?= $row['cnt'] * $row['match_cnt'] ?></strong>
                        </td>

                        <td class="c-prd <?= $order == 'act' ? 'c-id' : '' ?>">
                            <a href="javascript:void(0)" onclick="deleteQuery(this)">Удалить</a>
                        </td>
                    </tr>
                <?php 
} ?>
                    <tr id="deleteFrm" style="display:none;">
                        <td colspan="7">
                            <form name="frm" method="post" action="">
                                <input type="hidden" name="action" value="add_filter"/>
                                <input type="hidden" name="query" value=""/>
                                <div class="form form-cnc">
                                    <b class="b1"></b>
                                    <b class="b2"></b>
                                    <div class="form-in">
                                        <div class="form-block first">
                                            <div class="form-el">
                                                Чтобы удаляемый запрос или его часть больше не попадали в эту таблицу, нужно заполнить эту форму.
                                            </div>
                                            <div class="form-el">
                                                <label class="form-l">Удаляет слова, которые:</label>
                                                <div class="form-value">
                                                    <select name="filter_rule" class="sw205">
                                                        <?php foreach ($rules as $rule) {
    ?>
                                                        <option value="<?= $rule['id'] ?>"><?= $rule['rule_name'] ?></option>
                                                        <?php 
} ?>
                                                    </select>
                                                    <input type="text" name="word" class="sw205"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-block last">
                                            <div class="form-el form-btns flm">
                                                <button type="submit">Удалить запрос и создать фильтр</button> или <button onclick="return deleteQueryOnly(this)">Просто удалить запрос</button> 
                                            </div>
                                        </div>
                                    </div>
                                    <b class="b2"></b>
                                    <b class="b1"></b>
                                </div>
                            </form>
                        </td>
                    </tr>
            </tbody>
        </table>

        <?= new_paginator2($page, $pages, 3, '%s?'.urldecode(url($_GET, array('p' => '%d'))).'%s') ?>

    </div>
</div>
