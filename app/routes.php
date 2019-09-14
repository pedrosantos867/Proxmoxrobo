<?php
return array(

    /*API Routes*/
    'api/test' => 'api|test|test',
    'api/checkDomain' => 'api|domain|checkDomain',
    'api/getDomains' => 'api|domain|getDomains',
    'api/createProfile' => 'api|domain|createProfile',
    'api/registerDomain' => 'api|domain|registerDomain',
    /* --- */

    'login'                                                             => 'front|user|login',
    'logout'                                                            => 'front|user|logout',
    'admin'                                                             => 'admin|home',
    'admin/reminder'                                                    => 'admin|auth|reminder',
    'admin/reminder/code/([0-9A-z]{1,})'                                => 'admin|auth|reminder|code=$1',

    'admin/login'                                                       => 'admin|auth|login',
    'admin/login-client/([0-9]{1,})'                                    => 'admin|clients|loginClientFromAdmin|id_client=$1',
    'admin/logout'                                                      => 'admin|auth|logout',

    'admin/plan-property/remove/([0-9]{1,})'                            => 'admin|plans|removeProperty|id_detail=$1',
    'admin/plan-property/add/([0-9]{1,})'                               => 'admin|plans|addProperty|id_plan=$1',
    'admin/plan-property/edit/([0-9]{1,})/([0-9]{1,})'                  => 'admin|plans|addProperty|id_plan=$2|id_property=$1',

    'admin/plan/param/remove/([0-9]{1,})'                               => 'admin|plans|removeParam|id_param=$1',
    'admin/plan/param/add'                                              => 'admin|plans|addParam',
    'admin/plan/param/([0-9]{1,})'                                      => 'admin|plans|editParam|id_param=$1',

    'admin/server/add'                                                  => 'admin|server|edit|id_server=0',
    'admin/server/remove/([0-9]{1,})'                                   => 'admin|server|remove|id_server=$1',
    'admin/server/([0-9]{1,})'                                          => 'admin|server|edit|id_server=$1',

    'admin/server/check/([0-9]{1,})'                                    => 'admin|server|check|id_server=$1',
    'admin/servers'                                                     => 'admin|server|list',
    'admin/plan/params'                                                 => 'admin|plans|paramsList',

    'admin/plan/add'                                                    => 'admin|plans|add',
    'admin/plan/([0-9]{1,})'                                            => 'admin|plans|edit|plan=$1',
    'admin/plan/remove/([0-9]{1,})'                                     => 'admin|plans|remove|plan=$1',
    'admin/plans'                                                       => 'admin|plans',

    'admin/client/remove/([0-9]{1,})'                                   => 'admin|clients|remove|id_user=$1',
    'admin/client/add'                                                  => 'admin|clients|edit|id_user=0',
    'admin/client/info/([0-9]{1,})'                                     => 'admin|clients|info|id_client=$1',
    'admin/client/edit-info/([0-9]{1,})'                                => 'admin|clients|editInfo|id_info=$1',
    'admin/client/edit-balance/([0-9]{1,})'                             => 'admin|clients|editBalance|id_user=$1',
    'admin/client/([0-9]{1,})'                                          => 'admin|clients|edit|id_user=$1',
    'admin/clients'                                                     => 'admin|clients',

    'admin/orders/remove/([0-9]{1,})'                                   => 'admin|orders|remove|order=$1',

    'admin/orders'                                                      => 'admin|orders',
    'admin/domain-orders'                                               => 'admin|domainOrders|list',

    'admin/domain-orders/ns-change'                                           => 'admin|domainOrders|changeNS',
    'admin/domain-orders/prolong'                                             => 'admin|domainOrders|prolong',
    'admin/domain-orders/rereg'                                             => 'admin|domainOrders|rereg',
    'admin/domain-orders/remove'                                              => 'admin|domainOrders|remove',
    'admin/domain-orders/order'                                               => 'admin|domainOrders|order',
    'admin/domain-orders/order/([0-9,]{1,})'                                  => 'admin|domainOrders|order|ids=$1',

    'admin/domain-orders/change-owner'                                 => 'admin|domainOrders|changeOwner',

    'admin/domain-owners/edit'                                          => 'admin|domainOwners|edit',

    'admin/bills'                                                       => 'admin|bills|list',

    'admin/employees'                                                   => 'admin|employees|list',
    'admin/employee/remove/([0-9]{1,})'                                 => 'admin|employees|remove|id_user=$1',
    'admin/employee/([0-9]{1,})'                                        => 'admin|employees|edit|id_user=$1',
    'admin/employee/add'                                                => 'admin|employees|edit',

    'admin/bill/remove/([0-9]{1,})'                                     => 'admin|bills|remove|id_bill=$1',
    'admin/bill/refund/([0-9]{1,})' => 'admin|bills|refund|id_bill=$1',
    'admin/bill/off/([0-9]{1,})'                                        => 'admin|bills|off|id_bill=$1',
    'admin/bill/pay/([0-9]{1,})'                                        => 'admin|bills|pay|id_bill=$1',

    'admin/order/add'                                                   => 'admin|orders|edit|id_order=0',

    'admin/order/([0-9]{1,})'                                           => 'admin|orders|edit|id_order=$1',


    'admin/settings/payments/([0-9A-z]{1,})'                            => 'admin|settings|paymentSetting|system=$1',


    'admin/settings/currencies/setting'                                 => 'admin|settings|currenciesSetting',
    'admin/settings/currencies'                                         => 'admin|settings|currencies',

    'admin/settings/notifications/setting'                             => 'admin|settings|notificationsSetting',


    'admin/settings/language/add'                                       => 'admin|settings|addLanguage',
    'admin/settings/language/remove'                                    => 'admin|settings|removeLanguage',
    'admin/settings/languages/settings'                                 => 'admin|settings|languageSettings',
    'admin/settings/languages/translate-manager'                        => 'admin|settings|translateManager',

    'admin/modules/install'                                    => 'admin|modules|install',
    'admin/modules/uninstall'                                  => 'admin|modules|uninstall',
    'admin/modules/setting/([A-Za-z0-9/]{0,})'                 => 'admin|modules|setting|module=$1',
    'admin/modules'                                           => 'admin|modules|list',



    'admin/settings/currency/([0-9]{1,})'                               => 'admin|settings|editCurrency|id_currency=$1',
    'admin/settings/currency/add'                                       => 'admin|settings|addCurrency',
    'admin/settings/currency/refresh'                                   => 'admin|settings|refreshCurrency',
    'admin/settings/currency/remove/([0-9]{1,})'                        => 'admin|settings|removeCurrency|id_currency=$1',
    'admin/settings/payments'                                           => 'admin|settings|payments',
    'admin/settings/sms-gateway/(.*)'                                   => 'admin|settings|gatewaySetting|system=$1',
    'admin/settings/sms-gateway'                                        => 'admin|settings|smsGateway',
    'admin/settings/license'                                            => 'admin|settings|licenseInfo',
    'admin/settings/notifications'                                      => 'admin|settings|notifications',
    'admin/settings/([A-Za-z0-9/]{0,})'                                 => 'admin|settings|$1',
    'admin/settings'                                                    => 'admin|settings',
    'admin/settings/social-auth' => 'admin|settings|socialAuthSettings',
    'admin/settings/send-text-message' => 'admin|settings|sendTextMessage',



    'admin/tickets'                                                     => 'admin|tickets',
    'admin/ticket/change-status/([0-9]{1,})/(1|0|-1)'                   => 'admin|tickets|changeStatus|id_ticket=$1|status=$2',
    'admin/ticket/change-priority/([0-9]{1,})/(1|0|2)'                  => 'admin|tickets|changePriority|id_ticket=$1|priority=$2',

    'admin/ticket/remove/([0-9]{1,})'                                   => 'admin|tickets|remove|id_ticket=$1',
    'admin/ticket/close/([0-9]{1,})'                                    => 'admin|tickets|close|id_ticket=$1',

    'admin/ticket/download/answer/file/([0-9]{1,})/(.*)'          => 'admin|tickets|downloadAnswerFile|id_answer=$1|filename=$2',
    'admin/ticket/download/file/([0-9]{1,})/(.*)'         => 'admin|tickets|downloadFile|id_ticket=$1|filename=$2',
    'admin/ticket/([0-9]{1,})'                                          => 'admin|tickets|ticket|id_ticket=$1',
    'admin/ticket/new'                                                  => 'admin|tickets|newTicket',
    'admin/ticket/checker/get-count'                                    => 'admin|tickets|checkerGetTicketsCount',
    'admin/ticket/messages/checker/get-count'                           => 'admin|tickets|checkerGetTicketsMessagesCount',

    'admin/domain/remove/([0-9]{1,})'                                   => 'admin|domains|remove|id_domain=$1',
    'admin/domain/edit/([0-9]{1,})'                                     => 'admin|domains|edit|id_domain=$1',
    'admin/domain/add'                                                  => 'admin|domains|edit',
    'admin/domains'                                                     => 'admin|domains',

    'admin/domain-registrars'                     => 'admin|domainRegistrars|list',
    'admin/domain-registrars/remove/([0-9]{1,})'  => 'admin|domainRegistrars|remove|id_registrar=$1',
    'admin/domain-registrars/([0-9]{1,})'         => 'admin|domainRegistrars|edit|id_registrar=$1',
    'admin/domain-registrars/add'                 => 'admin|domainRegistrars|edit',

    'admin/promocodes/edit'                   => 'admin|promocodes|edit',
    'admin/promocodes/remove'                     => 'admin|promocodes|remove',
    'admin/promocodes'                            => 'admin|promocodes|list',



    'admin/services'                              => 'admin|services|list',
    'admin/services/edit'                         => 'admin|services|edit',
    'admin/services/remove'                       => 'admin|services|remove',

    'admin/service-categories'                    => 'admin|serviceCategories|list',
    'admin/service-categories/edit'               => 'admin|serviceCategories|edit',
    'admin/service-categories/remove'             => 'admin|serviceCategories|remove',

    'admin/service-orders'                        => 'admin|ServiceOrders|list',
    'admin/service-orders/show'                   => 'admin|ServiceOrders|show',
    'admin/service-orders/remove'                 => 'admin|ServiceOrders|remove',
    'admin/service-orders/info'                 => 'admin|ServiceOrders|info',
    'admin/service-orders/edit' => 'admin|ServiceOrders|edit',


    'admin/pages'                                 => 'admin|pages|list',
    'admin/pages/edit'                            => 'admin|pages|edit',
    'admin/pages/remove'                          => 'admin|pages|remove',

    'admin/vps-servers'                           => 'admin|VpsServers|list',
    'admin/vps-servers/edit'                      => 'admin|VpsServers|edit',
    'admin/vps-servers/remove'                    => 'admin|VpsServers|remove',
    'admin/vps-servers/check'                    => 'admin|VpsServers|check',

    'admin/vps-ips'                                 => 'admin|VpsServerIps|list',
    'admin/vps-ips/edit'                            => 'admin|VpsServerIps|edit',
    'admin/vps-ips/remove'                          => 'admin|VpsServerIps|remove',

    'admin/vps-plans'                           => 'admin|VpsPlans|list',
    'admin/vps-plans/edit'                      => 'admin|VpsPlans|edit',
    'admin/vps-plans/remove'                    => 'admin|VpsPlans|remove',


    'admin/vps-orders'                           => 'admin|VpsOrders|list',
    'admin/vps-orders/edit'                      => 'admin|VpsOrders|edit',
    'admin/vps-orders/remove'                    => 'admin|VpsOrders|remove',


    'admin/vps-params'                           => 'admin|VpsParams|list',
    'admin/vps-params/edit'                      => 'admin|VpsParams|edit',

    'admin/vps-params/remove'                    => 'admin|VpsParams|remove',

    'checker/menu' => 'front|front|checker',

    'reg'                                                               => 'front|user|reg',
    'reg/ref([0-9]{1,})'                                                => 'front|user|reg|id_ref=$1',

    'reminder/code/([A-Za-z0-9]{0,})'                                   => 'front|user|reminder|code=$1',
    'reminder'                                                          => 'front|user|reminder',

    'social/auth/(.*)'                                                       => 'front|user|socialAuth|back=$1',
    'social/auth'                                                       => 'front|user|socialAuth',

    'partner/getMoney'                                                  => 'front|partner|getMoney',
    'partner'                                                           => 'front|partner',
    'balance/create-bill/([+]?[0-9]*\.?[0-9]{1,2})'                                   => 'front|balance|createBill|summ=$1',

    'domain-owner/add'                                                  => 'front|domainOwner|add',

    'balance'                                                           => 'front|balance',

    'bills/order/change-plan/([0-9]{1,})'                               => 'front|order|changePlan|id_order=$1',

    'hosting-order/remove'                                              => 'front|order|remove',
    'hosting-order/prolong'                                             => 'front|order|prolong',

    'bills/order/([0-9]{1,})'                                           => 'front|bills|index|$1',

    'payment/status/([A-Za-z0-9]{0,})'                                  => 'front|bills|paymentStatus|system=$1',
    'payment/status/([A-Za-z0-9]{0,})/([0-9]{1,})/([A-Za-z0-9]{0,})'    => 'front|bills|paymentStatus|system=$1|id_bill=$2|status=$3',


    'bills'                                                             => 'front|bills',


    'bill/pay/balance/([0-9]{1,})'                                      => 'front|bills|payBalance|id_bill=$1',
    'bill/off/([0-9]{1,})'                                              => 'front|bills|off|id_bill=$1',

    'bill/pay/([0-9]{1,})' => 'front|bill|pay|$1',


    'bill/([0-9]{1,})'                                                  => 'front|bills|bill|$1',


    'domain-orders'                    => 'front|domainOrders|list',

    'domain-orders/ns-change'          => 'front|domainOrders|changeNS',
    'domain-orders/prolong'            => 'front|domainOrders|prolong',
    'domain-orders/remove'             => 'front|domainOrders|remove',
    'domain-orders/order'              => 'front|domainOrders|order',
    'domain-orders/order/([0-9,]{1,})' => 'front|domainOrders|order|ids=$1',
    'domain-orders/order/(.*)'          => 'front|domainOrders|order|domains=$1',


    'hosting-orders'                   => 'front|order|list',
    'hosting-orders/open-server-panel' => 'front|order|openServerPanel',
    'hosting-orders/new'               => 'front|order|hosting',

    'vps-orders'                   => 'front|VpsOrder|list',
    'vps-orders/new'               => 'front|VpsOrder|new',
    'vps-order/plan/([0-9]{1,})'   => 'front|VpsOrder|plan|plan_id=$1',
    'vps-orders/prolong'           => 'front|VpsOrder|prolong',
    'vps-orders/remove' => 'front|VpsOrder|remove',


    'order/plan/([0-9]{1,})'                                            => 'front|order|plan|plan=$1',
    'order/(A-Za-z+){0,}'                                               => 'front|order|$1',


    'support/download/answer/file/([0-9]{1,})/(.*)'      => 'front|support|downloadAnswerFile|id_answer=$1|filename=$2',
    'support/download/file/([0-9]{1,})/(.*)'             => 'front|support|downloadFile|id_ticket=$1|filename=$2',

    'support/ticket/new'                                                => 'front|support|new',
    'support/ticket/close'                                              => 'front|support|close',
    'support/ticket/show'                                               => 'front|support|show',
    'support'                                                           => 'front|support',
    'support/checker/get-count'                                         => 'front|support|checkerGetMessagesCount',

    'currency/set/([0-9]{1,})'                                          => 'front|setting|setCurrency|id_currency=$1|back=$2',
    'setting'                                                           => 'front|setting',
    'setting/docs/remove' => 'front|setting|removeDocs',
    'setting/safety'                                                    => 'front|setting|safety',
    'setting/notifications'                                             => 'front|setting|notifications',


    'setting/domain-owners'                                            => 'front|domainOwner|list',
    'setting/domain-owner/copy'                                        => 'front|domainOwner|copy',
    'setting/domain-owner/remove'                                      => 'front|domainOwner|remove',


    'setting/remove-social-account'                                      => 'front|setting|removeSocialAccount',

    'service-order/category-([0-9]{1,})'                               => 'front|serviceOrder|select|$1',
    'service-order/new/service-([0-9]{1,})'                            => 'front|serviceOrder|order|service_id=$1',
    'service-orders/category-([0-9]{1,})'                              => 'front|serviceOrder|list|category_id=$1',
    'service-orders/show'                                              => 'front|serviceOrder|show',
    'service-orders/info'                                              => 'front|serviceOrder|info',
    'service-orders/prolong'                                           => 'front|serviceOrder|prolong',
    'service-orders/remove'                                            => 'front|serviceOrder|remove',

    'bug-report'                                                        => 'admin|bugReport',
    'install'                                                           => '|install',
    'cron/(.*)' => '|cron|safeRun|key=$1',
    'cron'                                                              => '|cron',

    'admin/modules/([A-Za-z0-9]{0,})/(.*)'                                    => '|modules|route|module=$1|route=$2|prefix=admin/',
    'modules/([A-Za-z0-9]{0,})/(.*)'                                    => '|modules|route|module=$1|route=$2',

    'page/([A-Za-z0-9]{0,})' => 'front|page|display|page=$1',
    'none'                                                              => 'front|home',
    'index.php'                                                         => 'front|home',

    '(.*)'                                                              => 'front|home|error404'

);