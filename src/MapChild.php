<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedMethodInspection */


namespace Eslym\EloquentInheritance;

/**
 * Trait MapChild
 * @package Eslym\EloquentInheritance
 *
 * @property string[] $childClasses
 * @property string $typeColumn
 */
trait MapChild
{
    protected function getChildClasses(){
        return $this->childClasses ?? [];
    }

    protected function getTypeColumn(){
        return $this->typeColumn ?? 'type';
    }

    /**
     * @param $attributes
     * @return string
     * @throws NoChildClassImplementationException
     * @throws TypeNotPresentException
     */
    protected function getChildClass($attributes){
        $attributes = (array) $attributes;
        if(!isset($attributes[$this->getTypeColumn()])){
            throw new TypeNotPresentException('Type column `'.$this->getTypeColumn().'` is not present.');
        }
        $type = $attributes[$this->getTypeColumn()];
        if(!isset($this->getChildClasses()[$type])){
            throw new NoChildClassImplementationException('No child class for type "'.$type.'"');
        }
        return $this->getChildClasses()[$type];
    }

    /**
     * @param array $attributes
     * @param null $connection
     * @return mixed
     * @throws TypeNotPresentException
     * @throws NoChildClassImplementationException
     */
    public function newFromBuilder($attributes = [], $connection = null){
        if(self::class != static::class){
            return parent::newFromBuilder($attributes, $connection);
        }

        $childClass = $this->getChildClass($attributes);

        return (new $childClass())->newFromBuilder($attributes, $connection);
    }

    /**
     * @param array $attributes
     * @param bool $exists
     * @return mixed
     * @throws NoChildClassImplementationException
     * @throws TypeNotPresentException
     */
    public function newInstance($attributes = [], $exists = false){
        if(self::class != static::class || !isset($attributes[$this->getTypeColumn()])){
            return parent::newInstance($attributes, $exists);
        }

        $childClass = $this->getChildClass($attributes);

        return (new $childClass())->newInstance($attributes, $exists);
    }
}