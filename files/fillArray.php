<?php


class fillArray
{
    public $compulsoryExaminations = [];
    public $university = [];

    function __construct() {
        $this->fillCompulsoryExaminations();
    }


    /**
     * Fill the compulsoryExaminations array
     */
    function fillCompulsoryExaminations() {
        array_push($this->compulsoryExaminations, 'magyar nyelv és irodalom');
        array_push($this->compulsoryExaminations, 'történelem');
        array_push($this->compulsoryExaminations, 'matematika');
    }

    function fillUniversity($university) {
        array_push($this->university, $university);
    }
    /**
     * Get compulsoryExaminations array
     *
     * @return array
     */
    function getcompulsoryExaminations() {
        return $this->compulsoryExaminations;
    }

}