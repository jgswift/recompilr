<?php
namespace recompilr {
    use Vfs\FileSystem;
    use recompilr\Compiler\AggregateCompiler;
    use recompilr\Compiler\ClassRegistry;
        
    class Recompiler {
        /**
         * globally store filesystems 
         * @var \Vfs\FileSystem
         */
        private static $fileSystem;
        
        /**
         * globally store facade compiler
         * @var \recompilr\Compiler\AggregateCompiler
         */
        private static $compiler;
        
        /**
         * Retrieve global filesystem instance
         * @return Vfs\FileSystem
         */
        public static function getFileSystem() {
            if(empty(self::$fileSystem)) {
                self::$fileSystem = FileSystem::factory('recompilr://');
            }
            return self::$fileSystem;
        }
        
        /**
         * Retrieve compiler facade
         * @return \recompilr\Compiler\AggregateCompiler
         */
        public static function getCompiler() {
            if(!isset(self::$compiler)) {
                self::$compiler = self::makeCompiler();
            }
            
            return self::$compiler;
        }
        
        /**
         * Helper to create default aggregate compiler
         * @param \recompilr\Compiler\ClassRegistry $classRegistry
         * @return \recompilr\Compiler\AggregateCompiler
         */
        private static function makeCompiler(ClassRegistry $classRegistry = null) {
            return new AggregateCompiler($classRegistry);
        }
    }
}