<?php
namespace recompilr\Compiler {
    use recompilr\Compiler\ClassCompiler;
    
    class AggregateCompiler implements CompilerInterface {
        /**
         * Local list of compilers
         * @var \recompilr\Compiler\CompilerInterface
         */
        private $compilers = [];
        
        /**
         * Local class registry (applied to all compilers)
         * @var \recompilr\Compiler\ClassRegistry 
         */
        private $classRegistry;
        
        /**
         * Aggregate compiler constructor
         * Accepts registry but doesn't necessarily need one
         * @param \recompilr\Compiler\ClassRegistry $classRegistry
         */
        public function __construct(ClassRegistry $classRegistry = null) {
            $this->classRegistry = $classRegistry;
            $this->register($this->makeCompiler($classRegistry));
        }
        
        /**
         * Retrieve class registry
         * @return \recompilr\Compiler\ClassRegistry
         */
        public function getClassRegistry() {
            return $this->classRegistry;
        }
        
        /**
         * Add compiler
         * @param \recompilr\Compiler\CompilerInterface $compiler
         */
        public function register(CompilerInterface $compiler) {
            $this->compilers[] = $compiler;
        }
        
        /**
         * Remove compiler
         * @param \recompilr\Compiler\CompilerInterface $compiler
         */
        public function unregister(CompilerInterface $compiler) {
            $key = array_search($compiler,$this->compilers);
            if($key) {
                unset($this->compilers[$key]);
            }
        }
        
        /**
         * Retrieve list of compilers
         * @return \recompilr\Compiler\CompilerInterface
         */
        public function getCompilers() {
            return $this->compilers;
        }
        
        /**
         * Adds class mapping
         * @param string $class
         * @param string $id
         */
        public function map($class,$id) {
            foreach($this->compilers as $compiler) {
                if($compiler->isMapping($class)) {
                    return $compiler->map($class,$id);
                }
            }
            $compiler = $this->compilers[0];
            $compiler->map($class,$id);
        }
        
        /**
         * Checks if compiler is mapping class
         * @param string $class
         * @return boolean
         */
        public function isMapping($class) {
            foreach($this->compilers as $compiler) {
                if($compiler->isMapping($class)) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * Recompile class from file
         * @param string $class
         * @param string $file
         */
        public function execute($class,$file=null) {
            foreach($this->compilers as $compiler) {
                if($compiler->isMapping($class)) {
                    return $compiler->execute($class,$file);
                }
            }
            
            $compiler = $this->compilers[0];
            $compiler->execute($class,$file);
        }
        
        /**
         * Retrieve alias name for class
         * @param string $class
         * @return string
         */
        public function name($class) {
            foreach($this->compilers as $compiler) {
                if($compiler->isMapping($class)) {
                    return $compiler->name($class);
                }
            }

            return 'stdClass';
        }
        
        /**
         * Retrieve list of all compiled class aliases
         * @return type
         */
        public function names() {
            $results = [];
            
            foreach($this->compilers as $compiler) {
                $results = array_merge($results,$compiler->names());
            }
            
            return $results;
        }
        
        /**
         * Recompiles all mapped classes
         */
        public function all() {
            foreach($this->compilers as $compiler) {
                $compiler->all();
            }
        }
        
        /**
         * Create class instance
         * @param string $class
         * @param array $args
         * @return mixed
         */
        public function instance($class, array $args = []) {
            foreach($this->compilers as $compiler) {
                if($compiler->isMapping($class)) {
                    return $compiler->instance($class, $args);
                }
            }
            
            return $this->compilers[0]->instance($class,$args);
        }
        
        /**
         * Helper function to create default empty compiler
         * @param \recompilr\Compiler\ClassRegistry $classRegistry
         * @return \recompilr\Compiler\ClassCompiler
         */
        protected function makeCompiler(ClassRegistry $classRegistry = null) {
            if(is_null($classRegistry)) {
                $classRegistry = new ClassRegistry;
            }
            
            return new ClassCompiler($classRegistry);
        }
    }
}