<?php
namespace recompilr\Binary\PharBinary {
    use recompilr\Exception;
    use recompilr\Compiler\AggregateCompiler;
    
    class PharBinary extends AggregateBinary {
        /**
         * Saves binary in phar archive
         * @param \recompilr\Binary\PharBinary\AggregateCompiler $compiler
         */
        public function save(CompilerInterface $compiler) {
            $contents = '';
            
            if($compiler instanceof AggregateCompiler) {
                $compilers = $compiler->getCompilers();
                foreach($compilers as $compiler) {
                    $contents .= parent::recompile($compiler);
                }
            } else {
                $contents = parent::recompile($compiler);
            }
            
            $phar = new \Phar($this->fileName);
            $phar->addFromString('bin.rcx',$contents);
        }
        
        /**
         * Loads binary from phar archive
         * @param \recompilr\Binary\PharBinary\CompilerInterface $compiler
         * @throws \Exception
         */
        public function load(CompilerInterface $compiler = null) {
            $phar = new \Phar($this->fileName);
            $oldFileName = $this->fileName;
            if(isset($phar['bin.rcx'])) {
                $this->fileName = 'phar://'.$this->fileName.'/bin.rcx';
                parent::load($compiler);
            } else {
                throw new Exception('Invalid binary - "'.$this->fileName.'"');
            }
            $this->fileName = $oldFileName;
        }
    }
}

