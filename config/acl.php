<?php


return [

    'roles' => [
        'operation', 'provider', 'super_admin'
    ],

    'super_admin_permission' => [
        'user' => [
            'user-index',
            'user-store',
            'user-show',
            'user-update',
            'user-delete',

        ],

        'admin' => [
            'admin-index',
            'admin-store',
            'admin-show',
            'admin-update',
            'admin-delete',
            'admin-activate',
            'admin-deactivate',
            'admin-change-password',

        ],
        'provider' => [
            'provider-index',
            'provider-store',
            'provider-show',
            'provider-update',
            'provider-delete',

        ],

        'products' => [
            'products-index',
            'products-store',
            'products-show',
            'products-update',
            'products-delete',
        ],

        'shop' => [
            'shop-index',
            'shop-store',
            'shop-show',
            'shop-update',
            'shop-delete',
            'shop-approve',
        ],
        'adds' => [
            'adds-index',
            'adds-store',
            'adds-show',
            'adds-update',
            'adds-delete',
        ],

        'category' => [
            'category-index',
            'category-store',
            'category-show',
            'category-update',
            'category-delete',
        ],

        'collection' => [
            'collection-index',
            'collection-store',
            'collection-show',
            'collection-update',
            'collection-delete',
            'collection-rename',
        ],

        'order' => [
            'orders',
            'order-details',
            'order-shop',
            'finance-orders',
            'finance-statistics'
        ],

    ],
    'operation_permission' => [
        'user' => [
            'user-index',
            'user-store',
            'user-show',
            'user-update',
            'user-delete',

        ],

        'admin' => [
            'admin-index',
            'admin-store',
            'admin-show',
            'admin-update',
            'admin-delete',
            'admin-activate',
            'admin-deactivate',
            'admin-change-password',

        ],
        'provider' => [
            'provider-index',
            'provider-store',
            'provider-show',
            'provider-update',
            'provider-delete',

        ],

        'products' => [
            'products-index',
            'products-store',
            'products-show',
            'products-update',
            'products-delete',
        ],

        'shop' => [
            'shop-index',
            'shop-store',
            'shop-show',
            'shop-update',
            'shop-delete',
            'shop-approve',
        ],
        'adds' => [
            'adds-index',
            'adds-store',
            'adds-show',
            'adds-update',
            'adds-delete',
        ],

        'category' => [
            'category-index',
            'category-store',
            'category-show',
            'category-update',
            'category-delete',
        ],

        'collection' => [
            'collection-index',
            'collection-store',
            'collection-show',
            'collection-update',
            'collection-delete',
            'collection-rename',
        ],

        'order' => [
            'orders',
            'order-details',
            'order-shop',
            'finance-orders',
            'finance-statistics'
        ],

    ],

    'provider_permission' => [

        'category' => [
            'category-index',
            'category-store',
            'category-show',
            'category-update',
            'category-delete',

        ],

        'products' => [
            'products-index',
            'products-store',
            'products-show',
            'products-update',
            'products-delete',
        ],
        'collection' => [
            'collection-index',
            'collection-store',
            'collection-show',
            'collection-update',
            'collection-delete',
        ],
        'customer' => [
            'list',
            'show',
            'search',
        ],


    ],

];
