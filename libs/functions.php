<?php

function timeDiff($start, $end)
{
    if ($end >= $start) {

        $start = explode(":", $start);
        $end = explode(":", $end);

        if (is_array($start) && !empty($start) && is_array($end) && !empty($end)) {

            $spaces = count($start)-1;
            $total = array();
            $carrier = 0;
            for ($i = $spaces; $i >= 0; $i--) {
                $diff = ($end[$i] - $start[$i]) + $carrier;

                //Positive carrier (impossible on a diff)
                if ($diff >= 60) {
                    $carrier = 1;
                    $diff = $diff - 60;
                //Negative carrier
                } elseif ($diff < 0) {
                    $carrier = -1;
                    $diff = abs($diff);
                } else {
                    $carrier = 0;
                }

                $total[$i] = str_pad($diff, 2, "0", STR_PAD_LEFT);
            }

            return implode(":", array_reverse($total));
        }
    }
}
