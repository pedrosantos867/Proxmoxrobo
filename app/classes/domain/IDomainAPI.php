<?php


namespace domain;

use model\DomainOrder;
use model\DomainOwner;

interface IDomainAPI
{
    public function createPerson(DomainOwner $owner);

    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $owner);

    public function checkDomainAvailable( $domain);

    public function checkDomainsAvailable($domains);

    public function createContactPerson(DomainOwner $owner, $contract_id);

    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $DomainOwner);

    public function changeNS(DomainOrder $DomainOrder, $old_ns_array);

    public function changeContactPerson(DomainOrder $domainOrder, DomainOwner $DomainOwner);

    public function reqPool();


    public function getErrorCode();


}