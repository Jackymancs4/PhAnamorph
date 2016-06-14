<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

##Interface to anamorph class
##############################

include 'class/ana-class.php';

//http://localhost:90/test/php-anamorph/ana-int.php?address=aaa.jpg&altezzacm=5&lunghezzaoriginecm=10&altezzaoriginecm=20
//http://localhost:90/test/php-anamorph/ana-int.php?address=aaa.jpg&altezzacm=5&lunghezzaoriginecm=10&altezzaoriginecm=20&ana=true&anatype=fill&per=true&pertype=sub

if (!isset($_GET['address'])) {
    echo 'Indirizzo immagine non trovato!';
    exit();
}

if (!isset($_GET['altezzacm'])) {
    echo "Manca l'altezza aspettata dell'immagine!";
    exit();
}
if (!isset($_GET['lunghezzaoriginecm'])) {
    echo "Manca la distanza orizzontale dall'immagine!";
    exit();
}
if (!isset($_GET['altezzaoriginecm'])) {
    echo "Manca la distanza verticale dall'immagine!";
    exit();
}

$anamorph = new Anamorph();
$anamorph->load($_GET['address']);

$altezzacm = $_GET['altezzacm'];
$lunghezzaoriginecm = $_GET['lunghezzaoriginecm'];
$altezzaoriginecm = $_GET['altezzaoriginecm'];

$anamorph->init($lunghezzaoriginecm, $altezzaoriginecm, $altezzacm);

if (!isset($_GET['ana'])) {
    $_GET['ana'] = 'true';
}
if (!isset($_GET['anatype'])) {
    $_GET['anatype'] = 'fill';
}

if (!isset($_GET['per'])) {
    $_GET['per'] = true;
}
if (!isset($_GET['pertype'])) {
    $_GET['pertype'] = 'sub';
}

if ($_GET['ana'] == 'true') {
    if ($_GET['anatype'] == 'fill') {
        $anamorph->make_anamorph_fill();
    }
    if ($_GET['anatype'] == 'norm') {
        $anamorph->make_anamorph();
    }
}

 if ($_GET['per'] == 'true') {
     if ($_GET['pertype'] == 'norm') {
         $anamorph->make_ana_perspective();
     } elseif ($_GET['pertype'] == 'add' || $_GET['pertype'] == 'sub') {
         $anamorph->make_ana_perspective_fill($_GET['pertype']);
     }
 }

if (isset($_GET['ret'])) {
    $anamorph->return_image($_GET['ret']);
} else {
    $anamorph->return_image();
}
