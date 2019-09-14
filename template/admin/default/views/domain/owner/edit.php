<div class="loaded-block modal-800-width">
    <?= $_->JS('validator.js') ?>

    <script>
        $(function () {
            $('form').validate({messages: validate_messages});
        })
    </script>
    <? if (isset($ajax)) { ?>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Добавить владельца')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link($request) ?>"
                          method="post" class="ajax-form">
                        <? if (isset($ajax)) { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                        <? if (isset($owner->id)) { ?>
                            <input type="hidden" name="id" value="<?= $owner->id ?>">
                        <? } ?>

                        <? if ($disable_edit) { ?>
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                                <span class="glyphicon glyphicon-alert"></span>
                                <?=$_->l('Этот владелец используется! Редактирование запрещено!')?>
                            </div>
                        <? } ?>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label for="fio"><?= $_->l('Ф.И.О') ?></label>
                                <input type="text" class="form-control" name="fio" data-validate="fio"
                                       value="<?= $owner->fio ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?> >
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="exampleInputEmail1"><?= $_->l('Мобильный телефон') ?></label>
                                <input type="text" class="form-control" name="mobile_phone" data-validate="phone_new"
                                       data-validate-def=""
                                       value="<?= $owner->mobile_phone ? $owner->mobile_phone : $owner->phone ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                            </div>
                            <script>
                                $('input[name=mobile_phone]').inputmask("phone", {
                                    onKeyValidation: function () { //show some metadata in the console
                                        var obj = $(this).inputmask("getmetadata");
                                        if (typeof obj !== 'undefined') {
                                            var cc = (obj['cc']);
                                            $('select[name=country]').val(cc);
                                            $('select[name=country]').trigger('change');
                                        }
                                    }
                                }).on('keyup', function () {
                                    $('input[name=phone]').val($(this).val());
                                });
                            </script>
                            <div class="form-group col-lg-6">
                                <label for="exampleInputEmail1"><?= $_->l('Страна') ?></label>

                                <? $countryList = array(
                                    "AF" => "Afghanistan",
                                    "AL" => "Albania",
                                    "DZ" => "Algeria",
                                    "AS" => "American Samoa",
                                    "AD" => "Andorra",
                                    "AO" => "Angola",
                                    "AI" => "Anguilla",
                                    "AQ" => "Antarctica",
                                    "AG" => "Antigua and Barbuda",
                                    "AR" => "Argentina",
                                    "AM" => "Armenia",
                                    "AW" => "Aruba",
                                    "AU" => "Australia",
                                    "AT" => "Austria",
                                    "AZ" => "Azerbaijan",
                                    "BS" => "Bahamas",
                                    "BH" => "Bahrain",
                                    "BD" => "Bangladesh",
                                    "BB" => "Barbados",
                                    "BY" => "Belarus",
                                    "BE" => "Belgium",
                                    "BZ" => "Belize",
                                    "BJ" => "Benin",
                                    "BM" => "Bermuda",
                                    "BT" => "Bhutan",
                                    "BO" => "Bolivia",
                                    "BA" => "Bosnia and Herzegovina",
                                    "BW" => "Botswana",
                                    "BV" => "Bouvet Island",
                                    "BR" => "Brazil",
                                    "BQ" => "British Antarctic Territory",
                                    "IO" => "British Indian Ocean Territory",
                                    "VG" => "British Virgin Islands",
                                    "BN" => "Brunei",
                                    "BG" => "Bulgaria",
                                    "BF" => "Burkina Faso",
                                    "BI" => "Burundi",
                                    "KH" => "Cambodia",
                                    "CM" => "Cameroon",
                                    "CA" => "Canada",
                                    "CT" => "Canton and Enderbury Islands",
                                    "CV" => "Cape Verde",
                                    "KY" => "Cayman Islands",
                                    "CF" => "Central African Republic",
                                    "TD" => "Chad",
                                    "CL" => "Chile",
                                    "CN" => "China",
                                    "CX" => "Christmas Island",
                                    "CC" => "Cocos [Keeling] Islands",
                                    "CO" => "Colombia",
                                    "KM" => "Comoros",
                                    "CG" => "Congo - Brazzaville",
                                    "CD" => "Congo - Kinshasa",
                                    "CK" => "Cook Islands",
                                    "CR" => "Costa Rica",
                                    "HR" => "Croatia",
                                    "CU" => "Cuba",
                                    "CY" => "Cyprus",
                                    "CZ" => "Czech Republic",
                                    "CI" => "Côte d’Ivoire",
                                    "DK" => "Denmark",
                                    "DJ" => "Djibouti",
                                    "DM" => "Dominica",
                                    "DO" => "Dominican Republic",
                                    "NQ" => "Dronning Maud Land",
                                    "DD" => "East Germany",
                                    "EC" => "Ecuador",
                                    "EG" => "Egypt",
                                    "SV" => "El Salvador",
                                    "GQ" => "Equatorial Guinea",
                                    "ER" => "Eritrea",
                                    "EE" => "Estonia",
                                    "ET" => "Ethiopia",
                                    "FK" => "Falkland Islands",
                                    "FO" => "Faroe Islands",
                                    "FJ" => "Fiji",
                                    "FI" => "Finland",
                                    "FR" => "France",
                                    "GF" => "French Guiana",
                                    "PF" => "French Polynesia",
                                    "TF" => "French Southern Territories",
                                    "FQ" => "French Southern and Antarctic Territories",
                                    "GA" => "Gabon",
                                    "GM" => "Gambia",
                                    "GE" => "Georgia",
                                    "DE" => "Germany",
                                    "GH" => "Ghana",
                                    "GI" => "Gibraltar",
                                    "GR" => "Greece",
                                    "GL" => "Greenland",
                                    "GD" => "Grenada",
                                    "GP" => "Guadeloupe",
                                    "GU" => "Guam",
                                    "GT" => "Guatemala",
                                    "GG" => "Guernsey",
                                    "GN" => "Guinea",
                                    "GW" => "Guinea-Bissau",
                                    "GY" => "Guyana",
                                    "HT" => "Haiti",
                                    "HM" => "Heard Island and McDonald Islands",
                                    "HN" => "Honduras",
                                    "HK" => "Hong Kong SAR China",
                                    "HU" => "Hungary",
                                    "IS" => "Iceland",
                                    "IN" => "India",
                                    "ID" => "Indonesia",
                                    "IR" => "Iran",
                                    "IQ" => "Iraq",
                                    "IE" => "Ireland",
                                    "IM" => "Isle of Man",
                                    "IL" => "Israel",
                                    "IT" => "Italy",
                                    "JM" => "Jamaica",
                                    "JP" => "Japan",
                                    "JE" => "Jersey",
                                    "JT" => "Johnston Island",
                                    "JO" => "Jordan",
                                    "KZ" => "Kazakhstan",
                                    "KE" => "Kenya",
                                    "KI" => "Kiribati",
                                    "KW" => "Kuwait",
                                    "KG" => "Kyrgyzstan",
                                    "LA" => "Laos",
                                    "LV" => "Latvia",
                                    "LB" => "Lebanon",
                                    "LS" => "Lesotho",
                                    "LR" => "Liberia",
                                    "LY" => "Libya",
                                    "LI" => "Liechtenstein",
                                    "LT" => "Lithuania",
                                    "LU" => "Luxembourg",
                                    "MO" => "Macau SAR China",
                                    "MK" => "Macedonia",
                                    "MG" => "Madagascar",
                                    "MW" => "Malawi",
                                    "MY" => "Malaysia",
                                    "MV" => "Maldives",
                                    "ML" => "Mali",
                                    "MT" => "Malta",
                                    "MH" => "Marshall Islands",
                                    "MQ" => "Martinique",
                                    "MR" => "Mauritania",
                                    "MU" => "Mauritius",
                                    "YT" => "Mayotte",
                                    "FX" => "Metropolitan France",
                                    "MX" => "Mexico",
                                    "FM" => "Micronesia",
                                    "MI" => "Midway Islands",
                                    "MD" => "Moldova",
                                    "MC" => "Monaco",
                                    "MN" => "Mongolia",
                                    "ME" => "Montenegro",
                                    "MS" => "Montserrat",
                                    "MA" => "Morocco",
                                    "MZ" => "Mozambique",
                                    "MM" => "Myanmar [Burma]",
                                    "NA" => "Namibia",
                                    "NR" => "Nauru",
                                    "NP" => "Nepal",
                                    "NL" => "Netherlands",
                                    "AN" => "Netherlands Antilles",
                                    "NT" => "Neutral Zone",
                                    "NC" => "New Caledonia",
                                    "NZ" => "New Zealand",
                                    "NI" => "Nicaragua",
                                    "NE" => "Niger",
                                    "NG" => "Nigeria",
                                    "NU" => "Niue",
                                    "NF" => "Norfolk Island",
                                    "KP" => "North Korea",
                                    "VD" => "North Vietnam",
                                    "MP" => "Northern Mariana Islands",
                                    "NO" => "Norway",
                                    "OM" => "Oman",
                                    "PC" => "Pacific Islands Trust Territory",
                                    "PK" => "Pakistan",
                                    "PW" => "Palau",
                                    "PS" => "Palestinian Territories",
                                    "PA" => "Panama",
                                    "PZ" => "Panama Canal Zone",
                                    "PG" => "Papua New Guinea",
                                    "PY" => "Paraguay",
                                    "YD" => "People's Democratic Republic of Yemen",
                                    "PE" => "Peru",
                                    "PH" => "Philippines",
                                    "PN" => "Pitcairn Islands",
                                    "PL" => "Poland",
                                    "PT" => "Portugal",
                                    "PR" => "Puerto Rico",
                                    "QA" => "Qatar",
                                    "RO" => "Romania",
                                    "RU" => "Russia",
                                    "RW" => "Rwanda",
                                    "RE" => "Réunion",
                                    "BL" => "Saint Barthélemy",
                                    "SH" => "Saint Helena",
                                    "KN" => "Saint Kitts and Nevis",
                                    "LC" => "Saint Lucia",
                                    "MF" => "Saint Martin",
                                    "PM" => "Saint Pierre and Miquelon",
                                    "VC" => "Saint Vincent and the Grenadines",
                                    "WS" => "Samoa",
                                    "SM" => "San Marino",
                                    "SA" => "Saudi Arabia",
                                    "SN" => "Senegal",
                                    "RS" => "Serbia",
                                    "CS" => "Serbia and Montenegro",
                                    "SC" => "Seychelles",
                                    "SL" => "Sierra Leone",
                                    "SG" => "Singapore",
                                    "SK" => "Slovakia",
                                    "SI" => "Slovenia",
                                    "SB" => "Solomon Islands",
                                    "SO" => "Somalia",
                                    "ZA" => "South Africa",
                                    "GS" => "South Georgia and the South Sandwich Islands",
                                    "KR" => "South Korea",
                                    "ES" => "Spain",
                                    "LK" => "Sri Lanka",
                                    "SD" => "Sudan",
                                    "SR" => "Suriname",
                                    "SJ" => "Svalbard and Jan Mayen",
                                    "SZ" => "Swaziland",
                                    "SE" => "Sweden",
                                    "CH" => "Switzerland",
                                    "SY" => "Syria",
                                    "ST" => "São Tomé and Príncipe",
                                    "TW" => "Taiwan",
                                    "TJ" => "Tajikistan",
                                    "TZ" => "Tanzania",
                                    "TH" => "Thailand",
                                    "TL" => "Timor-Leste",
                                    "TG" => "Togo",
                                    "TK" => "Tokelau",
                                    "TO" => "Tonga",
                                    "TT" => "Trinidad and Tobago",
                                    "TN" => "Tunisia",
                                    "TR" => "Turkey",
                                    "TM" => "Turkmenistan",
                                    "TC" => "Turks and Caicos Islands",
                                    "TV" => "Tuvalu",
                                    "UM" => "U.S. Minor Outlying Islands",
                                    "PU" => "U.S. Miscellaneous Pacific Islands",
                                    "VI" => "U.S. Virgin Islands",
                                    "UG" => "Uganda",
                                    "UA" => "Ukraine",
                                    "SU" => "Union of Soviet Socialist Republics",
                                    "AE" => "United Arab Emirates",
                                    "GB" => "United Kingdom",
                                    "US" => "United States",
                                    "ZZ" => "Unknown or Invalid Region",
                                    "UY" => "Uruguay",
                                    "UZ" => "Uzbekistan",
                                    "VU" => "Vanuatu",
                                    "VA" => "Vatican City",
                                    "VE" => "Venezuela",
                                    "VN" => "Vietnam",
                                    "WK" => "Wake Island",
                                    "WF" => "Wallis and Futuna",
                                    "EH" => "Western Sahara",
                                    "YE" => "Yemen",
                                    "ZM" => "Zambia",
                                    "ZW" => "Zimbabwe",
                                    "AX" => "Åland Islands",
                                ); ?>

                                <select class="form-control" name="country"
                                        data-validate="required" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                    <? foreach ($countryList as $code => $name) { ?>
                                        <option
                                            value="<?= $code ?>" <?= ($owner->country == $code ? 'selected="selected"' : '') ?>><?= $name ?></option>
                                    <? } ?>
                                </select>
                            </div>

                        </div>
                        <div class="row">

                            <div class="form-group col-lg-12">
                                <label for="type"><?= $_->l('Тип') ?></label>
                                <select class="form-control " name="type"
                                        data-validate="required" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                    <option
                                        value="1" <?= ($owner->type == '1' ? 'selected="selected"' : '') ?>><?= $_->l('Частное лицо') ?></option>
                                    <option
                                        value="2" <?= ($owner->type == '2' ? 'selected="selected"' : '') ?>><?= $_->l('Юридическое лицо') ?></option>
                                </select>
                            </div>
                        </div>
                        <? if (!$disable_edit) { ?>
                            <script>
                                $(function () {
                                    $('select[name=country]').on('change', function () {

                                        if ($(this).val() == 'RU' || $(this).val() == 'BY' || $(this).val() == 'UA') {
                                            $('select[name=type]').removeAttr('disabled');
                                            $('select[name=type]').parents('div.form-group').show();
                                            $('select[name=type]').trigger('change');
                                        } else {
                                            $('select[name=type] option[value="1"]').prop('selected', 'selected');
                                            $('select[name=type]').trigger('change');
                                            $('select[name=type]').attr('disabled', 'disabled').parents('div.form-group').hide();
                                        }
                                    });
                                    $('select[name=country]').trigger('change');


                                })

                            </script>
                        <? } ?>
                        <div class="organization-inputs ru">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Название организации') ?></label>
                                    <input type="text" class="form-control" name="organization_name_ru"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Например: ООО Биллинг-системы') ?>"
                                           value="<?= $owner->organization_name ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Город') ?></label>
                                    <input type="text" class="form-control" name="city_ru" data-validate="required"
                                           value="<?= $owner->city ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Юридический адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_address_ru"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Почтовый адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_postal_address_ru"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->organization_postal_address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>


                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('ИНН организации') ?></label>
                                    <input type="text" class="form-control" name="organization_inn_ru"
                                           data-validate="required"
                                           value="<?= $owner->organization_inn ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('КПП') ?></label>
                                    <input type="text" class="form-control" name="organization_edrpou_ru"
                                           data-validate="custom" data-validate-match="^([\d]{9}|-)$"
                                           value="<?= $owner->organization_edrpou ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('ОГРН') ?></label>
                                    <input type="text" class="form-control" name="organization_ogrn_ru"
                                           data-validate="custom" data-validate-match="^([\d]{13}([\d]{2})?|-)$"
                                           value="<?= $owner->organization_ogrn ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('БИК') ?></label>
                                    <input type="text" class="form-control" name="organization_mfo_ru"
                                           data-validate="custom" data-validate="^(\d{8,9}|-)$"
                                           value="<?= $owner->organization_mfo ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Банк') ?></label>
                                    <input type="text" class="form-control" name="organization_bank_ru"
                                           data-validate="required"
                                           value="<?= $owner->organization_bank ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Рассчетный счет') ?></label>
                                    <input type="text" class="form-control" name="organization_rs_ru"
                                           data-validate="custom" data-validate-match="^(\d{20}|-)$"
                                           value="<?= $owner->organization_rs ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="fax"><?= $_->l('Факс') ?></label>
                                    <input type="text" class="form-control" name="organization_fax_ru"
                                           data-validate="phone_new"
                                           data-validate-allow-empty="1" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->fax ?>" <?= ($owner->fax && $disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('E-mail') ?></label>
                                    <input type="text" class="form-control" name="organization_email_ru"
                                           data-validate="email"
                                           value="<?= $owner->email ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Индекс') ?></label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '######'"
                                           name="organization_zip_code_ru" data-validate="required"
                                           value="<?= $owner->zip_code ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Телефон') ?></label>
                                    <input type="text" class="form-control" name="organization_phone_ru"
                                           data-validate="phone_new" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->phone ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                        </div>

                        <div class="organization-inputs by">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Название организации') ?></label>
                                    <input type="text" class="form-control" name="organization_name_by"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Например: ООО Биллинг-системы') ?>"
                                           value="<?= $owner->organization_name ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Город') ?></label>
                                    <input type="text" class="form-control" name="city_by" data-validate="required"
                                           value="<?= $owner->city ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Юридический адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_address_by"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Почтовый адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_postal_address_by"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->organization_postal_address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>


                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('УНП') ?></label>
                                    <input type="text" class="form-control" name="organization_inn_by"
                                           data-validate="required"
                                           value="<?= $owner->organization_inn ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('БИК') ?></label>
                                    <input type="text" class="form-control" name="organization_mfo_by"
                                           data-validate="custom" data-validate="^(\d{8,9}|-)$"
                                           value="<?= $owner->organization_mfo ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Банк') ?></label>
                                    <input type="text" class="form-control" name="organization_bank_by"
                                           data-validate="required"
                                           value="<?= $owner->organization_bank ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Рассчетный счет') ?></label>
                                    <input type="text" class="form-control" name="organization_rs_by"
                                           data-validate="custom" data-validate-match="^(\d{3,20}|-)$"
                                           value="<?= $owner->organization_rs ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Факс') ?></label>
                                    <input type="text" class="form-control" name="organization_fax_by"
                                           data-validate="phone_new"
                                           data-validate-def="" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->fax ?>" <?= ($owner->fax && $disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('E-mail') ?></label>
                                    <input type="text" class="form-control" name="organization_email_by"
                                           data-validate="email"
                                           value="<?= $owner->email ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Индекс') ?></label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '######'"
                                           name="organization_zip_code_by" data-validate="required"
                                           value="<?= $owner->zip_code ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Телефон') ?></label>
                                    <input type="text" class="form-control" name="organization_phone_by"
                                           data-validate="phone_new" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->phone ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                        </div>

                        <div class="organization-inputs ua">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Название организации') ?></label>
                                    <input type="text" class="form-control" name="organization_name_ua"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Например: ООО Биллинг-системы') ?>"
                                           value="<?= $owner->organization_name ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Город') ?></label>
                                    <input type="text" class="form-control" name="city_ua" data-validate="required"
                                           value="<?= $owner->city ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Юридический адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_address_ua"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Почтовый адрес') ?></label>
                                    <input type="text" class="form-control" name="organization_postal_address_ua"
                                           data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->organization_postal_address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>


                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('ЕГРПОУ') ?> (ЄДРПОУ)</label>
                                    <input type="text" class="form-control" name="organization_edrpou_ua"
                                           data-validate="required"
                                           value="<?= $owner->organization_edrpou ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('ИНН') ?> (ІПН)</label>
                                    <input type="text" class="form-control" name="organization_inn_ua"
                                           data-validate="required"
                                           value="<?= $owner->organization_inn ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('МФО') ?></label>
                                    <input type="text" class="form-control" name="organization_mfo_ua"
                                           data-validate="custom" data-validate-match="^(\d{6})$"
                                           value="<?= $owner->organization_mfo ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Банк') ?></label>
                                    <input type="text" class="form-control" name="organization_bank_ua"
                                           data-validate="required"
                                           value="<?= $owner->organization_bank ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>
                            <div class="row">

                                <div class="form-group col-lg-12">
                                    <label for="exampleInputEmail1"><?= $_->l('Рассчетный счет') ?></label>
                                    <input type="text" class="form-control" name="organization_rs_ua"
                                           data-validate="custom" data-validate-match="^(\d{3,30})$"
                                           value="<?= $owner->organization_rs ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Факс') ?></label>
                                    <input type="text" class="form-control" name="organization_fax_ua"
                                           data-validate="phone_new"
                                           data-validate-def="" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->fax ?>" <?= ($owner->fax && $disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('E-mail') ?></label>
                                    <input type="text" class="form-control" name="organization_email_ua"
                                           data-validate="email"
                                           value="<?= $owner->email ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Индекс') ?></label>
                                    <input type="text" class="form-control" data-inputmask="'mask': '#####'"
                                           name="organization_zip_code_ua" data-validate="required"
                                           value="<?= $owner->zip_code ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Телефон') ?></label>
                                    <input type="text" class="form-control" name="organization_phone_ua"
                                           data-validate="phone_new" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->phone ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                        </div>

                        <div class="person-inputs">
                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Индекс') ?></label>
                                    <input type="text" class="form-control" name="zip_code" data-validate="required"
                                           value="<?= $owner->zip_code ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Область') ?></label>
                                    <input type="text" class="form-control" name="region" data-validate="required"
                                           value="<?= $owner->region ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Город') ?></label>
                                    <input type="text" class="form-control" name="city" data-validate="required"
                                           value="<?= $owner->city ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Адрес') ?></label>
                                    <input type="text" class="form-control" name="address" data-validate="custom"
                                           data-validate-match="(.{3,})"
                                           data-validate-message-fail-custom="<?= $_->l('Введите адрес, например: ул. Пушкина, дом 21Г, кв. 1') ?>"
                                           value="<?= $owner->address ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Телефон') ?></label>
                                    <input type="text" class="form-control" name="phone" data-validate="phone_new"
                                           data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->phone ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Факс') ?></label>
                                    <input type="text" class="form-control" name="fax" data-validate="phone_new"
                                           data-validate-allow-empty="1" data-inputmask="'alias': 'phone'"
                                           value="<?= $owner->fax ?>" <?= ($owner->fax && $disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('E-mail') ?></label>
                                    <input type="text" class="form-control" name="email" data-validate="email"
                                           value="<?= $owner->email ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="birth_date"><?= $_->l('Дата рождения') ?></label>
                                    <input type="text" class="form-control" name="birth_date"
                                           value="<?= date('d-m-Y', strtotime($owner->birth_date)) ?>"
                                           data-inputmask="'mask': 'd-m-y'"
                                           data-validate="date_new" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Серия и номер паспорта') ?></label>
                                    <input type="text" class="form-control" name="passport" data-validate="required"
                                           value="<?= $owner->passport ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="exampleInputEmail1"><?= $_->l('Кем выдан паспорт:') ?></label>
                                    <input type="text" class="form-control" name="passport_issued"
                                           data-validate="required"
                                           value="<?= $owner->passport_issued ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="passport_date"><?= $_->l('Дата выдачи паспорта:') ?></label>
                                    <input type="text" class="form-control" name="passport_date"
                                           data-validate="date_new"
                                           data-inputmask="'mask': 'd-m-y'"
                                           value="<?= $owner->passport_date ? date('d-m-Y', strtotime($owner->passport_date)) : '01-01-1980' ?>" <?= ($disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>

                                <div class="form-group col-lg-6 only-for only-for-ru only-for-by">
                                    <label for="inn"><?= $_->l('ИНН:') ?></label>
                                    <input type="text" class="form-control" name="inn" data-validate="required"
                                           value="<?= $owner->inn ? $owner->inn : '' ?>" <?= ($owner->inn && $disable_edit ? 'disabled="disabled"' : '') ?>>
                                </div>
                            </div>
                        </div>

                        <script>


                            $('.organization-inputs').hide();
                            $('select[name=type]').on('change', function () {

                                $('.organization-inputs').hide();

                                var country = $('select[name=country]').val().toLowerCase();
                                var oinputs = $('.organization-inputs.' + country);

                                var pinputs = $('.person-inputs');
                                var type = $(this).val();


                                if (type == 1) {
                                    $(oinputs).hide();
                                    $(pinputs).show();

                                    var only_fields = $('.only-for-' + country);
                                    $('.only-for').hide();
                                    $(only_fields).show();
                                }
                                else if (type == 2) {
                                    $(oinputs).show();
                                    $(pinputs).hide();
                                }

                                $('form').validate({messages: validate_messages});
                            });
                            $('select[name=type]').trigger('change');
                        </script>

                        <script>
                            $('input[name=email]').inputmask({
                                mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
                                greedy: false,
                                onBeforePaste: function (pastedValue, opts) {
                                    pastedValue = pastedValue.toLowerCase();
                                    return pastedValue.replace("mailto:", "");
                                },
                                definitions: {
                                    '*': {
                                        validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                                        cardinality: 1,
                                        casing: "lower"
                                    }
                                }
                            });
                        </script>

                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><span
                                    class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
                            </button>
                        <? } ?>
                    </form>


                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $_->l('Закрыть') ?></button>
                    <button type="button" onclick="$('#ajaxModal form').submit();" class="btn btn-success"><span
                            class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

<? } ?>
</div>