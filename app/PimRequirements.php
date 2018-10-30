<?php

require_once __DIR__ . '/../var/SymfonyRequirements.php';

use Akeneo\Platform\CommunityRequirements;
use Akeneo\Platform\Requirement as PlatformRequirement;

/**
 * Akeneo PIM requirements
 *
 * This class specifies all requirements and optional recommendations that are necessary
 * to install and run Akeneo PIM application
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimRequirements extends SymfonyRequirements
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $directoriesToCheck = [])
    {
        parent::__construct();

        $communityRequirements = new CommunityRequirements(__DIR__.'/..', $directoriesToCheck);

        foreach($communityRequirements->getRequirements() as $requirement) {
            if ($requirement->isMandatory()) {
                $this->addPimRequirement($requirement);
            } else {
                $this->addRecommendation(
                    $requirement->isFullfilled(),
                    $requirement->getTestMessage(),
                    $requirement->getHelpText()
                );
            }
        }
    }

    /**
     * Adds an Akeneo PIM specific mandatory requirement
     */
    private function addPimRequirement(PlatformRequirement $requirement)
    {
        $this->add(
            new PimRequirement(
                $requirement->isFullfilled(),
                $requirement->getTestMessage(),
                $requirement->getHelpText()
            )
        );
    }

    /**
     * Get the list of Akeneo PIM specific requirements
     */
    public function getPimRequirements(): array
    {
        return array_filter($this->getRequirements(), function ($requirement) {
            return $requirement instanceof PimRequirement;
        });
    }

    /**
     * Gets the MySQL server version thanks to a PDO connection.
     *
     * If no connection is reached, or that "parameters.yml" do not exists, an
     * exception is thrown, then catch. If "parameters_test.yml" do not exists
     * either, then the exception is thrown again.
     * If it exits, an attempt to connect is done, and can result in an exception
     * if no connection is reached.
     *
     * @return string
     */
    protected function getMySQLVersion()
    {
        $file = file_get_contents(__DIR__.'/config/parameters.yml');

        if (false === $file) {
            throw new RuntimeException(
                'The file config/parameters.yml does not exist, please create it'
            );
        }

        $parameters = Yaml::parse($file);

        try {
            if (null === $parameters) {
                throw new RuntimeException(
                    'Your PIM is not configured. Please fill the file "app/config/parameters.yml"'
                );
            }

            return $this->getConnection($parameters)->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (RuntimeException $e) {
            $parameters = Yaml::parse(file_get_contents(__DIR__.'/config/parameters_test.yml'));

            if (null === $parameters) {
                throw $e;
            }

            return $this->getConnection($parameters)->getAttribute(PDO::ATTR_SERVER_VERSION);
        }
    }

    /**
     * @param array $parameters
     *
     * @return PDO
     */
    protected function getConnection(array $parameters)
    {
        return new PDO(
            sprintf(
                'mysql:port=%s;host=%s',
                $parameters['parameters']['database_port'],
                $parameters['parameters']['database_host']
            ),
            $parameters['parameters']['database_user'],
            $parameters['parameters']['database_password']
        );
    }

    /**
     * @param  string $val
     * @return int
     */
    protected function getBytes($val)
    {
        if (empty($val)) {
            return 0;
        }

        preg_match('/([\-0-9]+)[\s]*([a-z]*)$/i', trim($val), $matches);

        if (isset($matches[1])) {
            $val = (int) $matches[1];
        }

        switch (strtolower($matches[2])) {
            case 'g':
            case 'gb':
                $val *= 1024;
            // no break
            case 'm':
            case 'mb':
                $val *= 1024;
            // no break
            case 'k':
            case 'kb':
                $val *= 1024;
            // no break
        }

        return (float) $val;
    }

    /**
     * Uses requirements from CE dev
     * Get the list of mandatory requirements (all requirements excluding PhpIniRequirement)
     */
    public function getMandatoryRequirements(): array
    {
        return array_filter($this->getRequirements(), function ($requirement) {
            return !($requirement instanceof PhpIniRequirement) && !($requirement instanceof PimRequirement);
        });
    }

    /**
     * Get the list of PHP ini requirements
     */
    public function getPhpIniRequirements(): array
    {
        return array_filter($this->getRequirements(), function ($requirement) {
            return $requirement instanceof PhpIniRequirement;
        });
    }
}

/**
 * PimRequirement class
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimRequirement extends Requirement
{
}
