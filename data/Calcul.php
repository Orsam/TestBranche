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
    public static function Hart($a) {
        return $a;
    }
    public static function EvolutionBranche1() {
        return true;
    }
    public static function EvolutionB1($a) {
        return true;
    }
}
class Cdalcul {
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
    public static function Hart($a) {
        return $a;
    }
    public static function Branche1_2() {
        return "DEZ";
    }
    public static function Branche1_2_suite() {
        return "version 1.2";
    }
    public static function Creation121() {
        return false;
    }
    public static function Modification121() {
        return false;
    }
    public static function Continue_Master() {
        return false;
    }
    public static function Branche3() {
        return 'Création Branche 3';
    }
    public static function fezfze() {
        echo "oio";
    }
    public static function FonctionMaster() {
        echo "Master<br>";
    }
    public static function EFZ() {
        echo "bizard";
    }
    public static function FeuilleBranche() {
        echo "Feuille Brache One<br>";
    }
    public static function Date() {
        echo "Date<br>";
    }
    public static function Heure() {
        echo time();
    }
}

