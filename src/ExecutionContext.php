<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DesignPatterns\KeyValueContext\KeyValueContext;
use IfCastle\Exceptions\SerializeException;
use IfCastle\Exceptions\UnSerializeException;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableInterface;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableValidatorInterface;

class ExecutionContext extends KeyValueContext implements ExecutionContextInterface
{
    /**
     * @throws SerializeException
     */
    #[\Override]
    public function toArray(?ArraySerializableValidatorInterface $validator = null): array
    {
        if($validator?->isSerializationAllowed($this) === false) {
            throw new SerializeException('Serialization is not allowed', $this, 'array', $this);
        }
        
        $result = [];
        
        foreach ($this->context as $key => $value) {
            if ($value instanceof ArraySerializableInterface) {
                $result[$key] = $value->toArray($validator);
            } else if(is_scalar($value) || is_null($value)) {
                $result[$key] = $value;
            } else {
                throw new SerializeException('The value of the context should be scalar', $value, 'array', $this);
            }
        }
        
        return $result;
    }
    
    /**
     * @throws UnSerializeException
     */
    #[\Override]
    public static function fromArray(array $array, ?ArraySerializableValidatorInterface $validator = null): static
    {
        $context                    = [];
        $stack                      = [];
        $iterator                   = new \ArrayIterator($array);
        $iterator->rewind();
        
        while (true) {
            
            while ($iterator->valid()) {
                $value              = $iterator->current();
                $key                = $iterator->key();
                
                if(is_array($value)) {
                    
                    $iterator->next();
                    $context[$key]  = [];
                    $stack[]        = [$iterator, &$context];
                    
                    $context        = &$context[$key];
                    $iterator       = new \ArrayIterator($value);
                    $iterator->rewind();
                    
                    break;
                } else if(is_scalar($value) || is_null($value)) {
                    $context[$key]  = $value;
                    $iterator->next();
                } else {
                    throw new UnSerializeException('The value of the context should be scalar', 'array', $value);
                }
            }

            if($stack === []) {
                break;
            }
            
            [$iterator, &$context]   = array_pop($stack);
        }
        
        return new static($context);
    }
}