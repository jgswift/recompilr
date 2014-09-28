<?php
namespace recompilr\Binary {
    use recompilr\Compiler\CompilerInterface;
    
    interface BinaryInterface {
        /**
         * Retrieve file name
         */
        public function getFileName();
        
        /**
         * Perform binary save procedure
         */
        public function save(CompilerInterface $compiler);
        
        /**
         * Perform loading of binary into memory
         */
        public function load(CompilerInterface $compiler = null);
    }
}