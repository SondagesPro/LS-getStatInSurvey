<?php
/**
 * getStatInSurvey Plugin for LimeSurvey
 * Shows some statistics from previous answers
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2015-2022 Denis Chenu <http://sondages.pro>
 * @copyright 2021 Limesurvey GMBH <https://limesurvey.org>
 * @copyright 2015-2016 DareDo SA <http://www.daredo.net/>
 * @copyright 2016 Update France - Terrain d'études <http://www.updatefrance.fr/>
 * @license GPL v3
 * @version 2.3.3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 */
class getStatInSurvey extends PluginBase {

    protected $storage = 'DbStorage';
    static protected $name = 'getStatInSurvey';
    static protected $description = 'Get mean, percentage and least filled of answers during survey.';

    /**
     * @var string sDebugWhere
     */
    private $sDebugWhere = "";
    /**
     * @var string sDebugWhere
     */
    private $iSurveyId = 0;

    public function init() {
        // @todo $this->subscribe('beforeSurveySettings');
        // @todo $this->subscribe('newSurveySettings');
        // @todo $this->subscribe('afterSurveyComplete');
        $this->subscribe('beforeQuestionRender');
    }

    /**
     * This event is fired by the administration panel to gather extra settings
     * available for a survey.
     * The plugin should return setting meta data.
     * @param PluginEvent $event
     */
    public function beforeSurveySettings()
    {
        if (!$this->getEvent()) {
            throw new CHttpException(403);
        }
        $oEvent = $this->event;
        $this->iSurveyId=$oEvent->get('survey');
    }
    /**
    * Save the settings
    */
    public function newSurveySettings()
    {
        if (!$this->getEvent()) {
            throw new CHttpException(403);
        }
        $oEvent = $this->event;
        $this->iSurveyId=$oEvent->get('survey');
    }

    /**
    * Do something at end of survey
    */
    public function afterSurveyComplete()
    {
        if (!$this->getEvent()) {
            throw new CHttpException(403);
        }
        $oEvent=$this->getEvent();
        $this->iSurveyId=$oEvent->get('surveyId');
        $iResponseId=$oEvent->get('responseId');
        if($this->iSurveyId && $iResponseId)
        {
            $oSurvey=Survey::model()->findByPk($this->iSurveyId);
            $this->sDebugWhere="survey complete";
        }

    }
    /**
    * Do something at start of survey
    * Test if session exist, if yes : find if question is set, if not : create token and fill session
    */
    public function beforeQuestionRender()
    {
        if (!$this->getEvent()) {
            throw new CHttpException(403);
        }
        $oEvent=$this->getEvent();
        $this->iSurveyId=$oEvent->get('surveyId');
        $oSurvey=Survey::model()->findByPk($this->iSurveyId);
        if($oSurvey && $oSurvey->active=="Y")
        {
            $this->sDebugWhere = "question {$oEvent->get("qid")} ({$oEvent->get("code")})";
            $qid = $oEvent->get("qid");
            if(intval(App()->getConfig('versionnumber')) < 4) {
                $oQuestion = Question::model()->find("qid = :qid and language = :language",array(":qid"=>$qid,":language" => App()->getLanguage()));
            } else {
                $oQuestion = QuestionL10n::model()->find("qid = :qid and language = :language",array(":qid"=>$qid,":language" => App()->getLanguage()));
            }
            $textReplaced = $this->doReplacement($oQuestion->question,$qid);
            if(!is_null($textReplaced)) {
                $oEvent->set('text',$textReplaced);
            }
            $questionhelpReplaced = $this->doReplacement($oQuestion->help,$qid);
            if(!is_null($questionhelpReplaced)) {
                $oEvent->set('questionhelp',$questionhelpReplaced); /* pre 3.0 version */
                $oEvent->set('help',$questionhelpReplaced); /* 3.0 and version */
            }
            /* in 3.X and up Expression manager already happen */
            /* in 5.X : must disable for some question , and can disble for all single question*/
            if(!in_array($oEvent->get('type'), ['|','S','T','U','H'])) {
                $answersReplaced = $this->doReplacement($oEvent->get('answers'));
                if(!is_null($answersReplaced)) {
                    $oEvent->set('answers',$answersReplaced);
                }
            }
        }
    }

    /**
     * Get the value needed
     * $sMatch : the string to replace
    */
    private function getValue($sMatch)
    {
        $aMatch=explode(".",$sMatch);
        if(count($aMatch)>=2 )
        {
            $sQCode=$aMatch[0];
            $oQuestion=Question::model()->find("sid=:sid AND title=:title and parent_qid=0",array(":sid"=>$this->iSurveyId,":title"=>$sQCode));
            if(!$oQuestion) {
                return $this->_logUsage("Invalid question code : {$sQCode} in ".$this->sDebugWhere);
            }
            $sType=$aMatch[1];
            $sValue=(count($aMatch)>=3) ? $aMatch[2] : null;
            switch ($sType)
            {
                case 'moyenne':
                case 'moy':
                case 'moy2':
                case 'mean':
                case 'mean0':
                case 'mean2':
                    switch ($oQuestion->type) {
                        case "5":
                        case "L":
                        case "!":
                        case "O":
                        case "N":
                        case "*":
                        case "S":
                            return $this->getAverage($this->iSurveyId."X".$oQuestion->gid."X".$oQuestion->qid,$sType);
                        default:
                            return $this->_logUsage("{$sMatch} : Invalid question type : {$oQuestion->type} in {$this->sDebugWhere}");
                    }
                    break;
                case 'pourcent':
                case 'percent':
                case 'pc':
                    switch ($oQuestion->type) {
                        case "5":
                        case "L":
                        case "!":
                        case "O":
                        case "I":
                        case "D":
                        case "N":
                        case "G":
                        case "Y":
                        case "*":
                        case "S":
                            return $this->getPercentage($this->iSurveyId."X".$oQuestion->gid."X".$oQuestion->qid, $sValue,$sType);
                        default:
                            return $this->_logUsage("{$sMatch} Invalid question type : {$oQuestion->type} in {$this->sDebugWhere}");
                    }
                    break;
                case 'nb':
                    switch ($oQuestion->type) {
                        case "5":
                        case "L":
                        case "!":
                        case "O":
                        case "I":
                        case "D":
                        case "N":
                        case "G":
                        case "Y":
                        case "*":
                        case "S":
                            return $this->getCount($this->iSurveyId."X".$oQuestion->gid."X".$oQuestion->qid, $sValue);
                        default:
                            return $this->_logUsage("{$sMatch} : Invalid question type : {$oQuestion->type} in {$this->sDebugWhere}");
                    }
                    break;
                case 'nbnum':
                    switch ($oQuestion->type) {
                        case "5":
                        case "L":
                        case "!":
                        case "O":
                        case "N":
                        case "*":
                        case "S":
                            return $this->getCountNumeric($this->iSurveyId."X".$oQuestion->gid."X".$oQuestion->qid, $sValue);
                        default:
                            return $this->_logUsage("{$sMatch} : Invalid question type : {$oQuestion->type} in {$this->sDebugWhere}");
                    }
                    break;
                case 'leastfilled':
                    switch ($oQuestion->type) {
                        case "5":
                        case "L":
                        case "!":
                        case "O":
                            return $this->getLeastFilled($oQuestion);
                        default:
                            return $this->_logUsage("{$sMatch} : Invalid question type : {$oQuestion->type} in {$this->sDebugWhere}");
                    }
                    break;
                default:
                    return $this->_logUsage("Unknow type : {$oQuestion->type} in ".$this->sDebugWhere);
                    return;

            }
        }
    }
    /**
     * Get the moyenne for a numeric question type
     * @param : $sCode : question title
     */
    private function getAverage($sColumn,$sType="moyenne")
    {
        $aAverage=array(); // Go to cache ?
        if(isset($aAverage[$sColumn]))
            return $aAverage[$sColumn];
        $sQuotedColumn=Yii::app()->db->quoteColumnName($sColumn);
        $iTotal=$this->getCountNumeric($sColumn);
        $oCriteria = new CDbCriteria;
        $oCriteria->select="SUM({$sQuotedColumn})";
        $oCriteria->condition="submitdate IS NOT NULL";
        $oCriteria->addCondition("concat('',{$sQuotedColumn} * 1) = {$sQuotedColumn}");
        $iSum = Yii::app()->db->getCommandBuilder()->createFindCommand(SurveyDynamic::model($this->iSurveyId)->getTableSchema(),$oCriteria)->queryScalar();

        if($iTotal > 0){
            $aAverage[$sColumn]=$iSum/$iTotal;
        } else {
            $aAverage[$sColumn]="";
            return $aAverage[$sColumn];
        }
        
        switch ($sType) {
            case "moy":
            case "mean0":
                return round($aAverage[$sColumn]);
            case "moy2":
            case "mean2":
                return round($aAverage[$sColumn]*100)/100;
            case "moyenne":
            case "mean":
            default:
                return $aAverage[$sColumn];
        }
        // return is done
    }
    /**
     * Get the least filled response
     * @param : $oQuestion : the question object
     */
    private function getLeastFilled($oQuestion)
    {
        // get all answer codes then getcount on all of them
        $aAnswersFilled=array();
        foreach($oQuestion->answers as $key => $value) {
            $aAnswersFilled[$value['code']]=$this->getCount($this->iSurveyId."X".$oQuestion->gid."X".$oQuestion->qid,$value['code']);
        }
        if(!empty($aAnswersFilled)) {
            //return least filled code
            asort($aAnswersFilled);
            return key($aAnswersFilled);
        }
        return "";
    }
    private function getPercentage($sColumn,$sValue=null,$sType="pc")
    {
        //More easy
        $aPercentage=array(); // Go to cache : but need value for cache

        $sQuotedColumn=Yii::app()->db->quoteColumnName($sColumn);
        if(is_null($sValue))
        {
            $sValue=(isset(Yii::app()->session['survey_'.$this->iSurveyId][$sColumn]) && Yii::app()->session['survey_'.$this->iSurveyId]!=="") ? Yii::app()->session['survey_'.$this->iSurveyId][$sColumn] : null;
        }
        if(is_null($sValue))
        {
            return "";
        }
        if(isset($aPercentage[$sColumn][$sValue]))
            return $aPercentage[$sColumn][$sValue];

        $iTotal=$this->getCount($sColumn);
        $oCriteria = new CDbCriteria;
        $oCriteria->condition="submitdate IS NOT NULL";
        $oCriteria->compare($sQuotedColumn,$sValue);

        $iCount=intval(SurveyDynamic::model($this->iSurveyId)->count($oCriteria));

        if(!isset($aPercentage[$sColumn])) {
            $aPercentage[$sColumn]=array();
        }
        if($iTotal > 0) {
            $aPercentage[$sColumn][$sValue]=$iCount/$iTotal;
        } else {
            $aPercentage[$sColumn][$sValue]="";
            return $aPercentage[$sColumn][$sValue];
        }
        switch ($sType) {
            case "pc":
                return round($aPercentage[$sColumn][$sValue]*100);
            case "pourcent":
            case "percent":
            default:
                return $aPercentage[$sColumn][$sValue]*100;
        }
        // return is done
    }
    /**
     * Get the count of answered for a numeric question type (only numeric answers)
     * @param : $sCode : question title
     * @return integer
     */
    private function getCountNumeric($sColumn)
    {
        $aCountNumeric=array(); // Go to cache ?
        if(isset($aCountNumeric[$sColumn])) {
            return $aCountNumeric[$sColumn];
        }
        $sQuotedColumn=Yii::app()->db->quoteColumnName($sColumn);
        $aCountNumeric[$sColumn]=(int) SurveyDynamic::model($this->iSurveyId)->count("submitdate IS NOT NULL AND concat('',{$sQuotedColumn} * 1) = {$sQuotedColumn}");
        return $aCountNumeric[$sColumn];
    }
    /**
     * Get the count for a any question type (answered)
     * @param : $sCode : question title
     * @return integer
     */
    private function getCount($sColumn,$sValue=null)
    {
        $aCount=array(); // Go to cache ?
        if(isset($aCount[$sColumn][$sValue])) {
            return $aCount[$sColumn][$sValue];
        }
        $sQuotedColumn=Yii::app()->db->quoteColumnName($sColumn);
        $oCriteria = new CDbCriteria;
        $oCriteria->condition="submitdate IS NOT NULL";
        $oCriteria->addCondition("{$sQuotedColumn} IS NOT NULL");
        if(!is_null($sValue)) {
            $oCriteria->compare($sQuotedColumn,$sValue);
        }
        $aCount[$sColumn][$sValue]=intval(SurveyDynamic::model($this->iSurveyId)->count($oCriteria));
        return $aCount[$sColumn][$sValue];
    }
    /**
     * Replace specific string by value
     * @param string $string to replace
     * @param integer $qid
     * @return string|null
     */
    private function doReplacement($string, $qid = null)
    {
        $iCount=preg_match_all ('/\[([a-zA-Z0-9\.\-]*?)\]/',$string,$aMatches);
        if(!$iCount) {
            return null;
        }
        $aMatches=array_unique($aMatches[1]);
        $aReplace=$aQuoteReplace=array();
        foreach($aMatches as $sMatch)
        {
            $sValue=$this->getValue($sMatch);
            if(null!==$sValue)
            {
                $aReplace["[".$sMatch."]"]=$sValue;
                if(is_numeric($sValue))
                    $aQuoteReplace["\"[".$sMatch."]\""]=$sValue;
                else
                    $aQuoteReplace["\"[".$sMatch."]\""]='"'.$sValue.'"';

            }
        }
        if(!empty($aQuoteReplace))
        {
            $string=str_replace(array_keys($aQuoteReplace),$aQuoteReplace,$string);
        }
        if(!empty($aReplace))
        {
            $string=str_replace(array_keys($aReplace),$aReplace,$string);
        }

        /* Current version */
        if(version_compare(App()->getConfig('versionnumber'), "3.6.1", ">")) {
            return LimeExpressionManager::ProcessStepString($string);
        }
        if(!$qid || intval(App()->getConfig('versionnumber')) < 3) {
            return $string;
        }
        /* Pre 3.X version */
        return LimeExpressionManager::ProcessString($string, $qid, null, 3, 1, false, true, false);
    }

    /**
     * Logging uage error system
     * @param string to log
     * @return null|string
     */
    private function _logUsage($string) {
        Yii::log($string,'warning',"application.plugins.getStatInSurvey"); // Log as warning or as error ? Warning for adin user more than an error.
        if(Permission::model()->hasSurveyPermission($this->iSurveyId, 'surveycontent', 'update')) {
            return "[{$string}]"; // If user can edit survey : allow to see the error (except with expression …)
        }
        return;
    }
}
