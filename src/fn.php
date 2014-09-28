<?php
namespace recompilr;

/**
 * Map and compile class from file
 * @param string $class
 * @param string $file
 */
function execute($class, $file = null) {
    Recompiler::getCompiler()->execute($class, $file);
}

/**
 * Load *.rcx binary
 * @param string $fileName
 * @return \recompilr\Binary\AggregateBinary
 */
function load($fileName) {
    $binary = new Binary\AggregateBinary($fileName);
    $binary->load(Recompiler::getCompiler());
    
    return $binary;
}

/**
 * Instantiate object from mapped class
 * @param string $class
 * @param array $args
 * @return mixed
 */
function make($class,array $args = []) {
    return Recompiler::getCompiler()->instance($class,$args);
}

/**
 * Recompile all mapped classes
 */
function all() {
    Recompiler::getCompiler()->all();
} 

/**
 * Create binary from compiler
 * @param type $fileName
 */
function binary($fileName, Compiler\CompilerInterface $compiler = null) {
    if(is_null($compiler)) {
        $compiler = Recompiler::getCompiler();
    }
    $binary = new Binary\AggregateBinary($fileName);
    $binary->save($compiler);
    
    return $binary;
}

/**
 * Maps class to alias
 * @param string $class
 * @param string $id
 */
function map($class,$id) {
    Recompiler::getCompiler()->map($class,$id);
}

/**
 * Register compiler
 * @param \recompilr\Compiler\CompilerInterface $compiler
 */
function register(Compiler\CompilerInterface $compiler) {
    Recompiler::getCompiler()->register($compiler);
}

/**
 * Unregister compiler
 * @param \recompilr\Compiler\CompilerInterface $compiler
 */
function unregister(Compiler\CompilerInterface $compiler) {
    Recompiler::getCompiler()->unregister($compiler);
}

/**
 * Retrieve class subalias name
 * @param string $class
 * @return string
 */
function alias($class) {
    $cls = Recompiler::getCompiler()->name($class);
    return str_replace('\\','__',$cls);
}

/**
 * Retrieve list of all declared classes and mapped classes
 * @staticvar array $classes
 * @return array
 */
function classes() {
    static $classes;
    if(empty($classes)) {
        $classes = get_declared_classes();
        $newclasses = [];
        array_walk($classes, function($cls)use(&$newclasses) {
            $newclasses[str_replace('\\','__',$cls)] = $cls;
        });
        
        $classes = $newclasses;
    }
    
    
    return array_merge($classes,Recompiler::getCompiler()->names());
}