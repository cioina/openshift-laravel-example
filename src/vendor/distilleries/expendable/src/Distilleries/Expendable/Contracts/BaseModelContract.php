<?php namespace Distilleries\Expendable\Contracts;

interface BaseModelContract {

    public static function getChoice();
    
    public function scopeSearch($query, $searchQuery);

    public function getAllColumnsNames();

    public function scopeBetweenCreate($query, $start, $end);

    public function scopeBetweenUpdate($query, $start, $end);

    public static function getAllColumnsNamesStatic();

    public static function getTableNameStatic();

    public static function excelFileExists();

    public static function exportAll();

    public static function truncateAll();

    public static function importAll();

}