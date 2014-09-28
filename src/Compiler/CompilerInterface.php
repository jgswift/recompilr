<?php
namespace recompilr\Compiler {
    interface CompilerInterface {
        /**
         * Retrieve class registry
         */
        public function getClassRegistry();
        
        /**
         * map class to alias
         */
        public function map($class,$id);
        
        /**
         * adds class from file
         */
        public function execute($class,$file=null);
        
        /**
         * get aliased class name
         */
        public function name($class);
        
        /**
         * get list of aliased class names
         */
        public function names();
        
        /**
         * recompiles all mapped classes
         */
        public function all();
        
        /**
         * instantiated mapped class
         */
        public function instance($class, array $args = []);
        
        /**
         * checks if compiler maps class
         */
        public function isMapping($class);
    }
}