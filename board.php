<html>
<head>
<title>Zapis partii Scrabble</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="bootstrap-buttons.css">
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

<script>
$( document ).ready(function() {
    $('.upload').on('change', function(event) {
        var file_data = $(event.target).prop('files')[0];
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        form_data.append('turniej', $(event.target).attr('data-turniej'));
        form_data.append('runda', $(event.target).attr('data-runda'));
        form_data.append('player1', $(event.target).attr('data-player1'));
        form_data.append('player2', $(event.target).attr('data-player2'));
        form_data.append('p1pts', $(event.target).attr('data-p1pts'));
        form_data.append('p2pts', $(event.target).attr('data-p2pts'));
        form_data.append('update', 1);
        $.ajax({
         url: 'upload.php',
         dataType: 'text',
         cache: false,
         contentType: false,
         processData: false,
         data: form_data,                         
         type: 'post',
         success: function(php_script_response){
             console.log(php_script_response);
             var response = $.parseJSON(php_script_response);
             if ( response.status == 'error') {
                 alert( response.errormsg );
             }
             else {
                 alert('Zapis zaktualizowany.');
                 window.location.reload(true);
             }
         }
        });
    });
});
</script>
</head>
<body>

<?php

session_start();
require_once 'functions.php';
mb_internal_encoding("UTF-8");


function showBoard($moves, $gcgtext) {
    $board = array_fill(0, 15, array_fill(0, 15, 0));
    $j = 0;

    $board_bonus['word3'] = array(array(0, 0), array(14,14), array(0, 14), array(14, 0), array(0,7),
                                  array(7,0), array(14,7), array(7,14));
    $board_bonus['word2'] =  array(array(7,7),
                                   array(1,1), array(2,2), array(3,3), array(4,4), array (10, 10), array(11,11), array(12,12), array(13,13),
                                   array(1,13), array(2,12), array(3,11), array(4,10), array(13,1), array(12,2), array(11,3), array(10,4));
    $board_bonus['letter3'] = array(array(5,5), array(9,9), array(5,9), array(9,5),
                                    array(5,1), array(1,5), array(9,1), array(1,9), array(5,13), array(13,5), array(9,13), array(13,9));
    $board_bonus['letter2'] = array(array(3,0), array(0,3), array(14,3), array(3,14), array(11,0), array(11, 14), array(14, 11), array(0,11),
                                    array(3,7), array(7,3), array(7,11), array(11,7),
                                    array(2,6), array(2,8), array(6,2), array(8,2), array(12,6), array(12,8), array(6,12), array(8,12),
                                    array(6,6), array(8,8), array(8,6), array(6,8));
    $board_bonuses = array_fill(0, 15, array_fill(0, 15, 0));
    foreach ($board_bonus as $k => $v) {
        foreach ($v as $t) {
            $board_bonuses[$t[0]][$t[1]] = $k;
        }
    }

    $letter_points = array(
        'A' => 1,
        'Ą' => 5,
        'B' => 3,
        'C' => 2,
        'Ć' => 6,
        'D' => 2,
        'E' => 1,
        'Ę' => 5,
        'F' => 5,
        'G' => 3,
        'H' => 3,
        'I' => 1,
        'J' => 3,
        'K' => 2,
        'L' => 2,
        'Ł' => 3,
        'M' => 2,
        'N' => 1,
        'Ń' => 7,
        'O' => 1,
        'Ó' => 5,
        'P' => 2,
        'R' => 1,
        'S' => 1,
        'Ś' => 5,
        'T' => 2,
        'U' => 3,
        'W' => 1,
        'Y' => 2,
        'Z' => 1,
        'Ź' => 9,
        'Ż' => 5,
    );

    $length = count($moves);
    for ($k = 0; $k < $length; $k++) {
        $mv = explode(' ', $moves[$k]);
        if ($k < $length - 1) {
            $next_mv = explode(' ', $moves[$k+1]);
            if ($next_mv[2] == '--') {
                continue;
            }
        }

        if ($mv[2][0] != '-') {
            if (ord(substr($mv[2], -1)) > 64 && ord(substr($mv[2], -1)) < 80) {
                $start_row = intval(substr($mv[2], 0, -1))-1;
                $end_row = $start_row;
                $start_col = ord(substr($mv[2], -1)) - 65;
                $end_col = $start_col + mb_strlen($mv[3])-1;
            }
            else {
                $start_row = intval(substr($mv[2], 1))-1;
                $end_row = $start_row + mb_strlen($mv[3])-1;
                $start_col = ord(substr($mv[2], 0, 1)) - 65;
                $end_col = $start_col;
            }            
            
            $i = 0;
            for ($row = $start_row; $row <= $end_row; $row++) {
                for ($col = $start_col; $col <= $end_col; $col++) {
                    if (mb_substr($mv[3], $i, 1) != '.') {
                        $board[$row][$col] = mb_substr($mv[3], $i, 1);
                    }
                    $i += 1;
                }
            }
        }

    }
    ?>
    
    <?php
    
    #Board rendering and CSS based on a script by Weronika Rudnicka (ustczanka)
    $output = '<div id="left">';
    $output .= '<div id="board">';

    $output .= '<div class="boardrow header"><div class="tile"></div>';
    for ($i = 1; $i < 16; ++$i) {
        $output .= '<div class="tile">' . chr(64+$i) . '</div>';
    }
    $output .= '<div class="tile"></div></div>';

    for ($row = 0; $row < 15; $row++) {
        $output .= '<div class="boardrow">';
        $output .= '<div class="tile header">' . ($row+1) . '</div>';
        for ($col = 0; $col < 15; $col++) {
            
            if ($board[$row][$col] !== 0) {
                $output .= '<div class="tile letter';
                if (mb_strtolower($board[$row][$col]) == $board[$row][$col]) {
                    $output .= ' blank';
                }
                $output .= '">' . mb_strtoupper($board[$row][$col]);
                if (array_key_exists($board[$row][$col], $letter_points)) {
                    $output .= '<div class="points">' . $letter_points[$board[$row][$col]] . '</div>';
                }
                $output .= '</div>';
            }
            else {
                $output .= '<div class="tile ' . $board_bonuses[$row][$col] . '"></div>';
            }
        }
        $output .= '<div class="tile"></div>';
        $output .= '</div>';
    }

    $output .= '<div class="boardrow header"><div class="tile"></div>';
    for ($i = 1; $i < 16; ++$i) {
        $output .= '<div class="tile"></div>';
    }
    $output .= '<div class="tile"></div></div>';

    $output .= '</div>';
    
    print $output;

    $gcgtable = gcgToTable($gcgtext);
    
    print statsTable($gcgtable['stats'], $gcgtable['players']);

    print '[<a href="download.php?turniej=' . $_GET['turniej'] . '&runda=' . $_GET['runda'] . '&p1=' . $_GET['p1'] . '&p2=' . $_GET['p2'] . '">ściągnij zapis</a>]';

    if (!isset($_SESSION['user'])) {
        $_SESSION['user']="";
        $_SESSION['pass']="";
    }
    
    if (isset($_POST["user"])) {	
        $_SESSION['user']=hash('sha256', $_POST['user']);
        $_SESSION['pass']=hash('sha256', $_POST['pass']);
    }
    
    include_once 'credentials.php';

    if($_SESSION['user'] == $uploaduser
       && $_SESSION['pass'] == $uploadpass)
	{
        $gcgscores = getFinalScore($gcgtext);
        if ($gcgscores != -1) {
            
            echo '<div class="fileUpload btn">';
            echo '<span>Zaktualizuj</span>';
            echo '<input type="file" class="upload" ';
            echo ' data-turniej=' . $_GET['turniej'] . ' data-runda=';
            echo $_GET['runda'] . " data-player1=" . $_GET['p1'] . " data-player2=" . $_GET['p2'];
            echo " data-p1pts=" . $gcgscores['p1'] . " data-p2pts= " . $gcgscores['p2'];
            echo ' /></div>';
        }
        else {
            print_r($gcgscores);
        }
    }
    else {
		?>
        <br /><br />Zaloguj się, by aktualizować zapisy:
        <form method="POST" action="">
        login: <input type="text" name="user"><br/>
        hasło: <input type="password" name="pass"><br/>
        <input type="submit" name="submit" value="zaloguj">
        </form>
        <?php
    }

    print '</div>';


    $gcgprint = '<div id="gcg">';
    $gcgprint .= $gcgtable['table'];

    $gcgprint .='</div>';
    print $gcgprint;

}

function generateMovesTable($gcg) {
    $all_lines = preg_split("/\\r\\n|\\r|\\n/", $gcg);
    return array_slice($all_lines, 2);
}

$player1=$_GET['p1'];
if ($player1>$_GET['p2']) {
	$player1=$_GET['p2'];
}

require_once "../system.php";
db_open();
$query = "SELECT data FROM ".TBL_GCG." WHERE tour=".$_GET['turniej']." AND round=".$_GET['runda']." AND player1=".$player1.';';
$result = db_query($query);
if ($result) {
    $gcgtext = db_fetch_row($result)[0];
    $mymoves = generateMovesTable($gcgtext);
    showBoard($mymoves, $gcgtext);
}
db_close();

?>

</body>
</html>