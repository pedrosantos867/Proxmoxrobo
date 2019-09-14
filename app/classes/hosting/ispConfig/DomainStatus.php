<?php

namespace hosting\ispConfig;

class DomainStatus {

    public $domain;
    public $exists;
    public $error = false;

    public static function create($domain = null, $exists = null) {
        return new self($domain, $exists);
    }

    public function __construct($domain = null, $exists = null) {
        $this->domain = $domain;
        $this->exists = $exists;
    }

    public function __toString() {
        return $this->domain . " " . ($this->exists ? "true" : "false");
    }

}
