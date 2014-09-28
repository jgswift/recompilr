<?php
namespace recompilr\Binary {
    use recompilr\Compiler\CompilerBinary;
    use recompilr\Compiler\AggregateCompiler;
    
    class AggregateBinary extends CompilerBinary {
        /**
         * Loads multiple binaries
         * @param \recompilr\Compiler\CompilerInterface $compiler
         */
        public function load(\recompilr\Compiler\CompilerInterface $compiler = null) {
            if($compiler instanceof AggregateCompiler) {
                $compilers = $compiler->getCompilers();
                foreach($compilers as $compiler) {
                    parent::load($compiler);
                }
            } else {
                parent::load($compiler);
            }
        }
        
        /**
         * Saves multiple binaries in a single file
         * @param \recompilr\Compiler\AggregateCompiler $compiler
         */
        public function save(\recompilr\Compiler\CompilerInterface $compiler) {
            $contents = '';
            
            if($compiler instanceof AggregateCompiler) {
                $compilers = $compiler->getCompilers();
                foreach($compilers as $compiler) {
                    $contents .= parent::recompile($compiler);
                }

                $contents = '<?php '.$contents;
                $bin = base64_encode($contents);
                file_put_contents($this->fileName,$bin);
            } else {
                parent::save($compiler);
            }
        }
    }
}