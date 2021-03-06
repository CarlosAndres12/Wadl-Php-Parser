<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 13/11/2014
 * Time: 05:36 PM
 */

include_once 'DynamicObject.php';
include_once 'WadlObject.php';
include_once 'WadlElementResolver.php';
include_once 'WadlMethod.php';
include_once 'WadlParam.php.';
include_once 'WadlRepresentation.php';
include_once 'WadlRequest.php';
include_once 'WadlResource.php';
include_once 'WadlResourceType.php';
include_once 'WadlResponse.php';


class WadlApplication extends DynamicObject{

    protected $includeApplications = array();

    protected $resourceBase = '';
    protected $resources = array();
    protected $resourceTypes = array();
    protected $methods = array();
    protected $representations = array();
    protected $params = array();

    protected $filename;
    protected $basename;

    public static function load($filename)
    {
        $xmlWadl = simplexml_load_file($filename);
        return self::loadXml($xmlWadl, $filename);
    }
    public static function loadString($string)
    {
        $xmlWadl = simplexml_load_string($string);
        return self::loadXml($xmlWadl, 'noname.wadl');
    }
    public static function loadXml($xmlWadl, $filename)
    {
        $application = new self();
        $application->filename = $filename;
        $application->basename = basename($filename);

        if (!empty($xmlWadl->resources)) {

            $application->resourceBase = (string)$xmlWadl->resources['base'];
            if (($xmlWadl->resources->resource) == null) {
                throw new Exception('No resource found in resources');
            }
            $application->resources = WadlResource::loadAll($xmlWadl->resources->resource, $application);
        }

        $elementResolver = WadlElementResolver::getInstance();
        $basename = basename($filename);
        if (!empty($xmlWadl->resource_type)) {

            $application->resourceTypes = WadlResourceType::loadAll($xmlWadl->resource_type, $application);
            foreach ($application->resourceTypes as $resourceType) {
                $elementResolver->register($basename, $resourceType);
            }
        }
        if (!empty($xmlWadl->method)) {

            $application->methods = WadlMethod::loadAll($xmlWadl->method, $application);
            foreach ($application->methods as $method) {
                $elementResolver->register($basename, $method);
            }
        }
        if (!empty($xmlWadl->representation)) {

            $application->representations = WadlRepresentation::loadAll($xmlWadl->representation, $application);
            foreach ($application->representations as $representation) {
                $elementResolver->register($basename, $representation);
            }
        }
        if (!empty($xmlWadl->param)) {

            $application->params = WadlParam::loadAll($xmlWadl->param, $application);
            foreach ($application->params as $param) {
                $elementResolver->register($basename, $param);
            }
        }
        return $application;
    }

    public function import($filename) {
        $this->includeApplications[] = self::load($filename);
        return $this;
    }

    public function bind() {
        foreach ($this->includeApplications as $application) {
            $application->bind();
        }

        // don't lazy binding params, representations, methods and reource_types
        WadlParam::bindAll($this->params, $this);
        WadlRepresentation::bindAll($this->representations, $this);
        WadlMethod::bindAll($this->methods, $this);
        WadlResourceType::bindAll($this->resourceTypes, $this);

        WadlResource::bindAll($this->resources, $this);
        $this->buildResourceUri();

        return $this;
    }
    private function buildResourceUri()
    {
        foreach ($this->resources as $resource) {
            $resource->buildResourceUri($this->resourceBase);
        }
    }


}


