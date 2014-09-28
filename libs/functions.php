<?php

function timeDiff($start, $end, $op = "-")
{
    $start = explode(":", $start);
    $end = explode(":", $end);

    if (is_array($start) && !empty($start) && is_array($end) && !empty($end)) {

        $spaces = count($start)-1;
        $total = array();
        $carrier = 0;
        for ($i = $spaces; $i >= 0; $i--) {

            if ($i == 3) {
                $base = 25;
            } else {
                $base = 60;
            }

            if ($op == "-") {
                $diff = ($end[$i] - $start[$i]) + $carrier;
            } else {
                $diff = ($end[$i] + $start[$i]) + $carrier;
            }

            //Positive carrier (impossible on a diff)
            if ($diff >= $base) {
                $carrier = 1;
                $diff = $diff - $base;
            //Negative carrier
            } elseif ($diff < 0) {
                $carrier = -1;
                $diff = $base - abs($diff);
            } else {
                $carrier = 0;
            }

            $total[$i] = str_pad($diff, 2, "0", STR_PAD_LEFT);
        }

        return implode(":", array_reverse($total));
    }
}

function dateAddTime($date, $time)
{
    $start = explode(" ", $date);

    $timeDiff = timeDiff($start[1], $time, "+");

    $diffArray = explode(":", $timeDiff);

    //>24?
    if ($diffArray[0]>=24) {
        $diffArray[0]-=24;
        $start[0] = date("Y-m-d", strtotime($start[0]." +1 day"));
    }

    return $start[0]." ".implode(":", $diffArray);
}

function clearDiacritics($string)
{
    return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_COMPAT, 'UTF-8'));
}
