<?php
/**
 * Copyright (C) MIKO LLC - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Nikolay Beketov, 6 2018
 *
 */

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\TextArea;
use \Phalcon\Forms\Element\Password;



class GeneralSettingsEditForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        foreach ($options as $key=>$value){
            switch ($key){

                case "SIPPort":
                case "SIPDefaultExpiry":
                case "SIPMinExpiry":
                case "SIPMaxExpiry":
                case "RTPPortFrom":
                case "RTPPortTo":
                case "IAXPort":
                case "AMIPort":
                case "AJAMPort":
				case "AJAMPortTLS":
                case "SSHPort":
                case "WEBPort":
                case "WEBHTTPSPort":
                case "PBXCallParkingExt":
                case "PBXCallParkingStartSlot":
                case "PBXCallParkingEndSlot":
                case "PBXFeatureDigitTimeout":
                case "PBXFeatureAtxferNoAnswerTimeout":
                case "***ALL NUMBERIC ABOVE***":
                    $this->add(new Numeric($key,['value'=>$value]));
                    break;
                case "SSHPassword":
	                $this->add( new Password( $key, [ 'value' => $value ] ) );
	                $this->add( new Password( 'SSHPasswordRepeat',
		                [ 'value' => $value ] ) );
	                break;
	            case "WebAdminPassword":
		            $this->add(new Password($key,[ 'value' =>$value]));
		            $this->add( new Password( 'WebAdminPasswordRepeat',
			            [ 'value' => $value ] ) );
		            break;
                case "Description":
                $this->add(new TextArea($key,['value'=>$value, "rows"=>2]));
                break;
                case "SSHAuthorizedKeys":
	            case "SSHecdsaKey":
	            case "SSHRsaKey":
	            case "SSHDssKey":
	            case "WEBHTTPSPublicKey":
                case "WEBHTTPSPrivateKey":
                case "***ALL TEXTAREA ABOVE***":
	                $rows = max( round( strlen( $value ) / 95 ), 2 );
	                $this->add( new TextArea( $key,
		                [ 'value' => $value, "rows" => $rows ] ) );
                    break;
                case "PBXLanguage":
                    $language= new Select($key,
                        array(
                            'ru-ru'=>$this->translation->_("ex_Russian"),
                            'en-en'=>$this->translation->_("ex_English"),
							'de-de'=>$this->translation->_("ex_Deutsch")
                        )
                        , array(
                            'using' => array(
                                'id',
                                'name'
                            ),
                            'value'=>$value,
                            'useEmpty' => false,
                            'class' => 'ui selection dropdown language-select'
                        ));
                    $this->add($language);
                    break;
                case "PBXInternalExtensionLength":
                    $extLength= new Select($key,
                        array(
                            3=>$this->translation->_("gs_ThreeDigthts"),
                            4=>$this->translation->_("gs_FourDigthts"),
                            5=>$this->translation->_("gs_FiveDigthts"),
                            6=>$this->translation->_("gs_SixDigthts"),
                        )
                        , array(
                            'using' => array(
                                'id',
                                'name'
                            ),
                            'value'=>$value,
                            'useEmpty' => false,
                            'class' => 'ui selection dropdown extension-length-select'
                        ));
                    $this->add($extLength);
                    break;
                case "PBXRecordCalls":
                case "AJAMEnabled":
                case "AMIEnabled":
                case "RestartEveryNight":
				case "SendMetrics":
                case "***ALL CHECK BOXES ABOVE***":
                    $cheskarr=array('value'=>null);
                    if ($value) {
                        $cheskarr = array('checked' => 'checked','value'=>null);
                    }
                    $this->add(new Check($key,$cheskarr));
                    break;
                default:
                $this->add(new Text($key,['value'=>$value]));
            }
        }
    }
}