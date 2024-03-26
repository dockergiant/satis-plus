<?php

namespace DockerGiant\SatisGitlab\GitFilter;

use Psr\Log\LoggerInterface;

use MBO\RemoteGit\ProjectInterface;
use MBO\RemoteGit\ProjectFilterInterface;
use MBO\RemoteGit\ClientInterface as GitClientInterface;

/**
 * Filter projects based on GitLab project namespace name or id.
 *
 * @author roygoldman
 */
class GitlabNamespaceFilter implements ProjectFilterInterface {
    /**
     * @var string[]
     */
    protected array $groups;

    /**
     * @var GitClientInterface
     */
    protected GitClientInterface $gitClient;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * GitlabNamespaceFilter constructor.
     *
     * @param $groups
     */
    public function __construct($groups)
    {
        assert(!empty($groups));
        $this->groups = explode(',',strtolower($groups));
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return "gitlab namespace should be one of [".implode(', ',$this->groups)."]";
    }

    /**
     * {@inheritDoc}
     */
    public function isAccepted(ProjectInterface $project): bool
    {
        $project_info = $project->getRawMetadata();
        if (isset($project_info['namespace'])) {
            
            // Extra data from namespace to patch on.
            $valid_keys = [
                'name' => 'name',
                'id' => 'id',
            ];
            $namespace_info = array_intersect_key($project_info['namespace'], $valid_keys);
            $namespace_info = array_map('strtolower', $namespace_info);
            
            if (!empty($namespace_info) && !empty(array_intersect($namespace_info, $this->groups))) {
                // Accept any package with a permitted namespace name or id.
                return true;
            }
        }
        return false;
    }
}
