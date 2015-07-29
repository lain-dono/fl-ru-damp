<?php

class page_admin_team extends page_base
{
    public function getlistAction()
    {
        $order_dir = front::$_req['dir'];
        if (!in_array($order_dir, array('ASC', 'DESC'))) {
            $order_dir = 'ASC';
        }

        $order_col = front::$_req['sort'];
        if (!in_array($order_col, array('id', 'name', 'login', 'occupation', 'email', 'icq', 'skype', 'login', 'groupid', 'additional'))) {
            $order_col = 'id';
        }

        $limit = front::$_req['limit'];
        if (!$limit) {
            $limit = 20;
        }
        $offset = front::$_req['start'];
        if (!$offset) {
            $offset = 0;
        }

        $news = front::og('db')->select('SELECT id, name, login, occupation, email, icq, skype, login, groupid, additional FROM team_people LEFT JOIN team_groups on team_groups.id = team_people.groupid  ORDER BY ?v ?v LIMIT ?n OFFSET ?n;', $order_col, $order_dir, $limit, $offset)->fetchAll();

        $news = front::toUtf($news);

        $totalCount = front::og('db')->select('SELECT COUNT(*) FROM team_people;')->fetchOne();

        echo json_encode(array('data' => $news, 'totalCount' => $totalCount));
    }

    public function getGroupsAction()
    {
        $db = front::og('db'); //   , 
        $all = $db->select('SELECT id, title FROM team_groups f WHERE (f.title ILIKE ?);', '%'.trim(front::toWin(front::$_req['query'])).'%')->fetchAll();
        $all = front::toUtf($all);
        echo json_encode(array('data' => $all));
    }
    public function getinfoAction()
    {
        $db = front::og('db');
        $one_news = $db->select('SELECT tp.*, tg.title as group_name FROM team_people tp JOIN team_groups tg on tg.id = tp.groupid WHERE tp.id = ?n LIMIT 1;', front::$_req['id'])->fetchRow();
        $one_news = front::toUtf($one_news);
        $one_news['additional'] = br2nl($one_news['additional']);
        echo json_encode(array('form' => $one_news));
    }
    public function deleteAction()
    {
        $db = front::og('db');

        if (intval($id = front::$_req['id']) > 0) {
            $affected_rows = $db->delete('DELETE FROM team_people WHERE id = ?n;',
                $id
            );
        }
        echo json_encode(array('success' => $affected_rows));
    }
    public function saveAction()
    {
        $db = front::og('db');

        $pic = page_admin_flash_upload2::getFileValue(front::$_req['form']['userpic'], 'team/');

        $save = front::toWin(array(
            'name' => front::$_req['form']['name'],
            'login' => front::$_req['form']['login'],
            'occupation' => front::$_req['form']['occupation'],
            'email' => front::$_req['form']['email'],
            'userpic' => $pic,
            'icq' => front::$_req['form']['icq'],
            'skype' => front::$_req['form']['skype'],
            'login' => front::$_req['form']['login'],
            'groupid' => front::$_req['form']['groupid'],
            'additional' => nl2br(front::$_req['form']['additional']),
        ));
        if (intval($id = front::$_req['id']) > 0) {
            $aff = $db->update('UPDATE team_people SET ?s WHERE (id = ?n)', $save, $id);
        } else {
            $id = $db->insert('team_people', $save);
        }

        echo json_encode(array('success' => true, 'id' => $id));
    }
    public function editRadzelAction()
    {
        $db = front::og('db');

        $save = front::toWin(array(
            'title' => front::$_req['title'],
        ));
        if (intval($id = front::$_req['id']) > 0) {
            $aff = $db->update('UPDATE team_groups SET ?s WHERE (id = ?n)', $save, $id);
        } else {
            team::CreateGroup(front::$_req['title']);
            //$id = $db->insert("team_groups", $save);
        }

        echo json_encode(array('success' => true, 'id' => $id));
    }
    public function moveGroupAction()
    {
        team::MoveGroup(front::$_req['direction'], front::$_req['group_id']);

        echo json_encode(array('success' => true, 'id' => front::$_req['group_id']));
    }
    public function moveUserAction()
    {
        team::MoveUser(front::$_req['direction'], front::$_req['group_id'], front::$_req['id']);

        echo json_encode(array('success' => true, 'id' => front::$_req['id']));
    }
}
