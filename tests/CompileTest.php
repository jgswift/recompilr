<?php
namespace recompilr\Tests {
    use recompilr;
    
    class CompileTest extends RecompilrTestCase {
        function testFnLoadLast() {
            $binary = __DIR__.DIRECTORY_SEPARATOR.'Mock'.DIRECTORY_SEPARATOR.'bin.rcx';
            
            if(!file_exists($binary)) {
                $this->testFnCreateBinary();
            }
            
            recompilr\load($binary);
            
            $blank = recompilr\make('recompilr\Tests\BlankClass');
            
            $this->assertObjectHasAttribute('name1', $blank);
            
            $other = recompilr\make('recompilr\OtherClass');
            
            $this->assertObjectHasAttribute('foo', $other);
        }
        
        function testRecompileNewFile() {
            $className = 'recompilr\Tests\BlankClass';
            
            $compiler = \recompilr\Recompiler::getCompiler();
            
            $compiler->execute($className,$this->dir.'BlankClass.php');

            $blank = $compiler->instance($className);
          
            $this->assertObjectHasAttribute('name1', $blank);
            
            $compiler->execute($className,$this->dir.'BlankClass2.php');
            
            $blank = $compiler->instance($className);
            
            $this->assertObjectHasAttribute('name2', $blank);
        }
        
        function testFnRecompileNewFile() {
            $className = 'recompilr\Tests\BlankClass';
            
            recompilr\execute($className,$this->dir.'BlankClass.php');

            $blank = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name1', $blank);
            
            recompilr\execute($className,$this->dir.'BlankClass2.php');

            $blank = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name2', $blank);
        }
        
        function testFnRecompileSameFile() {
            $className = 'recompilr\Tests\BlankClass';
            
            recompilr\execute($className,$this->dir.'BlankClass.php');

            $blank1 = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name1', $blank1);
            
            $blank2 = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name1', $blank2);
            
            $this->assertEquals($blank1,$blank2);
        }
        
        function testFnRecompileAll() {
            $className = 'recompilr\Tests\BlankClass';
            
            recompilr\execute($className,$this->dir.'BlankClass.php');

            $blank1 = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name1', $blank1);
            
            recompilr\all();

            $blank2 = recompilr\make($className);
            
            $this->assertObjectHasAttribute('name1', $blank2);
            
            $this->assertEquals($blank1,$blank2);
        }
        
        function testNonExecutedCompile() {
            $className = 'recompilr\Tests\Mock\BlankBase';
            
            $blank1 = recompilr\make($className);
            
            $this->assertInstanceOf($className, $blank1);
        }
        
        function testFnRecompileAfterChange() {
            $className = 'recompilr\Tests\BlankClass';
            
            recompilr\execute($className,$this->dir.'BlankClass.php');
            
            $blank1 = recompilr\make($className);
            $this->assertObjectHasAttribute('name1', $blank1);
            
            $oldContents = file_get_contents($this->dir.'BlankClass.php');
            $newContents = file_get_contents($this->dir.'BlankClass2.php');
            
            file_put_contents($this->dir.'BlankClass.php',$newContents);
            
            recompilr\all();
            
            $blank2 = recompilr\make($className);
            $this->assertObjectHasAttribute('name2', $blank2);
            
            file_put_contents($this->dir.'BlankClass.php',$oldContents);
        }
        
        function testClassAliasing() {
            extract(recompilr\classes());
            
            $blank = new ${recompilr("recompilr\OtherClass")}();
            
            $this->assertTrue(is_object($blank));
        }
        
        function testNonExecutedClassAliasing() {
            extract(recompilr\classes());
            
            $blank = new ${recompilr("recompilr\Tests\Mock\BlankBase")}();
            
            $this->assertTrue(is_object($blank));
        }
        
        function testFnCreateBinary() {
            recompilr\execute('recompilr\Tests\BlankClass',$this->dir.'BlankClass.php');
            recompilr\execute('recompilr\OtherClass',$this->dir.'OtherClass.php');
            $blank1 = recompilr\make('recompilr\Tests\Mock\BlankBase');
            
            $binary = __DIR__.DIRECTORY_SEPARATOR.'Mock'.DIRECTORY_SEPARATOR.'bin.rcx';
            recompilr\binary($binary);
            
            $this->assertTrue(file_exists($binary));
        }
    }
}