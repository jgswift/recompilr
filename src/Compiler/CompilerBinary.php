<?php
namespace recompilr\Compiler {
    use recompilr\Binary\BinaryInterface;
    
    class CompilerBinary implements BinaryInterface {
        /**
         * Local file path of binary
         * @var string 
         */
        protected $fileName;
        
        /**
         * Default binary constructor
         * @param string $fileName
         */
        function __construct($fileName) {
            $this->fileName = $fileName;
        }
        
        /**
         * Retrieve binary file name
         * @return string
         */
        public function getFileName() {
            return $this->fileName;
        }
        
        /**
         * saves binary to file
         * @param \recompilr\Compiler\CompilerInterface $compiler
         * @return boolean
         */
        public function save(CompilerInterface $compiler) {
            $content = '<?php '.$this->recompile($compiler);
            $bin = base64_encode($content);
            file_put_contents($this->fileName,$bin);
        }
        
        /**
         * load binary from file
         * @param \recompilr\Compiler\CompilerInterface $compiler
         */
        public function load(CompilerInterface $compiler = null) {
            if(is_file($this->fileName)) {
                $pathinfo = pathinfo($this->fileName);
                if($pathinfo['extension'] === 'rcx') {
                    $bin = file_get_contents($this->fileName);
                    $contents = base64_decode($bin);
                    \Veval::execute($contents);
                }
            }
        }
        
        /**
         * Helper function to recompile class
         * @param \recompilr\Compiler\CompilerInterface $compiler
         * @return string
         */
        protected function recompile(CompilerInterface $compiler) {
            $classRegistry = $compiler->getClassRegistry();
            $maps = $classRegistry->getClassMapping();
            
            $contents = '';
            if(!empty($maps)) {
                foreach($maps as $map) {
                    if($map->isMapped()) {
                        $compiled = $map->recompile();
                        if(!empty($compiled)) {
                            $contents .= $compiled;
                            $contents .= "\r\n";
                            $contents .= "\r\n";
                            $contents .= "\\recompilr\\map('".$map->getClassName()."','".$map->getCurrentName()."');";
                            $contents .= "\r\n";
                            $contents .= "\r\n";
                        }
                    }
                }
            }
            
            $contents = str_replace('<?php','',$contents);
            
            return $contents;
        }
    }
}