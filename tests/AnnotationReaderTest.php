<?php

namespace TheCodingMachine\GraphQL\Controllers;

use Doctrine\Common\Annotations\AnnotationException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use ReflectionClass;
use TheCodingMachine\GraphQL\Controllers\Annotations\Type;
use TheCodingMachine\GraphQL\Controllers\Fixtures\Annotations\ClassWithInvalidClassAnnotation;
use TheCodingMachine\GraphQL\Controllers\Fixtures\Annotations\ClassWithInvalidTypeAnnotation;

class AnnotationReaderTest extends TestCase
{
    public function testBadConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new AnnotationReader(new DoctrineAnnotationReader(), 'foo');
    }

    public function testStrictMode()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::STRICT_MODE, []);

        $this->expectException(AnnotationException::class);
        $annotationReader->getTypeAnnotation(new ReflectionClass(ClassWithInvalidClassAnnotation::class));
    }

    public function testLaxModeWithBadAnnotation()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, []);

        $type = $annotationReader->getTypeAnnotation(new ReflectionClass(ClassWithInvalidClassAnnotation::class));
        $this->assertNull($type);
    }

    public function testLaxModeWithSmellyAnnotation()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, []);

        $this->expectException(AnnotationException::class);
        $annotationReader->getTypeAnnotation(new ReflectionClass(ClassWithInvalidTypeAnnotation::class));
    }

    public function testLaxModeWithBadAnnotationAndStrictNamespace()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, ['TheCodingMachine\\GraphQL\\Controllers\\Fixtures']);

        $this->expectException(AnnotationException::class);
        $annotationReader->getTypeAnnotation(new ReflectionClass(ClassWithInvalidClassAnnotation::class));
    }

    public function testGetAnnotationsStrictMode()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::STRICT_MODE, []);

        $this->expectException(AnnotationException::class);
        $annotationReader->getClassAnnotations(new ReflectionClass(ClassWithInvalidClassAnnotation::class), Type::class);
    }

    public function testGetAnnotationsLaxModeWithBadAnnotation()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, []);

        $types = $annotationReader->getClassAnnotations(new ReflectionClass(ClassWithInvalidClassAnnotation::class), Type::class);
        $this->assertSame([], $types);
    }

    public function testGetAnnotationsLaxModeWithSmellyAnnotation()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, []);

        $this->expectException(AnnotationException::class);
        $annotationReader->getClassAnnotations(new ReflectionClass(ClassWithInvalidTypeAnnotation::class), Type::class);
    }

    public function testGetAnnotationsLaxModeWithBadAnnotationAndStrictNamespace()
    {
        $annotationReader = new AnnotationReader(new DoctrineAnnotationReader(), AnnotationReader::LAX_MODE, ['TheCodingMachine\\GraphQL\\Controllers\\Fixtures']);

        $this->expectException(AnnotationException::class);
        $annotationReader->getClassAnnotations(new ReflectionClass(ClassWithInvalidClassAnnotation::class), Type::class);
    }


}