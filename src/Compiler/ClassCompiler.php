<?php
namespace recompilr\Compiler {
    use Veval;
    use Vfs\Node\File;
    use recompilr\Compiler\ClassRegistry;
    use recompilr\Compiler\ClassMapping;
    use recompilr\Compiler\CompilerInterface;
    use recompilr\Recompiler;
    
    class ClassCompiler implements CompilerInterface {
        /**
         * Local registry
         * @var \recompilr\Compiler\ClassRegistry 
         */
        private $classRegistry;
        
        /**
         * Default compiler constructor
         * @param \recompilr\Compiler\ClassRegistry $classRegistry
         */
        public function __construct(ClassRegistry $classRegistry = null) {
            if(is_null($classRegistry)) {
                $classRegistry = new ClassRegistry();
            }
            
            $this->classRegistry = $classRegistry;
        }
        
        /**
         * Retrieve registry
         * @return \recompilr\Compiler\ClassRegistry
         */
        public function getClassRegistry() {
            return $this->classRegistry;
        }
        
        /**
         * Maps class to new name
         * @param string $class
         * @param string $id
         * @return null
         */
        public function map($class,$id) {
            $map = $this->classRegistry->getMap($class);
            if(!empty($map)) {
                return;
            }
            
            $map = new ClassMapping($class,null,$id);
            
            $cName = str_replace('\\','_',$class);
            Recompiler::getFileSystem()->get('/')->add($cName.'.php', new File($map->getSource()));
            $fileName = 'recompilr://'.$cName.'.php';
            
            $map->setFileName($fileName);
            
            $this->classRegistry->addMap($map);
        }
        
        /**
         * Checks if compiler maps class
         * @param string $class
         * @return boolean
         */
        public function isMapping($class) {
            $map = $this->classRegistry->getMap($class);
            
            return !empty($map);
        }
        
        /**
         * Recompile class
         * @param string $class
         * @param string $file
         * @return null
         * @throws \InvalidArgumentException
         */
        public function execute($class,$file=null) {
            $map = $this->classRegistry->getMap($class);
            
            if(empty($map)) {
                if(is_null($file) && !class_exists($class)) {
                    throw new \InvalidArgumentException($class.' does not exist');
                }
                $map = new ClassMapping($class,$file);
                $this->classRegistry->addMap($map);
                if(!is_null($file)) {
                    $this->compile($map);
                }
            } else {
                if(is_null($file)) {
                    if($map->isUpdated()) {
                        $file = $map->getFileName();
                    } else {
                        return;
                    }
                } else {
                    $map->setFileName($file);
                }
            
                $this->compile($map);
            }
        }
        
        /**
         * Performs compilation using veval
         * @param \recompilr\Compiler\ClassMapping $map
         */
        protected function compile(ClassMapping $map) {
            Veval::execute($map->recompile());
        }
        
        /**
         * Retrieve mapped class name
         * @param string $class
         * @return string
         */
        public function name($class) {
            return $this->classRegistry->getMap($class)->getCurrentName();
        }
        
        /**
         * Retrieve mapped class names keyed by alias
         * @return string
         */
        public function names() {
            $maps = $this->classRegistry->getClassMapping();
            
            $result = [];
            foreach($maps as $map) {
                $key = str_replace('\\','__',$map->getCurrentName());
                $result[$key] = $map->getCurrentName();
            }
            
            return $result;
        }
        
        /**
         * Recompiles all updated classes
         */
        public function all() {
            $maps = $this->classRegistry->getClassMapping();
            
            if(!empty($maps)) {
                foreach($maps as $map) {
                    if($map->isUpdated()) {
                        $this->compile($map);
                    }
                }
            }
        }
        
        /**
         * Creates instance of mapped class
         * @param string $class
         * @param array $args
         * @return mixed
         */
        public function instance($class, array $args = []) {
            $map = $this->classRegistry->getMap($class);
            
            if($map instanceof ClassMapping) {
                return $map->instance($args);
            } else {
                $this->execute($class);
                return $this->instance($class,$args);
            }
        }
    }
}
