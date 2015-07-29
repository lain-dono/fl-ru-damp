<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/projects.php';
new_projects::careerjetGenerateRss('upload/careerjet.xml');
echo 'файл готов';

?>