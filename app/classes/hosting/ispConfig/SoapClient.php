<?php

namespace hosting\ispConfig;

class SoapClient extends AbstractSoapClient {

    public function login($username, $password) {

        return $this->makeCall("login", $username, $password);
    }

    public function logout() {
        return $this->makeCall("logout", $this->getSessionId());
    }

    public function serverGet($serverId, $section = "") {
        return $this->makeCall("server_get", $this->getSessionId(), $serverId, $section);
    }

    public function serverGetAll() {
        return $this->makeCall("server_get_all", $this->getSessionId());
    }

    public function serverGetServeridByName($serverName) {
        return $this->makeCall("server_get_serverid_by_name", $this->getSessionId(), $serverName);
    }

    public function serverGetFunctions($serverId) {
        return $this->makeCall("server_get_functions", $this->getSessionId(), $serverId);
    }

    public function updateRecordPermissions($tablename, $indexField, $indexValue, $permissions) {
        return $this->makeCall("update_record_permissions", $tablename, $indexField, $indexValue, $permissions);
    }

    public function serverGetAppVersion() {
        return $this->makeCall("server_get_app_version", $this->getSessionId());
    }

    public function serverGetServeridByIp($ipaddress) {
        return $this->makeCall("server_get_serverid_by_ip", $this->getSessionId(), $ipaddress);
    }

    public function serverIpGet($primaryId) {
        return $this->makeCall("server_ip_get", $this->getSessionId(), $primaryId);
    }

    public function serverIpAdd($clientId, $params) {
        return $this->makeCall("server_ip_add", $this->getSessionId(), $clientId, $params);
    }

    public function serverIpUpdate($clientId, $ipId, $params) {
        return $this->makeCall("server_ip_update", $this->getSessionId(), $clientId, $ipId, $params);
    }

    public function serverIpDelete($ipId) {
        return $this->makeCall("server_ip_delete", $this->getSessionId(), $ipId);
    }

    public function mailDomainGet($primaryId) {
        return $this->makeCall("mail_domain_get", $this->getSessionId(), $primaryId);
    }

    public function mailDomainAdd($clientId, $params) {
        return $this->makeCall("mail_domain_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailDomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_domain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailDomainDelete($primaryId) {
        return $this->makeCall("mail_domain_delete", $this->getSessionId(), $primaryId);
    }

    public function mailAliasdomainGet($primaryId) {
        return $this->makeCall("mail_aliasdomain_get", $this->getSessionId(), $primaryId);
    }

    public function mailAliasdomainAdd($clientId, $params) {
        return $this->makeCall("mail_aliasdomain_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailAliasdomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_aliasdomain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailAliasdomainDelete($primaryId) {
        return $this->makeCall("mail_aliasdomain_delete", $this->getSessionId(), $primaryId);
    }

    public function mailMailinglistGet($primaryId) {
        return $this->makeCall("mail_mailinglist_get", $this->getSessionId(), $primaryId);
    }

    public function mailMailinglistAdd($clientId, $params) {
        return $this->makeCall("mail_mailinglist_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailMailinglistUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_mailinglist_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailMailinglistDelete($primaryId) {
        return $this->makeCall("mail_mailinglist_delete", $this->getSessionId(), $primaryId);
    }

    public function mailUserGet($primaryId) {
        return $this->makeCall("mail_user_get", $this->getSessionId(), $primaryId);
    }

    public function mailUserAdd($clientId, $params) {
        return $this->makeCall("mail_user_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailUserUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_user_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailUserDelete($primaryId) {
        return $this->makeCall("mail_user_delete", $this->getSessionId(), $primaryId);
    }

    public function mailUserFilterGet($primaryId) {
        return $this->makeCall("mail_user_filter_get", $this->getSessionId(), $primaryId);
    }

    public function mailUserFilterAdd($clientId, $params) {
        return $this->makeCall("mail_user_filter_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailUserFilterUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_user_filter_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailUserFilterDelete($primaryId) {
        return $this->makeCall("mail_user_filter_delete", $this->getSessionId(), $primaryId);
    }

    public function mailAliasGet($primaryId) {
        return $this->makeCall("mail_alias_get", $this->getSessionId(), $primaryId);
    }

    public function mailAliasAdd($clientId, $params) {
        return $this->makeCall("mail_alias_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailAliasUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_alias_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailAliasDelete($primaryId) {
        return $this->makeCall("mail_alias_delete", $this->getSessionId(), $primaryId);
    }

    public function mailForwardGet($primaryId) {
        return $this->makeCall("mail_forward_get", $this->getSessionId(), $primaryId);
    }

    public function mailForwardAdd($clientId, $params) {
        return $this->makeCall("mail_forward_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailForwardUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_forward_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailForwardDelete($primaryId) {
        return $this->makeCall("mail_forward_delete", $this->getSessionId(), $primaryId);
    }

    public function mailCatchallGet($primaryId) {
        return $this->makeCall("mail_catchall_get", $this->getSessionId(), $primaryId);
    }

    public function mailCatchallAdd($clientId, $params) {
        return $this->makeCall("mail_catchall_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailCatchallUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_catchall_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailCatchallDelete($primaryId) {
        return $this->makeCall("mail_catchall_delete", $this->getSessionId(), $primaryId);
    }

    public function mailTransportGet($primaryId) {
        return $this->makeCall("mail_transport_get", $this->getSessionId(), $primaryId);
    }

    public function mailTransportAdd($clientId, $params) {
        return $this->makeCall("mail_transport_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailTransportUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_transport_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailTransportDelete($primaryId) {
        return $this->makeCall("mail_transport_delete", $this->getSessionId(), $primaryId);
    }

    public function mailRelayRecipientGet($primaryId) {
        return $this->makeCall("mail_relay_recipient_get", $this->getSessionId(), $primaryId);
    }

    public function mailRelayRecipientAdd($clientId, $params) {
        return $this->makeCall("mail_relay_recipient_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailRelayRecipientUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_relay_recipient_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailRelayRecipientDelete($primaryId) {
        return $this->makeCall("mail_relay_recipient_delete", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterWhitelistGet($primaryId) {
        return $this->makeCall("mail_spamfilter_whitelist_get", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterWhitelistAdd($clientId, $params) {
        return $this->makeCall("mail_spamfilter_whitelist_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailSpamfilterWhitelistUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_spamfilter_whitelist_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailSpamfilterWhitelistDelete($primaryId) {
        return $this->makeCall("mail_spamfilter_whitelist_delete", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterBlacklistGet($primaryId) {
        return $this->makeCall("mail_spamfilter_blacklist_get", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterBlacklistAdd($clientId, $params) {
        return $this->makeCall("mail_spamfilter_blacklist_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailSpamfilterBlacklistUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_spamfilter_blacklist_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailSpamfilterBlacklistDelete($primaryId) {
        return $this->makeCall("mail_spamfilter_blacklist_delete", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterUserGet($primaryId) {
        return $this->makeCall("mail_spamfilter_user_get", $this->getSessionId(), $primaryId);
    }

    public function mailSpamfilterUserAdd($clientId, $params) {
        return $this->makeCall("mail_spamfilter_user_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailSpamfilterUserUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_spamfilter_user_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailSpamfilterUserDelete($primaryId) {
        return $this->makeCall("mail_spamfilter_user_delete", $this->getSessionId(), $primaryId);
    }

    public function mailPolicyGet($primaryId) {
        return $this->makeCall("mail_policy_get", $this->getSessionId(), $primaryId);
    }

    public function mailPolicyAdd($clientId, $params) {
        return $this->makeCall("mail_policy_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailPolicyUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_policy_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailPolicyDelete($primaryId) {
        return $this->makeCall("mail_policy_delete", $this->getSessionId(), $primaryId);
    }

    public function mailFetchmailGet($primaryId) {
        return $this->makeCall("mail_fetchmail_get", $this->getSessionId(), $primaryId);
    }

    public function mailFetchmailAdd($clientId, $params) {
        return $this->makeCall("mail_fetchmail_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailFetchmailUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_fetchmail_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailFetchmailDelete($primaryId) {
        return $this->makeCall("mail_fetchmail_delete", $this->getSessionId(), $primaryId);
    }

    public function mailWhitelistGet($primaryId) {
        return $this->makeCall("mail_whitelist_get", $this->getSessionId(), $primaryId);
    }

    public function mailWhitelistAdd($clientId, $params) {
        return $this->makeCall("mail_whitelist_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailWhitelistUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_whitelist_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailWhitelistDelete($primaryId) {
        return $this->makeCall("mail_whitelist_delete", $this->getSessionId(), $primaryId);
    }

    public function mailBlacklistGet($primaryId) {
        return $this->makeCall("mail_blacklist_get", $this->getSessionId(), $primaryId);
    }

    public function mailBlacklistAdd($clientId, $params) {
        return $this->makeCall("mail_blacklist_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailBlacklistUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_blacklist_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailBlacklistDelete($primaryId) {
        return $this->makeCall("mail_blacklist_delete", $this->getSessionId(), $primaryId);
    }

    public function mailFilterGet($primaryId) {
        return $this->makeCall("mail_filter_get", $this->getSessionId(), $primaryId);
    }

    public function mailFilterAdd($clientId, $params) {
        return $this->makeCall("mail_filter_add", $this->getSessionId(), $clientId, $params);
    }

    public function mailFilterUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("mail_filter_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function mailFilterDelete($primaryId) {
        return $this->makeCall("mail_filter_delete", $this->getSessionId(), $primaryId);
    }

    public function clientGet($clientId) {
        return $this->makeCall("client_get", $this->getSessionId(), $clientId);
    }

    public function clientGetId($sysUserid) {
        return $this->makeCall("client_get_id", $this->getSessionId(), $sysUserid);
    }

    public function clientGetGroupid($clientId) {
        return $this->makeCall("client_get_groupid", $this->getSessionId(), $clientId);
    }

    public function clientAdd($contact_name, $company_name, $user_name, $password, $email, $template_master, $limit_client = 0, $web_php_options = array('no', 'fast-cgi', 'cgi', 'mod', 'suphp', 'php-fpm'), $ssh_chroot = array('no', 'jailkit'), $language = "en", $usertheme = "default", $country = "NZ", $resellerId = 0) {
        $params = array(
            "contact_name"    => $contact_name,
            "username"        => $user_name,
            "password"        => $password,
            "company_name"    => $company_name,
            "email"           => $email,
            "template_master"       => $template_master,
            "limit_client"    => $limit_client,
            "web_php_options" => $web_php_options,
            "ssh_chroot"      => $ssh_chroot,
            "language"        => $language,
            "usertheme"       => $usertheme,
            "country"         => $country,
        );
        return $this->makeCall("client_add", $this->getSessionId(), $resellerId, $params);
    }

    public function clientUpdate($clientId, $resellerId, $params) {
        return $this->makeCall("client_update", $this->getSessionId(), $clientId, $resellerId, $params);
    }

    public function clientTemplateAdditionalGet($clientId) {
        return $this->makeCall("client_template_additional_get", $this->getSessionId(), $clientId);
    }

    public function SetClientFormdata($clientId) {
        return $this->makeCall("_set_client_formdata", $clientId);
    }

    public function clientTemplateAdditionalAdd($clientId, $templateId) {
        return $this->makeCall("client_template_additional_add", $this->getSessionId(), $clientId, $templateId);
    }

    public function clientTemplateAdditionalDelete($clientId, $assignedTemplateId) {
        return $this->makeCall("client_template_additional_delete", $this->getSessionId(), $clientId, $assignedTemplateId);
    }

    public function clientDelete($clientId) {
        return $this->makeCall("client_delete", $this->getSessionId(), $clientId);
    }

    public function clientDeleteEverything($clientId) {
        return $this->makeCall("client_delete_everything", $this->getSessionId(), $clientId);
    }

    public function sitesCronGet($cronId) {
        return $this->makeCall("sites_cron_get", $this->getSessionId(), $cronId);
    }

    public function sitesCronAdd($clientId, $params) {
        return $this->makeCall("sites_cron_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesCronUpdate($clientId, $cronId, $params) {
        return $this->makeCall("sites_cron_update", $this->getSessionId(), $clientId, $cronId, $params);
    }

    public function sitesCronDelete($cronId) {
        return $this->makeCall("sites_cron_delete", $this->getSessionId(), $cronId);
    }

    public function sitesDatabaseGet($primaryId) {
        return $this->makeCall("sites_database_get", $this->getSessionId(), $primaryId);
    }

    public function sitesDatabaseAdd($clientId, $serverId, $site, $dbName, $dbUserId, $type = 'mysql', $charset = 'utf8', $active = 'y') {
        $params = array(
            "server_id"        => $serverId,
            "parent_domain_id" => $site,
            "database_user_id" => $dbUserId,
            "type"             => $type,
            "database_name"    => $dbName,
            "database_charset" => $charset,
            "active"           => $active,
        );
        return $this->makeCall("sites_database_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesDatabaseUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_database_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesDatabaseDelete($primaryId) {
        return $this->makeCall("sites_database_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesDatabaseUserGet($primaryId) {
        return $this->makeCall("sites_database_user_get", $this->getSessionId(), $primaryId);
    }

    public function sitesDatabaseUserAdd($clientId, $serverId, $dbUser, $dbPass) {
        $params = array(
            "server_id"         => $serverId,
            "database_user"     => $dbUser,
            "database_password" => $dbPass,
            "repeat_password"   => $dbPass
        );
        return $this->makeCall("sites_database_user_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesDatabaseUserUpdate($clientId, $dbUserId, $serverId, $dbUser, $dbPass) {
        $params = array(
            "server_id"         => $serverId,
            "database_user"     => $dbUser,
            "database_password" => $dbPass,
            "repeat_password"   => $dbPass
        );
        return $this->makeCall("sites_database_user_update", $this->getSessionId(), $clientId, $dbUserId, $params);
    }

    public function sitesDatabaseUserDelete($primaryId) {
        return $this->makeCall("sites_database_user_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesFtpUserGet($primaryId) {
        return $this->makeCall("sites_ftp_user_get", $this->getSessionId(), $primaryId);
    }

    public function sitesFtpUserAdd($clientId, $siteId, $userName, $password, $quotaSize = "-1", $active = "y") {
        $result = false;
        $site = $this->sitesWebDomainGet($siteId);
        // print_r($site);
        if ($site !== false) {
            $params = array(
                "server_id"        => $site['server_id'],
                "parent_domain_id" => $siteId,
                "username"         => $userName,
                "password"         => $password,
                "quota_size"       => $quotaSize,
                "active"           => $active,
                "uid"              => $site['system_user'],
                "gid"              => $site['system_group'],
                "dir"              => $site['document_root'],
                "sys_userid"       => $site['sys_userid'],
                "sys_groupid"      => $site['sys_groupid'],
            );
            $result = $this->makeCall("sites_ftp_user_add", $this->getSessionId(), $clientId, $params);
        }
        return $result;
    }

    public function sitesFtpUserUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_ftp_user_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesFtpUserDelete($primaryId) {
        return $this->makeCall("sites_ftp_user_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesFtpUserServerGet($ftpUser) {
        return $this->makeCall("sites_ftp_user_server_get", $this->getSessionId(), $ftpUser);
    }

    public function sitesShellUserGet($primaryId) {
        return $this->makeCall("sites_shell_user_get", $this->getSessionId(), $primaryId);
    }

    public function sitesShellUserAdd($clientId, $params) {
        return $this->makeCall("sites_shell_user_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesShellUserUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_shell_user_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesShellUserDelete($primaryId) {
        return $this->makeCall("sites_shell_user_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebDomainGet($primaryId) {
        return $this->makeCall("sites_web_domain_get", $this->getSessionId(), $primaryId);
    }

    /**
     *
     * @param type $clientId
     * @param type $domain
     * @param type $serverId
     * @param type $ipAddress
     * @param type $subdomain
     * @param type $hd_quota
     * @param type $traffic_quota
     * @param type $allow_override
     * @param type $pm_max_children
     * @param type $pm_start_servers
     * @param type $pm_min_spare_servers
     * @param type $pm_max_spare_servers
     * @param type $pm_process_idle_timeout
     * @param type $pm_max_requests
     * @param type $errordocs
     * @param type $php
     * @param type $stats_type
     * @param type $pm
     * @param type $active
     * @param type $suexec
     * @param type $vhost_type
     * @param type $type
     * @param type $fastcgi_php_version
     * @param type $readonly
     * @return type
     */
    public function sitesWebDomainAdd($clientId, $domain, $serverId = 1, $ipAddress = "*", $subdomain = "www", $hd_quota = "-1", $traffic_quota = "-1", $allow_override = "All", $pm_max_children = "10", $pm_start_servers = "2", $pm_min_spare_servers = "1", $pm_max_spare_servers = "5", $pm_process_idle_timeout = "10", $pm_max_requests = "0", $errordocs = "1", $php = "fast-cgi", $stats_type = "webalizer", $pm = "dynamic", $active = "y", $suexec = "y", $vhost_type = "name", $type = "vhost", $fastcgi_php_version = "", $readonly = "0") {
        $params = array(
            "domain"                  => $domain,
            "server_id"               => $serverId,
            "ip_address"              => $ipAddress,
            "subdomain"               => $subdomain,
            "hd_quota"                => $hd_quota,
            "traffic_quota"           => $traffic_quota,
            "allow_override"          => $allow_override,
            "pm_max_children"         => $pm_max_children,
            "pm_start_servers"        => $pm_start_servers,
            "pm_min_spare_servers"    => $pm_min_spare_servers,
            "pm_max_spare_servers"    => $pm_max_spare_servers,
            "pm_process_idle_timeout" => $pm_process_idle_timeout,
            "pm_max_requests"         => $pm_max_requests,
            "errordocs"               => $errordocs,
            "php"                     => $php,
            "stats_type"              => $stats_type,
            "pm"                      => $pm,
            "active"                  => $active,
            "suexec"                  => $suexec,
            "vhost_type"              => $vhost_type,
            "type"                    => $type,
            "fastcgi_php_version" => $fastcgi_php_version,
            'http_port' => '80',
            'https_port' => '443',
            'nginx_directives' => ''
        );
        return $this->makeCall("sites_web_domain_add", $this->getSessionId(), $clientId, $params, $readonly);
    }

    public function sitesWebDomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_domain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebDomainDelete($primaryId) {
        return $this->makeCall("sites_web_domain_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebVhostSubdomainGet($primaryId) {
        return $this->makeCall("sites_web_vhost_subdomain_get", $this->getSessionId(), $primaryId);
    }

    public function sitesWebVhostSubdomainAdd($clientId, $params) {
        return $this->makeCall("sites_web_vhost_subdomain_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesWebVhostSubdomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_vhost_subdomain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebVhostSubdomainDelete($primaryId) {
        return $this->makeCall("sites_web_vhost_subdomain_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebAliasdomainGet($primaryId) {
        return $this->makeCall("sites_web_aliasdomain_get", $this->getSessionId(), $primaryId);
    }

    public function sitesWebAliasdomainAdd($clientId, $siteId, $alias) {
        $result = false;
        $site   = $this->getSite($siteId);
        if ($site !== false) {
            $params = array(
                "server_id"        => $site['server_id'],
                "domain"           => $alias,
                "type"             => 'alias',
                "parent_domain_id" => $site['domain_id'],
                "active"           => "y",
                "subdomain"        => "www",
            );
            try {
                $result = $this->makeCall("sites_web_aliasdomain_add", $this->getSessionId(), $clientId, $params);
            }
            catch (Exception $exc) {
                $result              = false;
                $this->lastException = $exc;
            }
        }
        return $result;
    }

    public function sitesWebAliasdomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_aliasdomain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebAliasdomainDelete($primaryId) {
        return $this->makeCall("sites_web_aliasdomain_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebSubdomainGet($primaryId) {
        return $this->makeCall("sites_web_subdomain_get", $this->getSessionId(), $primaryId);
    }

    public function sitesWebSubdomainAdd($clientId, $params) {
        return $this->makeCall("sites_web_subdomain_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesWebSubdomainUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_subdomain_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebSubdomainDelete($primaryId) {
        return $this->makeCall("sites_web_subdomain_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebFolderGet($primaryId) {
        return $this->makeCall("sites_web_folder_get", $this->getSessionId(), $primaryId);
    }

    public function sitesWebFolderAdd($clientId, $params) {
        return $this->makeCall("sites_web_folder_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesWebFolderUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_folder_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebFolderDelete($primaryId) {
        return $this->makeCall("sites_web_folder_delete", $this->getSessionId(), $primaryId);
    }

    public function sitesWebFolderUserGet($primaryId) {
        return $this->makeCall("sites_web_folder_user_get", $this->getSessionId(), $primaryId);
    }

    public function sitesWebFolderUserAdd($clientId, $params) {
        return $this->makeCall("sites_web_folder_user_add", $this->getSessionId(), $clientId, $params);
    }

    public function sitesWebFolderUserUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("sites_web_folder_user_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function sitesWebFolderUserDelete($primaryId) {
        return $this->makeCall("sites_web_folder_user_delete", $this->getSessionId(), $primaryId);
    }

    public function domainsDomainGet($primaryId) {
        return $this->makeCall("domains_domain_get", $this->getSessionId(), $primaryId);
    }

    public function domainsDomainAdd($clientId, $params) {
        return $this->makeCall("domains_domain_add", $this->getSessionId(), $clientId, $params);
    }

    public function domainsDomainDelete($primaryId) {
        return $this->makeCall("domains_domain_delete", $this->getSessionId(), $primaryId);
    }

    public function domainsGetAllByUser($groupId) {
        return $this->makeCall("domains_get_all_by_user", $this->getSessionId(), $groupId);
    }

    public function dnsTemplatezoneAdd($clientId, $templateId, $domain, $ip, $ns1, $ns2, $email) {
        return $this->makeCall("dns_templatezone_add", $this->getSessionId(), $clientId, $templateId, $domain, $ip, $ns1, $ns2, $email);
    }

    public function dnsZoneGet($primaryId) {
        return $this->makeCall("dns_zone_get", $this->getSessionId(), $primaryId);
    }

    public function dnsZoneGetId($origin) {
        return $this->makeCall("dns_zone_get_id", $this->getSessionId(), $origin);
    }

    public function dnsZoneAdd($clientId, $params) {
        return $this->makeCall("dns_zone_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsZoneUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_zone_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsZoneDelete($primaryId) {
        return $this->makeCall("dns_zone_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsAaaaGet($primaryId) {
        return $this->makeCall("dns_aaaa_get", $this->getSessionId(), $primaryId);
    }

    public function dnsAaaaAdd($clientId, $params) {
        return $this->makeCall("dns_aaaa_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsAaaaUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_aaaa_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsAaaaDelete($primaryId) {
        return $this->makeCall("dns_aaaa_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsAGet($primaryId) {
        return $this->makeCall("dns_a_get", $this->getSessionId(), $primaryId);
    }

    public function dnsAAdd($clientId, $params) {
        return $this->makeCall("dns_a_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsAUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_a_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsADelete($primaryId) {
        return $this->makeCall("dns_a_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsAliasGet($primaryId) {
        return $this->makeCall("dns_alias_get", $this->getSessionId(), $primaryId);
    }

    public function dnsAliasAdd($clientId, $params) {
        return $this->makeCall("dns_alias_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsAliasUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_alias_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsAliasDelete($primaryId) {
        return $this->makeCall("dns_alias_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsCnameGet($primaryId) {
        return $this->makeCall("dns_cname_get", $this->getSessionId(), $primaryId);
    }

    public function dnsCnameAdd($clientId, $params) {
        return $this->makeCall("dns_cname_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsCnameUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_cname_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsCnameDelete($primaryId) {
        return $this->makeCall("dns_cname_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsHinfoGet($primaryId) {
        return $this->makeCall("dns_hinfo_get", $this->getSessionId(), $primaryId);
    }

    public function dnsHinfoAdd($clientId, $params) {
        return $this->makeCall("dns_hinfo_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsHinfoUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_hinfo_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsHinfoDelete($primaryId) {
        return $this->makeCall("dns_hinfo_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsMxGet($primaryId) {
        return $this->makeCall("dns_mx_get", $this->getSessionId(), $primaryId);
    }

    public function dnsMxAdd($clientId, $params) {
        return $this->makeCall("dns_mx_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsMxUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_mx_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsMxDelete($primaryId) {
        return $this->makeCall("dns_mx_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsNsGet($primaryId) {
        return $this->makeCall("dns_ns_get", $this->getSessionId(), $primaryId);
    }

    public function dnsNsAdd($clientId, $params) {
        return $this->makeCall("dns_ns_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsNsUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_ns_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsNsDelete($primaryId) {
        return $this->makeCall("dns_ns_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsPtrGet($primaryId) {
        return $this->makeCall("dns_ptr_get", $this->getSessionId(), $primaryId);
    }

    public function dnsPtrAdd($clientId, $params) {
        return $this->makeCall("dns_ptr_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsPtrUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_ptr_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsPtrDelete($primaryId) {
        return $this->makeCall("dns_ptr_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsRpGet($primaryId) {
        return $this->makeCall("dns_rp_get", $this->getSessionId(), $primaryId);
    }

    public function dnsRpAdd($clientId, $params) {
        return $this->makeCall("dns_rp_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsRpUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_rp_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsRpDelete($primaryId) {
        return $this->makeCall("dns_rp_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsSrvGet($primaryId) {
        return $this->makeCall("dns_srv_get", $this->getSessionId(), $primaryId);
    }

    public function dnsSrvAdd($clientId, $params) {
        return $this->makeCall("dns_srv_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsSrvUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_srv_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsSrvDelete($primaryId) {
        return $this->makeCall("dns_srv_delete", $this->getSessionId(), $primaryId);
    }

    public function dnsTxtGet($primaryId) {
        return $this->makeCall("dns_txt_get", $this->getSessionId(), $primaryId);
    }

    public function dnsTxtAdd($clientId, $params) {
        return $this->makeCall("dns_txt_add", $this->getSessionId(), $clientId, $params);
    }

    public function dnsTxtUpdate($clientId, $primaryId, $params) {
        return $this->makeCall("dns_txt_update", $this->getSessionId(), $clientId, $primaryId, $params);
    }

    public function dnsTxtDelete($primaryId) {
        return $this->makeCall("dns_txt_delete", $this->getSessionId(), $primaryId);
    }

    public function klientadd($formdefFile, $resellerId, $params) {
        return $this->makeCall("klientadd", $formdefFile, $resellerId, $params);
    }

    public function insertQuery($formdefFile, $clientId, $params, $eventIdentifier) {
        return $this->makeCall("insertQuery", $formdefFile, $clientId, $params, $eventIdentifier);
    }

    public function insertQueryPrepare($formdefFile, $clientId, $params) {
        return $this->makeCall("insertQueryPrepare", $formdefFile, $clientId, $params);
    }

    public function insertQueryExecute($sql, $params, $eventIdentifier) {
        return $this->makeCall("insertQueryExecute", $sql, $params, $eventIdentifier);
    }

    public function updateQuery($formdefFile, $clientId, $primaryId, $params, $eventIdentifier) {
        return $this->makeCall("updateQuery", $formdefFile, $clientId, $primaryId, $params, $eventIdentifier);
    }

    public function updateQueryPrepare($formdefFile, $clientId, $primaryId, $params) {
        return $this->makeCall("updateQueryPrepare", $formdefFile, $clientId, $primaryId, $params);
    }

    public function updateQueryExecute($sql, $primaryId, $params, $eventIdentifier) {
        return $this->makeCall("updateQueryExecute", $sql, $primaryId, $params, $eventIdentifier);
    }

    public function deleteQuery($formdefFile, $primaryId, $eventIdentifier) {
        return $this->makeCall("deleteQuery", $formdefFile, $primaryId, $eventIdentifier);
    }

    public function checkPerm($functionName) {
        return $this->makeCall("checkPerm", $this->getSessionId(), $functionName);
    }

    public function getSession() {
        return $this->makeCall("getSession", $this->getSessionId());
    }

    public function clientGetSitesByUser($sysUserid, $sysGroupid) {
        return $this->makeCall("client_get_sites_by_user", $this->getSessionId(), $sysUserid, $sysGroupid);
    }

    public function sitesGetAll() {
        return $this->makeCall("sites_get_all", $this->getSessionId());
    }

    public function sitesWebDomainSetStatus($primaryId, $status) {
        return $this->makeCall("sites_web_domain_set_status", $this->getSessionId(), $primaryId, $status);
    }

    public function clientGetByUsername($username) {
        return $this->makeCall("client_get_by_username", $this->getSessionId(), $username);
    }

    public function clientGetAll() {
        return $this->makeCall("client_get_all", $this->getSessionId());
    }

    public function clientChangePassword($clientId, $newPassword) {
        return $this->makeCall("client_change_password", $this->getSessionId(), $clientId, $newPassword);
    }

    public function mailDomainGetByDomain($domain) {
        return $this->makeCall("mail_domain_get_by_domain", $this->getSessionId(), $domain);
    }

    public function getFunctionList() {
        return $this->makeCall("get_function_list", $this->getSessionId());
    }

    public function sitesDatabaseGetAllByUser($clientId) {
        return $this->makeCall("sites_database_get_all_by_user", $this->getSessionId(), $clientId);
    }

    public function clientTemplatesGetAll() {
        return $this->makeCall("client_templates_get_all", $this->getSessionId());
    }

    public function dnsZoneGetByUser($clientId, $serverId) {
        return $this->makeCall("dns_zone_get_by_user", $this->getSessionId(), $clientId, $serverId);
    }

    public function dnsRrGetAllByZone($zoneId) {
        return $this->makeCall("dns_rr_get_all_by_zone", $this->getSessionId(), $zoneId);
    }

    public function dnsZoneSetStatus($primaryId, $status) {
        return $this->makeCall("dns_zone_set_status", $this->getSessionId(), $primaryId, $status);
    }

    public function mailDomainSetStatus($primaryId, $status) {
        return $this->makeCall("mail_domain_set_status", $this->getSessionId(), $primaryId, $status);
    }

    public function openvzOstemplateGet($ostemplateId) {
        return $this->makeCall("openvz_ostemplate_get", $this->getSessionId(), $ostemplateId);
    }

    public function openvzOstemplateAdd($clientId, $params) {
        return $this->makeCall("openvz_ostemplate_add", $this->getSessionId(), $clientId, $params);
    }

    public function openvzOstemplateUpdate($clientId, $ostemplateId, $params) {
        return $this->makeCall("openvz_ostemplate_update", $this->getSessionId(), $clientId, $ostemplateId, $params);
    }

    public function openvzOstemplateDelete($ostemplateId) {
        return $this->makeCall("openvz_ostemplate_delete", $this->getSessionId(), $ostemplateId);
    }

    public function openvzTemplateGet($templateId) {
        return $this->makeCall("openvz_template_get", $this->getSessionId(), $templateId);
    }

    public function openvzTemplateAdd($clientId, $params) {
        return $this->makeCall("openvz_template_add", $this->getSessionId(), $clientId, $params);
    }

    public function openvzTemplateUpdate($clientId, $templateId, $params) {
        return $this->makeCall("openvz_template_update", $this->getSessionId(), $clientId, $templateId, $params);
    }

    public function openvzTemplateDelete($templateId) {
        return $this->makeCall("openvz_template_delete", $this->getSessionId(), $templateId);
    }

    public function openvzIpGet($ipId) {
        return $this->makeCall("openvz_ip_get", $this->getSessionId(), $ipId);
    }

    public function openvzGetFreeIp($serverId) {
        return $this->makeCall("openvz_get_free_ip", $this->getSessionId(), $serverId);
    }

    public function openvzIpAdd($clientId, $params) {
        return $this->makeCall("openvz_ip_add", $this->getSessionId(), $clientId, $params);
    }

    public function openvzIpUpdate($clientId, $ipId, $params) {
        return $this->makeCall("openvz_ip_update", $this->getSessionId(), $clientId, $ipId, $params);
    }

    public function openvzIpDelete($ipId) {
        return $this->makeCall("openvz_ip_delete", $this->getSessionId(), $ipId);
    }

    public function openvzVmGet($vmId) {
        return $this->makeCall("openvz_vm_get", $this->getSessionId(), $vmId);
    }

    public function openvzVmGetByClient($clientId) {
        return $this->makeCall("openvz_vm_get_by_client", $this->getSessionId(), $clientId);
    }

    public function openvzVmAdd($clientId, $params) {
        return $this->makeCall("openvz_vm_add", $this->getSessionId(), $clientId, $params);
    }

    public function openvzVmAddFromTemplate($clientId, $ostemplateId, $templateId, $overrideParams) {
        return $this->makeCall("openvz_vm_add_from_template", $this->getSessionId(), $clientId, $ostemplateId, $templateId, $overrideParams);
    }

    public function openvzVmUpdate($clientId, $vmId, $params) {
        return $this->makeCall("openvz_vm_update", $this->getSessionId(), $clientId, $vmId, $params);
    }

    public function openvzVmDelete($vmId) {
        return $this->makeCall("openvz_vm_delete", $this->getSessionId(), $vmId);
    }

    public function openvzVmStart($vmId) {
        return $this->makeCall("openvz_vm_start", $this->getSessionId(), $vmId);
    }

    public function openvzVmStop($vmId) {
        return $this->makeCall("openvz_vm_stop", $this->getSessionId(), $vmId);
    }

    public function openvzVmRestart($vmId) {
        return $this->makeCall("openvz_vm_restart", $this->getSessionId(), $vmId);
    }

}
