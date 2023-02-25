<?php

class dataManipulation
{

    private $item;
    private $graduationResults = array();
    private $fillArray;
    private $subjectsArray = array();
    private $chosenUniversity;
    private $basicPoint = 0;
    private $extraPoint = 0;

    function __construct($item) {
        $this->item = $item;
    }

    /**
     * get graduationResults from $this->item
     */
    function getGraduationResults () {
        if (is_array($this->item)) {
            $keys = array_keys($this->item);
            $values = array_values($this->item);
            $key = array_search('erettsegi-eredmenyek', $keys);
            if ($key !== false) {
                return $values[$key];
            }
            return false;
        }
        return false;
    }

    /**
     * get graduationResults from $this->item
     */
    function getChosenSpecialisation() {
        if (is_array($this->item)) {
            $keys = array_keys($this->item);
            $values = array_values($this->item);
            $key = array_search('valasztott-szak', $keys);
            if ($key !== false) {
                return $values[$key];
            }
            return false;
        }
        return false;
    }

    /**
     * fill array of subject of contact
     */
    function fillSubjectsArray() {
        foreach($this->graduationResults as $graduationResult) {
            array_push($this->subjectsArray, $graduationResult["nev"]);
        }
    }

    /**
     * audit mandatory subjects
     *
     * @return bool
     */
    function examAudit() {
        foreach( $this->fillArray->compulsoryExaminations as $compulsoryExamination) {
            if (array_search($compulsoryExamination, $this->subjectsArray) === false) {
                echo 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt' . "\n";
                return false;
            }
        }
        return true;
    }

    /**
     * the choice of university control
     *
     * @return false|mixed
     */
    function getChosenUniversity() {
        $chosenSpecialisation = $this->getChosenSpecialisation();
        foreach($this->fillArray->university as $university) {
            if ($university['egyetem'] === $chosenSpecialisation['egyetem'] &&
                $university['kar'] === $chosenSpecialisation['kar'] &&
                $university['szak'] === $chosenSpecialisation['szak']) {
                $this->chosenUniversity = $university;
                return $this->chosenUniversity;
            }
        }
        return false;
    }

    /**
     * basic points reading
     *
     * @param $subject
     *
     * @return int
     */
    function getResult($subject) {
        foreach ($this->graduationResults as $graduationResult) {
            if ($graduationResult['nev'] === $subject) {
                return intval($graduationResult['eredmeny']);
            }
        }
        return 0;
    }

    /**
     * the results are greater than this 20%
     *
     * @return bool
     */
    function resultMoreThen20() {
        foreach($this->graduationResults as $graduationResult) {
            if( intVal($graduationResult['eredmeny']) < 20 ) {
                echo 'hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt' . "\n";
                return false;
            }
        }
        return true;
    }

    /**
     * optional subject control
     *
     * @return bool
     */
    function optionalSubject() {
        foreach ( $this->chosenUniversity['tárgy']['választható'] as $subj) {
            if(array_search($subj, $this->subjectsArray) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * optional subject points
     *
     * @return int
     */
    function optionalPoints() {
        $point = 0;
        foreach($this->chosenUniversity['tárgy']['választható'] as $subj) {
            foreach($this->graduationResults as $graduationResult) {
                if ( $subj === $graduationResult['nev']) {
                    if ($point < intVal($graduationResult['eredmeny'])) {
                        $point = intVal($graduationResult['eredmeny']);
                    }
                }
            }
        }
        return $point;
    }

    /**
     * advanced points
     *
     * @return int
     */
    function advancedLevel() {
        $point = 0;
        foreach($this->graduationResults as $graduationResult) {
            if($graduationResult['tipus'] === 'emelt') {
                $point += 50;
                if ($point >= 100) {
                    return 100;
                }
            }
        }
        return $point;
    }

    /**
     * language exam points
     *
     * @param $point
     *
     * @return int|mixed
     */
    function languageExam($point) {
        $keys = array_keys($this->item);
        $values = array_values($this->item);
        $key = array_search('tobbletpontok', $keys);
        if ($key !== false) {
            $extraPointsArray = $values[$key];
            $languagePointArray = array();
            foreach ($extraPointsArray as $extraPoint) {
                if ($extraPoint['kategoria'] == 'Nyelvvizsga' && $extraPoint['tipus'] == 'B2' ) {
                    array_push($languagePointArray, ['name' => $extraPoint['nyelv'], 'value' => 28]);
                }
                if ($extraPoint['kategoria'] == 'Nyelvvizsga' && $extraPoint['tipus'] == 'C1' ) {
                    if (count($languagePointArray) > 0) {
                        $tf = true;
                        foreach($languagePointArray as $la) {
                            if ($la['name'] == $extraPoint['nyelv']) {
                               $la['value'] = 40;
                               $tf = false;
                            }
                        }
                        if ($tf) {
                            array_push($languagePointArray, ['name' => $extraPoint['nyelv'], 'value' => 40]);
                        }
                    } else {
                        array_push($languagePointArray, ['name' => $extraPoint['nyelv'], 'value' => 40]);
                    }
                }
            }
        }
        foreach ($languagePointArray as $la) {
            $point += $la['value'];
            if ($point >= 100) {
                return 100;
            }
        }

        return $point;
    }


    /**
     * extra ponits calculation
     *
     * @return int
     */
    function extraPoints() {
        $point = $this->advancedLevel();
        if ($point == 100) {
            return 100;
        }

        return $this->languageExam($point);;
    }

    /**
     * Basic point calculation
     */
    function basicPointsCalculation($fillArray) {
        $this->fillArray = $fillArray;
        $this->graduationResults = $this->getGraduationResults();
        if($this->resultMoreThen20() !== false) {
            if ($this->graduationResults !== false) {
                $this->fillSubjectsArray();
                if ($this->examAudit()) {
                    if($this->getChosenUniversity()) {
                        if ( array_search($this->chosenUniversity['tárgy']['kötelező'], $this->subjectsArray) !== false) {
                            $this->basicPoint += $this->getResult($this->chosenUniversity['tárgy']['kötelező']);
                            if ($this->optionalSubject()) {
                                $this->basicPoint += $this->optionalPoints();
                            }
                            $this->basicPoint = $this->basicPoint * 2;
                            $this->extraPoint = $this->extraPoints();
                        }
                    }
                }
            }
        }
        if (($this->basicPoint + $this->extraPoint) > 0) {
            echo ($this->basicPoint + $this->extraPoint) . ' (' . $this->basicPoint . ' alappont + '. $this->extraPoint . ' többletpont)' . "\n";
        }
        return $this->basicPoint + $this->extraPoint;
    }
}