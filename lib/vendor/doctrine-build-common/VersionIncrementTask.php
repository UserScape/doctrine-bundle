<?php

require_once 'phing/Task.php';

/**
 * Increments a version number on the mini level.
 *
 * Alpha, Beta, Dev Versions are stripped from the version, assumption is
 * that the next release will be stable.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class VersionIncrementTask extends Task
{
    /** Pattern to match unstable versions */
    const UNSTABLE_PATTERN = '((-RC[0-9]+|-DEV|-ALPHA|-BETA))i';

    protected $version;

    protected $property;

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function setProperty($property)
    {
        $this->property = $property;
    }

    public function init()
    {

    }

    public function main()
    {
        $version = preg_replace(self::UNSTABLE_PATTERN, '', $this->version);
        $parts = explode(".", $version);
        if (count($parts) != 3) {
            throw new \InvalidArgumentException("Version is assumed in format x.y.z, $this->version given");
        }
        if (!preg_match(self::UNSTABLE_PATTERN, $this->version)) {
            $parts[2]++;
        }
        $this->project->setProperty($this->property, implode(".", $parts));
    }
}
