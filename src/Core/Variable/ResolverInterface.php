<?php


namespace LastCall\Mannequin\Core\Variable;


interface ResolverInterface
{

    public function resolves(string $type): bool;

    public function resolve(string $type, $value);

}