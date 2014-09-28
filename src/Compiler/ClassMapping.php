<?php
namespace recompilr\Compiler {
    class ClassMapping {
        /**
         * local class name
         * @var string
         */
        private $className;
        
        /**
         * local file name
         * @var string 
         */
        private $fileName;
        
        /**
         * local class alias
         * @var string 
         */
        private $currentName;
        
        /**
         * last time file was updated
         * @var integer
         */
        private $lastModified;
        
        /**
         * Prevent mapping of autoloaded classes
         * @var boolean
         */
        private $mapping = false;
        
        /**
         * Default ClassMapping constructor
         * @param string $className
         * @param string $fileName
         * @param string $currentName
         */
        public function __construct($className, $fileName = null, $currentName=null) {
            $this->className = $className;
            if(!is_null($fileName)) {
                $this->fileName = $fileName;
            }
            if(!is_null($currentName)) {
                $this->currentName = $currentName;
            }
            
            if(is_null($fileName) && class_exists($className)) {
                $reflectionClass = new \ReflectionClass($className);
                $this->setFileName($reflectionClass->getFileName());
            } else {
                $this->mapping = true;
            }
        }
        
        /**
         * Checks if class does map
         * @return boolean
         */
        public function isMapped() {
            return $this->mapping;
        }
        
        /**
         * Retrieve class alias
         * @return string
         */
        public function getCurrentName() {
            return $this->currentName;
        }
        
        /**
         * Update class alias
         * @param string $newName
         * @return string $newName
         */
        public function setCurrentName($newName) {
            return $this->currentName = $newName;
        }
        
        /**
         * Retrieve original class name
         * @return string
         */
        public function getClassName() {
            return $this->className;
        }
        
        /**
         * Retrieve class file location
         * @return string
         */
        public function getFileName() {
            return $this->fileName;
        }
        
        /**
         * Update file location
         * @param string $fileName
         * @return string
         */
        public function setFileName($fileName) {
            if(empty($this->fileName)) {
                $this->lastModified = filemtime($this->fileName);
            }
            return $this->fileName = $fileName;
        }
        
        /**
         * Retrieve file last modification time
         * @return integer
         */
        public function getLastModified() {
            if(empty($this->lastModified)) {
                $this->lastModified = filemtime($this->fileName);
            }
            
            return $this->lastModified;
        }
        
        /**
         * Check if file was updated
         * @return boolean
         */
        public function isUpdated() {
            $lastModified = $this->getLastModified();
            
            $newModified = filemtime($this->fileName);
            
            if($lastModified !== $newModified) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Helper function to transform class to short name of class
         * @param mixed $object
         * @return boolean|string
         */
        private function getShortNameOf($object) {
            if(!is_object($object) && !is_string($object)) {
                return false;
            }

            $class = explode('\\', (is_string($object) ? $object : get_class($object)));
            return $class[count($class) - 1];
        }
        
        /**
         * Retrieve shortname of alias
         * @return string
         */
        public function getShortName() {
            return $this->getShortNameOf($this->currentName);
        }
        
        /**
         * Retrieve shortname of original class name
         * @return string
         */
        public function getShortClassName() {
            return $this->getShortNameOf($this->className);
        }
        
        /**
         * Retrieve namespace of class alias
         * @return boolean
         */
        public function getNamespace() {
            $object = $this->currentName;
            if(!is_object($object) && !is_string($object)) {
                return false;
            }

            $class = explode('\\', (is_string($object) ? $object : get_class($object)));
            unset($class[count($class) - 1]);
            return implode('\\',$class);
        }
        
        /**
         * Recreate alias
         * @return string
         */
        protected function makeStub() {
            return $this->currentName = uniqid($this->className.'_');
        }
        
        /**
         * Create instance of aliased class
         * @param array $args
         * @return mixed
         */
        public function instance(array $args = []) {
            if(!class_exists($this->currentName)) {
                $this->currentName = $this->className;
            }

            if(!empty($args)) {
                $reflectionClass = new \ReflectionClass($this->currentName);

                return $reflectionClass->newInstanceArgs($args);
            }
            
            if(class_exists($this->currentName,false)) {
                return new $this->currentName;
            }
        }
        
        /**
         * Retrieve source
         * @return string
         */
        public function getSource() {
            if(!class_exists($this->currentName,false)) {
                return '';
            }
            
            $class = new \ReflectionClass($this->currentName);
            $fileName = $class->getFileName();
            $startLine = $class->getStartLine()-1; // getStartLine() seems to start after the {, we want to include the signature
            $endLine = $class->getEndLine();
            $numLines = $endLine - $startLine;

            return trim(implode('',array_slice(file($fileName),$startLine,$numLines))); 
        }
        
        /**
         * Recreate source
         * @return string
         */
        public function recompile() {
            $this->makeStub();
            $contents = file_get_contents($this->getFileName());
            
            $newID = $this->getShortName();
            $start = strpos($contents,'class ');
            $classStart = strpos($contents,' ',$start+6);
            $open = strpos($contents,'{',$start);
            $pre = substr($contents,0,$start);
            $classLen = $open-$classStart;
            $between = substr($contents,$classStart,$classLen);
                    
            $classBody = substr($contents,$open);
            
            $classSignature = 'class '.$newID;
            
            return $pre.$classSignature.$between.' '.$classBody;
        }
    }
}
