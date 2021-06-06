<?php

function wielkanoc($y)
{

    $a = $y % 19;
    $b = floor($y / 100);
    $c = $y % 100;
    $d = floor($b / 4);
    $e = $b % 4;
    $f = floor(($b + 8) / 25);
    $g = floor(($b - $f + 1) / 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = floor($c / 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = floor(($a + 11 * $h + 22 * $l) / 451);
    $p = ($h + $l - 7 * $m + 114) % 31;
    $wm = '0' . floor(($h + $l - 7 * $m + 114) / 31);
    $wd = $p + 1;
    if ($wd < 10) $wd = '0' . $wd;
    return $y . '-' . $wm . '-' . $wd;

}

function piatekPoBozymNarodzeniu($y) {
    $christmas = $y.'-12-25';
    $oktawa = [];
    $pastChristmas = ($y-1).'-12-25';
    $pastChristmas = new DateTime($pastChristmas);

    if($pastChristmas->format('N')==4) {
        array_push($oktawa,$y.'-01-02');
    }

    $christmas = new DateTime($christmas);
    switch($christmas->format('N')) {
        case '1': {array_push($oktawa, $y.'-12-29'); break;}
        case '2': {array_push($oktawa, $y.'-12-28'); break;}
        case '3': {array_push($oktawa, $y.'-12-27'); break;}
        case '4': {array_push($oktawa, $y.'-12-26'); break;} //oraz 2.01
        case '5': {break;} //1.01
        case '6': {array_push($oktawa, $y.'-12-31'); break;}
        case '7': {array_push($oktawa, $y.'-12-30'); break;}
    }

    return $oktawa;
}

function piatekPoWielkanocy($y) {
    $wielkanoc = new DateTime(wielkanoc($y));
    $wielkanoc->add(new DateInterval('P5D'));
    return $wielkanoc->format('Y-m-d');

}
function nspj($y) {
    $wielkanoc = new DateTime(wielkanoc($y));
    $wielkanoc->add(new DateInterval('P49D'));
    $wielkanoc->add(new DateInterval('P19D'));
    return $wielkanoc->format('Y-m-d');
}

function postScisly($y) {
    $post = [];
    $wielkanoc = new DateTime(wielkanoc($y));
    $wielkipiatek = $wielkanoc->modify('previous friday');
    array_push($post,$wielkipiatek->format('Y-m-d'));
    $srodapopielcowa = $wielkanoc->modify('-44 days');
    array_push($post,$srodapopielcowa->format('Y-m-d'));
    return $post;
}

function isTodayFriday() {
    return (date('N')==5);
}
function isThatDayFriday($date) {
    return (date('N',strtotime($date)))==5;
}

function nextFriday() {
    $today = new DateTime();
    $today->modify("next friday");
    return $today->format('Y-m-d');
}
function canIEatMeatAtDate($date) {


    $tY = date("Y",strtotime($date));
    $uroczystosci =
        [
            "uroczystość Świętej Bożej Rodzicielki Maryi" => $tY.'-01-01',
            "uroczystość Objawienia Pańskiego (Trzech Króli)" => $tY.'-01-06',
            "uroczystość św. Józefa, Oblubieńca NMP" => $tY.'-03-19',
            "uroczystość Zwiastowania Pańskiego" => $tY.'-03-25',
            "uroczystość Świętego Wojciecha, patrona Polski" => $tY.'-04-23',
            "uroczystość NMP Królowej Polski" => $tY.'-05-03',
            "uroczystość Świętego Stanisława" => $tY.'-05-08',
            "uroczystość Narodzenia Św. Jana Chrzciciela" => $tY.'-06-24',
            "uroczystość Świętych Apostołów Piotra i Pawła" => $tY.'-06-29',
            "uroczystość Wniebowzięcia NMP" => $tY.'-08-15',
            "uroczystość NMP Częstochowskiej" => $tY.'-08-26',
            "uroczystość Wszystkich Świętych" => $tY.'-11-01',
            "uroczystość Niepokalanego Poczęcia NMP" => $tY.'-12-08',
            "Boże Narodzenie" => $tY.'-12-25',
            "Oktawa Wielkanocy" => piatekPoWielkanocy($tY),
            "uroczysość Najświętszego Serca Pana Jezusa" => nspj($tY),
        ];

    $oktawaBN = piatekPoBozymNarodzeniu($tY);
    if(count($oktawaBN)==2) {
        $uroczystosci["Oktawa Bożego Narodzenia ".($tY-1)] = $oktawaBN[0];
        $uroczystosci["Oktawa Bożego Narodzenia ".$tY] = $oktawaBN[1];
    }
    if(count($oktawaBN)==1) {
        $uroczystosci["Oktawa Bożego Narodzenia ".$tY] = $oktawaBN[0];
    }



    $postScisly = postScisly($tY);

    // jeżeli Wielki Piątek lub Środa Popielcowa, to nie - sprawdzić!!!!!!!!!!!!
    if(in_array($date,$postScisly)) { return false;}
    if(isThatDayFriday($date)&&!in_array($date,$uroczystosci)) { return false;}
    //&&!in_array($date,$uroczystosciDiecezjalne[$diecezja])
    if(isThatDayFriday($date)&&!in_array($date,$uroczystosci)) {
        return array_keys($uroczystosci,$date);
    }
    if(date('N',strtotime($date))!=7) return "dzień powszedni"; else return "w niedzielę post nie obowiązuje";
}

function diecezje($date) {
    $tY = date("Y",strtotime($date));

    $uroczystosciDiecezjalne = [
        //0 => [],
        "archidiecezji białostockiej" => [$tY.'-03-04', $tY.'-04-24', $tY.'-11-16'],
        "diecezji drohiczyńskiej" => [$tY.'-03-04'],
        "diecezji łomżyńskiej" => [$tY.'-03-04', $tY.'-07-12'],
        "archidiecezji częstochowskiej" => [$tY.'-03-04'],
        "diecezji sosnowieckiej" => [$tY.'-06-17'],
        "diecezji pelplińskiej" => [$tY.'-08-10'],
        "diecezji włocławskiej" => [$tY.'-03-04', $tY.'-10-22'],
        "archidiecezji katowickiej" => [$tY.'-08-17',$tY.'-09-12', $tY.'-10-16'],
        "diecezji gliwickiej" => [$tY.'-08-17',$tY.'-10-16'],
        "diecezji opolskiej" => [$tY.'-07-26',$tY.'-08-17', $tY.'-10-16'],
        "archidiecezji krakowskiej" => [$tY.'-10-20',$tY.'-10-22'],
        "diecezji bielsko-żywieckiej" => [$tY.'-08-14'],
        "diecezji tarnowskiej" => [$tY.'-09-08',$tY.'-10-22'],
        "diecezji siedleckiej" => [$tY.'-10-28'],
        "archidiecezji łódzkiej" => [$tY.'-03-19'],
        "diecezji kaliskiej" => [$tY.'-03-04'],
        "diecezji rzeszowskiej" => [$tY.'-01-19'],
        "diecezji zielonogórsko-gorzowskiej" => [$tY.'-06-18'],
        "archidiecezji warmińskiej" => [$tY.'-11-30'],
        "diecezji elbląskiej" => [$tY.'-05-16'],
        "diecezji płockiej" => [$tY.'-05-16', $tY.'-09-18'],
        "diecezji warszawsko-praskiej" => [$tY.'-05-16'],
        "archidiecezji wrocławskiej" => [$tY.'-10-16'],
        "diecezji legnickiej" => [$tY.'-03-04', $tY.'-10-16'],
        "diecezji świdnickiej" => [$tY.'-10-16'],
        "diecezji toruńskiej" => [$tY.'-06-27',$tY.'-10-22'],
        "diecezji bydgoskiej" => [$tY.'-09-08',$tY.'-10-22'],
        "diecezji łowickiej" => [$tY.'-10-13',$tY.'-11-11'],
        "diecezji gnieźnieńskiej" => [$tY.'-10-22'],
    ];

    return array_keys($uroczystosciDiecezjalne,$date);
}

function plDayOfTheWeek($dotw) {
    $dotw = (date("N",strtotime($dotw)));
    switch($dotw) {
        case 1: return "poniedziałek";
        case 2: return "wtorek";
        case 3: return "środa";
        case 4: return "czwartek";
        case 5: return "piątek";
        case 6: return "sobota";
        case 7: return "niedziela";
        default: return "(coś się zepsuło, powiadom administratora)";
    }
}
function plMonth($dotw) {
    $dotw = (date("m",strtotime($dotw)));
    switch($dotw) {
        case 1: return "stycznia";
        case 2: return "lutego";
        case 3: return "marca";
        case 4: return "kwietnia";
        case 5: return "maja";
        case 6: return "czerwca";
        case 7: return "lipca";
        case 8: return "sierpnia";
        case 9: return "września";
        case 10: return "października";
        case 11: return "listopada";
        case 12: return "grudnia";
        default: return "(coś się zepsuło, powiadom administratora)";
    }
}
function todayIsMessage() {
    $today = date('Y-m-d');
    $message = "Dzisiaj jest <b>";
    $message .= plDayOfTheWeek($today);
    $message .= "</b>, ".(date("d"));
    $message .= " ".plMonth($today);
    $message .= " ".date("Y")." roku.";
    $message .= PHP_EOL;
    return $message;
}

function canIEatMeatTodayMessage() {
    $today = date('Y-m-d');
    $message = "";
    if(canIEatMeatAtDate($today)) {
        $message = "<strong>Można</strong> dzisiaj jeść mięso - ".canIEatMeatAtDate($today).".";
    } else {
        if(!isTodayFriday() && !canIEatMeatAtDate($today)) echo "Dziś post ścisły. "; else {
            $message = "<strong>Obowiązuje wstrzemięźliwość od pokarmów mięsnych. </strong>";
            if(!empty(diecezje($today))) {
                $message .= "Obowiązuje dyspensa, gdy przebywasz w ".implode(" lub ",diecezje($today)).".";
            }
        }
    }
    return $message;
}

function canIEatMeatNextFridayMessage() {
    if(canIEatMeatAtDate(nextFriday())) {
        $message = "<br /> W następny piątek będzie można jeść mięso - ".canIEatMeatAtDate(nextFriday());
    } else {
        $message = "<br /> W następny piątek nie będzie można jeść mięsa. ";
        if(!empty(diecezje(nextFriday()))) {
            $message .= "Zwolnienie z postu nie będzie obowiązywać podczas pobytu w ".implode(" lub ",diecezje($today)).".";
        }
    }

    return "<p>".$message."</p>";
}