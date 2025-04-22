<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class VersioningService
{
    private string $defaultVersion;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->requestStack = $requestStack;
        $this->defaultVersion = $params->get('default_api_version');
    }

    public function getVersion(): string
    {
        $version = $this->defaultVersion;

        $request = $this->requestStack->getCurrentRequest();
        $accept = $request?->headers->get('Accept'); // null safe pour éviter une erreur si $request est null

        if ($accept) {
            $entete = explode(';', $accept);

            foreach ($entete as $value) {
                if (strpos($value, 'version') !== false) {
                    $versionParts = explode('=', $value);
                    if (isset($versionParts[1])) {
                        $version = trim($versionParts[1]);
                    }
                    break;
                }
            }
        }

        return $version;
    }
}
