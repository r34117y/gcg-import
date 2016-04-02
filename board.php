<html>
<head>
    <title>zapis</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        table.zapis { width: 243px; font-size: 9px;}
        table.zapis td.gracz{ font-size: 14px; font-weight: bold; text-align: center; padding: 10px 5px; }
        table.zapis th, table.zapis td{ padding: 6px 2px; }
        table#plansza   {margin: 5px; text-align: center; border-spacing:0px;}
        table#plansza td    {width: 25px; height: 25px;  line-height: 25px; background:#3C8571;  border: 1px solid #23574B;}
        table#plansza th, table#plansza td:first-child  {width: 20px; height: 25px; line-height: 25px; background:#23574B; border: 1px solid #23574B; color:white; font-weight: normal; font-size:9px;}
        table#plansza td.word3    {background: #DB3920;}
        table#plansza td.word2   {background: #EFA284;}
        table#plansza td.letter3       {background: #4194E0;}
        table#plansza td.letter2  {background: #79B6E5;}
        table#plansza td.letter {background: #fcfac4;}
            .bottomSpan {bottom: 0; right: 0; position: relative; right: 0; font-size: 6px;}
div#gcg {font-family:monospace;}
        span.blank{color: red;}
        #worek{ text-align: left;   margin: 0 10px 20px 10px;}
    </style>
</head>
<body>

<?php

mb_internal_encoding("UTF-8");

error_reporting( E_ALL );

function showBoard($moves, $gcgtext) {
$board = array_fill(0, 15, array_fill(0, 15, 0));
$j =0;

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
    print '<div id="board">
    </table>
    <table id="plansza" class="onleft">
        <tr>
            <th>&nbsp;</th>
';

    for ($i = 1; $i < 16; ++$i) {
        print "<th>$i</th>";
    }

    print '<th>&nbsp;</th></tr>';

for ($row = 0; $row < 15; $row++) {
    $output .= '<tr><td class="aligncenter">' . chr(65 + $row) . '</td>';

    for ($col = 0; $col < 15; $col++) {

        if ($board[$row][$col] !== 0) {
            $output .= '<td class="letter">' . $board[$row][$col] . '</td>';
        }
        else {
            $output .= '<td class="' . $board_bonuses[$row][$col] . '"></td>';
        }
    }
    $output .= '<th>&nbsp;</th></tr>';
}


$output .= '<tr>';
for ($i = 0; $i < 17; $i++) {
    $output .= '<th>&nbsp;</th>';
}
$output .= '</tr></table></div>';
print $output;

print '[<a href="download.php?turniej=' . $_GET['turniej'] . '&runda=' . $_GET['runda'] . '&p1=' . $_GET['p1'] . '">ściągnij zapis</a>]<br /><br />';

$gcgprint = '<div id="gcg">';
$gcgprint .= nl2br($gcgtext);

fclose($myfile);

$gcgprint .='</div>';
print $gcgprint;
  
}


function generateMovesTable($gcg) {
    $all_lines = explode(PHP_EOL, $gcg);
    return array_slice($all_lines, 2);
}


include 'config.php';
$con = mysqli_connect($mysqlhost, $mysqluser, $mysqlpwd, $mysqldbname);
$query = 'SELECT gcg FROM PFSTOURHH WHERE turniej = ' . $_GET['turniej'] . ' AND runda = ' . $_GET['runda'] . ' AND player1 = ' . $_GET['p1'] . ';';
mysqli_set_charset($con, 'utf8');
$result = mysqli_query($con, $query);
if ($result) {
    $gcgtext = mysqli_fetch_array($result)[0];
    //$moves_fname = 'upload/gcg/' . $_GET['turniej'] . '_' . $_GET['runda'] . '_' . $_GET['p1'] . '_' . $_GET['p2'] . '.gcg';
    $mymoves = generateMovesTable($gcgtext);
    showBoard($mymoves, $gcgtext);
    }
?>

</body>
</html>