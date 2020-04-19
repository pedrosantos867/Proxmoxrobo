<?php

namespace model;

use System\Cookie;
use System\LanguageDictionary;
use \System\ObjectModel;
use System\Path;
use \System\Tools;

class Languages extends ObjectModel
{
    public static $table = 'languages';



    public static function init($key = 'lang', $languages = null)
    {
        global $lang;


        if (isset($_COOKIE[$key]) && $_COOKIE[$key]) {
            if(!$languages) {
                $language = new Languages($_COOKIE[$key]);
                if ($language->isLoadedObject()) {
                    $lang = $language;
                }
            } else{

                if (isset($languages[$_COOKIE[$key]])) {
                    $lang = $languages[$_COOKIE[$key]];
                }
            }
        }



    }

    public static function translate($word, $folder, $template, $params=null,  $id_lang = null){


        if($id_lang){
            $Lang = new Languages($id_lang);
            if($Lang->isLoadedObject()){
                $lang = $Lang;
            } else {
                global $lang;
            }
        } else {
            global $lang;
        }

        if (_TRANSLATE_MODE_ENABLE_ && isset($lang) ) {

            if($lang->id >= 1) {
                $languageDB = new LanguageDictionary($lang->iso_code, $folder, $template);

                if (($languageDB->{$word}) === null) {
                    $languageDB->set($word, '');
                    $languageDB->save();


                }
            }
        }


        if (isset($lang) && $lang->id >1) {
            $languageDB = new LanguageDictionary($lang->iso_code, $folder, $template);

            $word = ($languageDB->{$word} ? $languageDB->{$word} : $word);

        }


        if (isset($params) && is_array($params)) {
                foreach ($params as $key => $value) {
                    $word = str_ireplace('%' . $key, $value, $word);
                }
        }


        //{4|word|words}
        $matches = array();
        if (preg_match_all('/(?<=\{)[^\]]*(?=\})/', $word, $matches)) {
            foreach ($matches[0] as $match) {
                //echo ($match) . '----';
                $array = explode('|', $match);
                $c     = $array[0];

                unset($array[0]);
                //   print_r(array_values( $array));
                $w = Tools::getWord($c, array_values($array));

                $word = preg_replace('/((?=\{)[^\]]*\})/', $w, $word, 1);

            }
        }

        return $word;
    }

    public static function get($key){
        return Cookie::get($key);
    }

    public static function set($key = 'lang', $id_lang = 0, $force = false){

            if ($id_lang == '0') {
                setcookie($key, 0, time() + 3600 * 24 * 30, '/');
            } else {
                if(!$force) {
                    $lang = new Languages($id_lang);
                    if ($lang->id) {
                        setcookie($key, $lang->id, time() + 360000 * 24 * 30, '/');
                    }
                } else{
                    setcookie($key, $id_lang, time() + 360000 * 24 * 30, '/');
                }
            }


    }

    public function remove(){

            unlink(Path::getRoot('template/front/default/i18n/' . $this->iso_code . '.lang'));
            unlink(Path::getRoot('template/admin/default/i18n/'.$this->iso_code.'.lang'));
            unlink(Path::getRoot('template/install/i18n/'.$this->iso_code.'.lang'));

        return parent::remove();
    }
    public function save($id_lang = 0){
        if(parent::save($id_lang)){
            if(!file_exists(Path::getRoot('template/front/default/i18n/'.$this->iso_code.'.lang'))) {
                file_put_contents( Path::getRoot('template/front/default/i18n/' . $this->iso_code . '.lang'), '{}');
            }
            if(!file_exists(Path::getRoot('template/admin/default/i18n/'.$this->iso_code.'.lang'))) {
                file_put_contents(Path::getRoot('template/admin/default/i18n/'.$this->iso_code.'.lang'),'{}');
            }
            if(!file_exists(Path::getRoot('template/install/i18n/'.$this->iso_code.'.lang'))) {
                file_put_contents( Path::getRoot('template/install/i18n/'.$this->iso_code.'.lang'),'{}');
            }
        }


    }




} 