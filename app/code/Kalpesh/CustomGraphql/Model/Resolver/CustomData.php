<?php

namespace Kalpesh\CustomGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CustomData implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $id = $args['id'];

        return [
            'id' => $id,
            'name' => 'Kalpesh',
            'message' => 'Custom GraphQL working in Magento 2'
        ];
    }
}