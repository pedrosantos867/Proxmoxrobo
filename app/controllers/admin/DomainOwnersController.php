<?php

namespace admin;

use model\DomainOrder;
use model\DomainOwner;
use System\Tools;

class DomainOwnersController extends FrontController{
    public function actionEditAjax(){

        $this->carcase->import('content', $v = $this->getView('domain/owner/edit.php'));
        $v->owner = new DomainOwner(Tools::rGET('owner_id'));


        $disable_edit = 0;

        if (Tools::rGET('owner_id')) {
            $DomainOrder = new DomainOrder();
            if ($DomainOrder->where('owner_id', Tools::rGET('owner_id'))->where('status', 1)->getRow()) {
                $disable_edit = 1;
            }

        }
        $v->disable_edit = $disable_edit;
        if(Tools::rPOST()) {

            $DomainOwner = new DomainOwner(Tools::rPOST('id'));

            if (Tools::rPOST()) {

                if (!$disable_edit) {
                    $DomainOwner->fio = Tools::rPOST('fio');
                    $DomainOwner->birth_date = date('Y-m-d', strtotime(Tools::rPOST('birth_date')));

                    $mobile_phone = Tools::rPOST('mobile_phone');
                    $mobile_phone = str_replace('(', '', $mobile_phone);
                    $mobile_phone = str_replace(')', '', $mobile_phone);
                    $mobile_phone = str_replace('-', '', $mobile_phone);


                    $DomainOwner->mobile_phone = $mobile_phone;
                    $DomainOwner->type = Tools::rPOST('type');
                    $DomainOwner->country = Tools::rPOST('country');

                    if (Tools::rPOST('type') == 2) {

                        $country_code = strtolower(Tools::rPOST('country'));

                        $DomainOwner->organization_name = Tools::rPOST('organization_name_' . $country_code);

                        $DomainOwner->address = Tools::rPOST('organization_address_' . $country_code);
                        $DomainOwner->organization_postal_address = Tools::rPOST('organization_postal_address_' . $country_code);

                        $DomainOwner->organization_inn = Tools::rPOST('organization_inn_' . $country_code);
                        $DomainOwner->organization_edrpou = Tools::rPOST('organization_edrpou_' . $country_code);
                        $DomainOwner->organization_bank = Tools::rPOST('organization_bank_' . $country_code);
                        $DomainOwner->organization_rs = Tools::rPOST('organization_rs_' . $country_code);
                        $DomainOwner->organization_mfo = Tools::rPOST('organization_mfo_' . $country_code);
                        $DomainOwner->organization_ogrn = Tools::rPOST('organization_ogrn_' . $country_code);
                        $DomainOwner->city = Tools::rPOST('city_' . $country_code);


                        $fax = Tools::rPOST('organization_fax_' . $country_code);
                        $fax = str_replace('(', '', $fax);
                        $fax = str_replace(')', '', $fax);
                        $fax = str_replace('-', '', $fax);
                        $DomainOwner->fax = $fax;

                        $DomainOwner->email = Tools::rPOST('organization_email_' . $country_code);
                        $DomainOwner->zip_code = Tools::rPOST('organization_zip_code_' . $country_code);

                        $phone = Tools::rPOST('organization_phone_' . $country_code);
                        $phone = str_replace('(', '', $phone);
                        $phone = str_replace(')', '', $phone);
                        $phone = str_replace('-', '', $phone);
                        $DomainOwner->phone = $phone;

                    } else {

                        $DomainOwner->region = Tools::rPOST('region');
                        $DomainOwner->zip_code = Tools::rPOST('zip_code');
                        $DomainOwner->city = Tools::rPOST('city');
                        $DomainOwner->address = Tools::rPOST('address');


                        $phone = Tools::rPOST('phone');
                        $phone = str_replace('(', '', $phone);
                        $phone = str_replace(')', '', $phone);
                        $phone = str_replace('-', '', $phone);

                        $DomainOwner->phone = $phone;
                        $DomainOwner->email = Tools::rPOST('email');
                        $DomainOwner->passport = Tools::rPOST('passport');
                        $DomainOwner->passport_issued = Tools::rPOST('passport_issued');
                        $DomainOwner->passport_date = date('Y-m-d', strtotime(Tools::rPOST('passport_date')));

                        $fax = Tools::rPOST('fax');
                        $fax = str_replace('(', '', $fax);
                        $fax = str_replace(')', '', $fax);
                        $fax = str_replace('-', '', $fax);

                        $DomainOwner->fax = $fax;

                        $DomainOwner->inn = Tools::rPOST('inn');
                    }



                }

                if ($disable_edit && !$DomainOwner->fax) {
                    $DomainOwner->fax = Tools::rPOST('fax');
                }

                if ($disable_edit && !$DomainOwner->inn) {
                    $DomainOwner->inn = Tools::rPOST('inn');
                }

                $DomainOwner->save();
                $this->returnAjaxAnswer(1, 'Владелец успешно сохранен');

            }

            // echo json_encode($DomainOwner->where('client_id', $this->client->id)->getRows());
        }


    }
}