<?php

namespace DockerGiant\SatisGitlab\Satis;

/**
 * Incremental satis configuration builder
 * 
 * @author dockergiant
 */
class ConfigBuilder {

    /**
     * resulting configuration
     */
    protected $config ;

    /**
     * Init configuration with a template
     * @param $templatePath string path to the template
     */
    public function __construct( $templatePath = null )
    {
        if ( empty($templatePath) ){
            $templatePath = dirname(__FILE__).'/../Resources/default-template.json';
        }
        $this->config = json_decode(file_get_contents($templatePath),true);
    }

    /**
     * Get resulting configuration
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * Set name
     */
    public function setName($name){
        $this->config['name'] = $name;

        return $this;
    }

    /**
     * Set homepage
     * @param $homepage
     * @return ConfigBuilder
     */
    public function setHomepage($homepage){
        $this->config['homepage'] = $homepage;

        return $this;
    }

    /**
     * Turn on mirror mode
     * @return void
     */
    public function enableArchive(){
        $this->config['archive'] = array(
            'directory' => 'dist',
            'format' => 'zip',
            'skip-dev' => true
        );
    }

    /**
     * Add gitlab domain to config
     * @param $gitlabDomain
     * @return ConfigBuilder
     */
    public function addGitlabDomain($gitlabDomain){
        if ( ! isset($this->config['config']) ){
            $this->config['config'] = array();
        }
        if ( ! isset($this->config['config']['gitlab-domains']) ){
            $this->config['config']['gitlab-domains'] = array();
        }

        $this->config['config']['gitlab-domains'][] = $gitlabDomain ;

        return $this;
    }

    /**
     * Add gitlab token
     *
     * @param $gitlabDomain
     * @param $gitlabAuthToken
     * @return ConfigBuilder
     */
    public function addGitlabToken($gitlabDomain, $gitlabAuthToken){
        if ( ! isset($this->config['config']['gitlab-token']) ){
            $this->config['config']['gitlab-token'] = array();
        }
        $this->config['config']['gitlab-token'][$gitlabDomain] = $gitlabAuthToken;

        return $this;
    }

    // Function to convert HTTPS URL to SSH URL
    function convertHttpsToSshUrl($httpsUrl) {
        // Parse the URL to get its components
        $parsedUrl = parse_url($httpsUrl);

        // Extract the host and path
        $host = $parsedUrl['host'];
        $path = ltrim($parsedUrl['path'], '/'); // Remove leading slash for SSH format

        // Construct the SSH URL
        return "git@" . $host . ":" . $path;
    }

    /**
     * Add a repository to satis
     *
     * @param string $projectName "{vendorName}/{componentName}"
     * @param string $projectUrl
     * @param boolean $unsafeSsl allows to disable ssl checks
     *
     * @return void
     */
    public function addRepository(
        $projectName,
        $projectUrl,
        $unsafeSsl = false
    ){
        if ( ! isset($this->config['repositories']) ){
            $this->config['repositories'] = array();
        }

        $repository = array(
            'type' => 'vcs',
            'url' => $this->convertHttpsToSshUrl($projectUrl),
        );

        if ( $unsafeSsl ){
            $repository['options'] = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                    "allow_self_signed" => true
                ]
            ];
        }

        $this->config['repositories'][] = $repository ;
        $this->config['require'][$projectName] = '*';
    }

}