<?php


return [
   
    'roles' => [
       'admin','provider'
    ],
    'admin_permission' => [
        'user-management' => [
            'user-management-index',
            'user-management-store',
            'user-management-show',
            'user-management-update',
            'user-management-delete',
        ],
        'categories' => [
            'categories-index',
            'categories-store',
            'categories-show',
            'categories-update',
            'categories-delete',
            'categories-deleteMany',

        ],

        'books' => [
            'books-index',
            'books-store',
            'books-show',
            'books-update',
            'books-delete',
        ],

    ],

    'provider_permission' => [

       
        'master-skill' => [
            'master-skill-index',
            'master-skill-store',
            'master-skill-show',
            'master-skill-update',
            'master-skill-delete',
            'master-skill-deleteMany',

        ],
        'courses' => [
            'courses-index',
            'courses-store',
            'courses-show',
            'courses-update',
            'courses-delete',
        ],
        'instructors' => [
            'instructors-index',
            'instructors-store',
            'instructors-show',
            'instructors-update',
            'instructors-delete',
        ],
        'learningPath' => [
            'learningpath-index',
            'learningpath-store',
            'learningpath-show',
            'learningpath-update',
            'learningpath-delete',
        ],
        'Levels' => [
            'Levels-index',
            'Levels-store',
            'Levels-show',
            'Levels-update',
            'Levels-delete',
        ],
    ],
     
 ];   

?>
