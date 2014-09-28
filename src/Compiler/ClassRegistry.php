<?php
namespace recompilr\Compiler {
    class ClassRegistry {
        /**
         * List of ClassMappings
         * @var array
         */
        private $classMapping = [];
        
        /**
         * Default registry constructor
         * @param array $classMappings
         * @throws \InvalidArgumentException
         */
        function __construct(array $classMappings = []) {
            if(!empty($classMappings)) {
                foreach($classMappings as $mapping) {
                    if($mapping instanceof ClassMapping) {
                        $this->addMap($mapping);
                    } else {
                        throw new \InvalidArgumentException("Only accepts an array of ClassMapping instances");
                    }
                }
            }
        }
        
        /**
         * alias @see setMap
         * @param \recompilr\Compiler\ClassMapping $map
         */
        function addMap(ClassMapping $map) {
            $this->setMap($map);
        }
        
        /**
         * alias $see unsetMap
         * @param \recompilr\Compiler\ClassMapping $map
         */
        function removeMap(ClassMapping $map) {
            $this->unsetMap($map);
        }
        
        /**
         * add ClassMapping to registry
         * @param \recompilr\Compiler\ClassMapping $map
         */
        function setMap(ClassMapping $map) {
            $className = $map->getClassName();
            $this->classMapping[$className] = $map;
        }
        
        /**
         * remvoes ClassMapping from registry
         * @param \recompilr\Compiler\ClassMapping $map
         */
        function unsetMap(ClassMapping $map) {
            $className = $map->getClassName();
            unset($this->classMapping[$className]);
        }
        
        /**
         * Retrieve map if exists
         * @param string $className
         * @return \recompilr\Compiler\ClassMapping
         */
        function getMap($className) {
            if(isset($this->classMapping[$className])) {
                return $this->classMapping[$className];
            }
        }
        
        /**
         * Retrieve all class mappings
         * @return array
         */
        function getClassMapping() {
            return $this->classMapping;
        }
    }
}
