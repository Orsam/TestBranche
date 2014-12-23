<?php
class Calcul {
    public static function Addition($a,$b) {
        return $a+$b;
    }
    public static function Division($a,$b) {
        return $a/$b;
    }
    public static function Soustraction($a,$b) {
        return $a-$b;
    }
    public static function Multiplication($a,$b) {
        return $a*$b;
    }
    public static function Pourcentage($total,$valeur) {
        return ($valeur*100)/$total;
    }
}
