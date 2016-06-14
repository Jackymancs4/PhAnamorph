<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

include 'class/ana-class.php';

$anamorph = new Anamorph();
$anamorph->load('img/moustache2.jpg');

$altezzacm = 5;
$lunghezzaoriginecm = 10;
$altezzaoriginecm = 20;

$anamorph->init($lunghezzaoriginecm, $altezzaoriginecm, $altezzacm);
$anamorph->make_anamorph_fill();
$anamorph->make_ana_perspective_fill('add');

$anamorph->return_image();
