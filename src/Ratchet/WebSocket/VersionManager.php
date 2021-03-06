<?php
namespace Ratchet\WebSocket;
use Ratchet\WebSocket\Version\VersionInterface;
use Guzzle\Http\Message\RequestInterface;

class VersionManager {
    private $versionString = '';

    protected $versions = array();

    /**
     * Get the protocol negotiator for the request, if supported
     * @param Guzzle\Http\Message\RequestInterface
     * @return Ratchet\WebSocket\Version\VersionInterface
     */
    public function getVersion(RequestInterface $request) {
        foreach ($this->versions as $version) {
            if ($version->isProtocol($request)) {
                return $version;
            }
        }

        throw new \InvalidArgumentException("Version not found");
    }

    /**
     * @param Guzzle\Http\Message\RequestInterface
     * @return bool
     */
    public function isVersionEnabled(RequestInterface $request) {
        foreach ($this->versions as $version) {
            if ($version->isProtocol($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enable support for a specific version of the WebSocket protocol
     * @param Ratchet\WebSocket\Vesion\VersionInterface
     * @return HandshakeNegotiator
     */
    public function enableVersion(VersionInterface $version) {
        $this->versions[$version->getVersionNumber()] = $version;

        if (empty($this->versionString)) {
            $this->versionString = (string)$version->getVersionNumber();
        } else {
            $this->versionString .= ", {$version->getVersionNumber()}";
        }

        return $this;
    }

    /**
     * Disable support for a specific WebSocket protocol version
     * @param int The version ID to un-support
     * @return HandshakeNegotiator
     */
    public function disableVersion($versionId) {
        unset($this->versions[$versionId]);

        $this->versionString = implode(',', array_keys($this->versions));

        return $this;
    }

    /**
     * Get a string of version numbers supported (comma delimited)
     * @return string
     */
    public function getSupportedVersionString() {
        return $this->versionString;
    }
}