<?php

namespace hosting\ispConfig;

interface SoapClientInterface {

    public function login($username, $password);

    public function logout();

    public function serverGet($serverId, $section);

    public function serverGetAll();

    public function serverGetServeridByName($serverName);

    public function serverGetFunctions($serverId);

    public function updateRecordPermissions($tablename, $indexField, $indexValue, $permissions);

    public function serverGetAppVersion();

    public function serverGetServeridByIp($ipaddress);

    public function serverIpGet($primaryId);

    public function serverIpAdd($clientId, $params);

    public function serverIpUpdate($clientId, $ipId, $params);

    public function serverIpDelete($ipId);

    public function mailDomainGet($primaryId);

    public function mailDomainAdd($clientId, $params);

    public function mailDomainUpdate($clientId, $primaryId, $params);

    public function mailDomainDelete($primaryId);

    public function mailAliasdomainGet($primaryId);

    public function mailAliasdomainAdd($clientId, $params);

    public function mailAliasdomainUpdate($clientId, $primaryId, $params);

    public function mailAliasdomainDelete($primaryId);

    public function mailMailinglistGet($primaryId);

    public function mailMailinglistAdd($clientId, $params);

    public function mailMailinglistUpdate($clientId, $primaryId, $params);

    public function mailMailinglistDelete($primaryId);

    public function mailUserGet($primaryId);

    public function mailUserAdd($clientId, $params);

    public function mailUserUpdate($clientId, $primaryId, $params);

    public function mailUserDelete($primaryId);

    public function mailUserFilterGet($primaryId);

    public function mailUserFilterAdd($clientId, $params);

    public function mailUserFilterUpdate($clientId, $primaryId, $params);

    public function mailUserFilterDelete($primaryId);

    public function mailAliasGet($primaryId);

    public function mailAliasAdd($clientId, $params);

    public function mailAliasUpdate($clientId, $primaryId, $params);

    public function mailAliasDelete($primaryId);

    public function mailForwardGet($primaryId);

    public function mailForwardAdd($clientId, $params);

    public function mailForwardUpdate($clientId, $primaryId, $params);

    public function mailForwardDelete($primaryId);

    public function mailCatchallGet($primaryId);

    public function mailCatchallAdd($clientId, $params);

    public function mailCatchallUpdate($clientId, $primaryId, $params);

    public function mailCatchallDelete($primaryId);

    public function mailTransportGet($primaryId);

    public function mailTransportAdd($clientId, $params);

    public function mailTransportUpdate($clientId, $primaryId, $params);

    public function mailTransportDelete($primaryId);

    public function mailRelayRecipientGet($primaryId);

    public function mailRelayRecipientAdd($clientId, $params);

    public function mailRelayRecipientUpdate($clientId, $primaryId, $params);

    public function mailRelayRecipientDelete($primaryId);

    public function mailSpamfilterWhitelistGet($primaryId);

    public function mailSpamfilterWhitelistAdd($clientId, $params);

    public function mailSpamfilterWhitelistUpdate($clientId, $primaryId, $params);

    public function mailSpamfilterWhitelistDelete($primaryId);

    public function mailSpamfilterBlacklistGet($primaryId);

    public function mailSpamfilterBlacklistAdd($clientId, $params);

    public function mailSpamfilterBlacklistUpdate($clientId, $primaryId, $params);

    public function mailSpamfilterBlacklistDelete($primaryId);

    public function mailSpamfilterUserGet($primaryId);

    public function mailSpamfilterUserAdd($clientId, $params);

    public function mailSpamfilterUserUpdate($clientId, $primaryId, $params);

    public function mailSpamfilterUserDelete($primaryId);

    public function mailPolicyGet($primaryId);

    public function mailPolicyAdd($clientId, $params);

    public function mailPolicyUpdate($clientId, $primaryId, $params);

    public function mailPolicyDelete($primaryId);

    public function mailFetchmailGet($primaryId);

    public function mailFetchmailAdd($clientId, $params);

    public function mailFetchmailUpdate($clientId, $primaryId, $params);

    public function mailFetchmailDelete($primaryId);

    public function mailWhitelistGet($primaryId);

    public function mailWhitelistAdd($clientId, $params);

    public function mailWhitelistUpdate($clientId, $primaryId, $params);

    public function mailWhitelistDelete($primaryId);

    public function mailBlacklistGet($primaryId);

    public function mailBlacklistAdd($clientId, $params);

    public function mailBlacklistUpdate($clientId, $primaryId, $params);

    public function mailBlacklistDelete($primaryId);

    public function mailFilterGet($primaryId);

    public function mailFilterAdd($clientId, $params);

    public function mailFilterUpdate($clientId, $primaryId, $params);

    public function mailFilterDelete($primaryId);

    public function clientGet($clientId);

    public function clientGetId($sysUserid);

    public function clientGetGroupid($clientId);

    public function clientAdd($contact_name, $company_name, $user_name, $password, $email, $telephone, $limit_client = 0, $web_php_options = array('no', 'fast-cgi', 'cgi', 'mod', 'suphp', 'php-fpm'), $ssh_chroot = array('no', 'jailkit'), $language = "en", $usertheme = "default", $country = "NZ", $resellerId = 0);

    public function clientUpdate($clientId, $resellerId, $params);

    public function clientTemplateAdditionalGet($clientId);

    public function SetClientFormdata($clientId);

    public function clientTemplateAdditionalAdd($clientId, $templateId);

    public function clientTemplateAdditionalDelete($clientId, $assignedTemplateId);

    public function clientDelete($clientId);

    public function clientDeleteEverything($clientId);

    public function sitesCronGet($cronId);

    public function sitesCronAdd($clientId, $params);

    public function sitesCronUpdate($clientId, $cronId, $params);

    public function sitesCronDelete($cronId);

    public function sitesDatabaseGet($primaryId);

    public function sitesDatabaseAdd($clientId, $serverId, $site, $dbName, $dbUserId, $type = 'mysql', $charset = 'utf8', $active = 'y');

    public function sitesDatabaseUpdate($clientId, $primaryId, $params);

    public function sitesDatabaseDelete($primaryId);

    public function sitesDatabaseUserGet($primaryId);

    public function sitesDatabaseUserAdd($clientId, $serverId, $dbUser, $dbPass);

    public function sitesDatabaseUserUpdate($clientId, $dbUserId, $serverId, $dbUser, $dbPass);

    public function sitesDatabaseUserDelete($primaryId);

    public function sitesFtpUserGet($primaryId);

    public function sitesFtpUserAdd($clientId, $siteId, $userName, $password, $quotaSize = "-1", $active = "y");

    public function sitesFtpUserUpdate($clientId, $primaryId, $params);

    public function sitesFtpUserDelete($primaryId);

    public function sitesFtpUserServerGet($ftpUser);

    public function sitesShellUserGet($primaryId);

    public function sitesShellUserAdd($clientId, $params);

    public function sitesShellUserUpdate($clientId, $primaryId, $params);

    public function sitesShellUserDelete($primaryId);

    public function sitesWebDomainGet($primaryId);

    public function sitesWebDomainAdd($clientId, $domain, $serverId = 1, $ipAddress = "*", $subdomain = "www", $hd_quota = "-1", $traffic_quota = "-1", $allow_override = "All", $pm_process_idle_timeout = "10", $pm_max_requests = "0", $pm_max_children = "10", $pm_start_servers = "2", $pm_max_spare_servers = "5", $errordocs = 1, $php = "php-fpm", $stats_type = "webalizer", $pm = "dynamic", $active = "y", $suexec = "y", $vhost_type = "name", $type = "vhost", $fastcgi_php_version = "PHP 5.4.30:/etc/init.d/php-5.4.30-fpm:/opt/phpfarm/inst/php-5.4.30/lib/:/opt/phpfarm/inst/php-5.4.30/etc/pool.d/", $readonly = 0);

    public function sitesWebDomainUpdate($clientId, $primaryId, $params);

    public function sitesWebDomainDelete($primaryId);

    public function sitesWebVhostSubdomainGet($primaryId);

    public function sitesWebVhostSubdomainAdd($clientId, $params);

    public function sitesWebVhostSubdomainUpdate($clientId, $primaryId, $params);

    public function sitesWebVhostSubdomainDelete($primaryId);

    public function sitesWebAliasdomainGet($primaryId);

    public function sitesWebAliasdomainAdd($clientId, $siteId, $alias);

    public function sitesWebAliasdomainUpdate($clientId, $primaryId, $params);

    public function sitesWebAliasdomainDelete($primaryId);

    public function sitesWebSubdomainGet($primaryId);

    public function sitesWebSubdomainAdd($clientId, $params);

    public function sitesWebSubdomainUpdate($clientId, $primaryId, $params);

    public function sitesWebSubdomainDelete($primaryId);

    public function sitesWebFolderGet($primaryId);

    public function sitesWebFolderAdd($clientId, $params);

    public function sitesWebFolderUpdate($clientId, $primaryId, $params);

    public function sitesWebFolderDelete($primaryId);

    public function sitesWebFolderUserGet($primaryId);

    public function sitesWebFolderUserAdd($clientId, $params);

    public function sitesWebFolderUserUpdate($clientId, $primaryId, $params);

    public function sitesWebFolderUserDelete($primaryId);

    public function domainsDomainGet($primaryId);

    public function domainsDomainAdd($clientId, $params);

    public function domainsDomainDelete($primaryId);

    public function domainsGetAllByUser($groupId);

    public function dnsTemplatezoneAdd($clientId, $templateId, $domain, $ip, $ns1, $ns2, $email);

    public function dnsZoneGet($primaryId);

    public function dnsZoneGetId($origin);

    public function dnsZoneAdd($clientId, $params);

    public function dnsZoneUpdate($clientId, $primaryId, $params);

    public function dnsZoneDelete($primaryId);

    public function dnsAaaaGet($primaryId);

    public function dnsAaaaAdd($clientId, $params);

    public function dnsAaaaUpdate($clientId, $primaryId, $params);

    public function dnsAaaaDelete($primaryId);

    public function dnsAGet($primaryId);

    public function dnsAAdd($clientId, $params);

    public function dnsAUpdate($clientId, $primaryId, $params);

    public function dnsADelete($primaryId);

    public function dnsAliasGet($primaryId);

    public function dnsAliasAdd($clientId, $params);

    public function dnsAliasUpdate($clientId, $primaryId, $params);

    public function dnsAliasDelete($primaryId);

    public function dnsCnameGet($primaryId);

    public function dnsCnameAdd($clientId, $params);

    public function dnsCnameUpdate($clientId, $primaryId, $params);

    public function dnsCnameDelete($primaryId);

    public function dnsHinfoGet($primaryId);

    public function dnsHinfoAdd($clientId, $params);

    public function dnsHinfoUpdate($clientId, $primaryId, $params);

    public function dnsHinfoDelete($primaryId);

    public function dnsMxGet($primaryId);

    public function dnsMxAdd($clientId, $params);

    public function dnsMxUpdate($clientId, $primaryId, $params);

    public function dnsMxDelete($primaryId);

    public function dnsNsGet($primaryId);

    public function dnsNsAdd($clientId, $params);

    public function dnsNsUpdate($clientId, $primaryId, $params);

    public function dnsNsDelete($primaryId);

    public function dnsPtrGet($primaryId);

    public function dnsPtrAdd($clientId, $params);

    public function dnsPtrUpdate($clientId, $primaryId, $params);

    public function dnsPtrDelete($primaryId);

    public function dnsRpGet($primaryId);

    public function dnsRpAdd($clientId, $params);

    public function dnsRpUpdate($clientId, $primaryId, $params);

    public function dnsRpDelete($primaryId);

    public function dnsSrvGet($primaryId);

    public function dnsSrvAdd($clientId, $params);

    public function dnsSrvUpdate($clientId, $primaryId, $params);

    public function dnsSrvDelete($primaryId);

    public function dnsTxtGet($primaryId);

    public function dnsTxtAdd($clientId, $params);

    public function dnsTxtUpdate($clientId, $primaryId, $params);

    public function dnsTxtDelete($primaryId);

    public function klientadd($formdefFile, $resellerId, $params);

    public function insertQuery($formdefFile, $clientId, $params, $eventIdentifier);

    public function insertQueryPrepare($formdefFile, $clientId, $params);

    public function insertQueryExecute($sql, $params, $eventIdentifier);

    public function updateQuery($formdefFile, $clientId, $primaryId, $params, $eventIdentifier);

    public function updateQueryPrepare($formdefFile, $clientId, $primaryId, $params);

    public function updateQueryExecute($sql, $primaryId, $params, $eventIdentifier);

    public function deleteQuery($formdefFile, $primaryId, $eventIdentifier);

    public function checkPerm($functionName);

    public function getSession();

    public function clientGetSitesByUser($sysUserid, $sysGroupid);

    public function sitesGetAll();

    public function sitesWebDomainSetStatus($primaryId, $status);

    public function clientGetByUsername($username);

    public function clientGetAll();

    public function clientChangePassword($clientId, $newPassword);

    public function mailDomainGetByDomain($domain);

    public function getFunctionList();

    public function sitesDatabaseGetAllByUser($clientId);

    public function clientTemplatesGetAll();

    public function dnsZoneGetByUser($clientId, $serverId);

    public function dnsRrGetAllByZone($zoneId);

    public function dnsZoneSetStatus($primaryId, $status);

    public function mailDomainSetStatus($primaryId, $status);

    public function openvzOstemplateGet($ostemplateId);

    public function openvzOstemplateAdd($clientId, $params);

    public function openvzOstemplateUpdate($clientId, $ostemplateId, $params);

    public function openvzOstemplateDelete($ostemplateId);

    public function openvzTemplateGet($templateId);

    public function openvzTemplateAdd($clientId, $params);

    public function openvzTemplateUpdate($clientId, $templateId, $params);

    public function openvzTemplateDelete($templateId);

    public function openvzIpGet($ipId);

    public function openvzGetFreeIp($serverId);

    public function openvzIpAdd($clientId, $params);

    public function openvzIpUpdate($clientId, $ipId, $params);

    public function openvzIpDelete($ipId);

    public function openvzVmGet($vmId);

    public function openvzVmGetByClient($clientId);

    public function openvzVmAdd($clientId, $params);

    public function openvzVmAddFromTemplate($clientId, $ostemplateId, $templateId, $overrideParams);

    public function openvzVmUpdate($clientId, $vmId, $params);

    public function openvzVmDelete($vmId);

    public function openvzVmStart($vmId);

    public function openvzVmStop($vmId);

    public function openvzVmRestart($vmId);
}
