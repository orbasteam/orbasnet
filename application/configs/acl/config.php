<?php

return array(

    # 非會員
    'guest' => array(
        'privileges' => array(
            'view'
        )
    ),

    # 前台會員
    'member' => array(
        'privileges' => array(
            'edit'
        ),
        'resources' => array(
        	'default'
        ),
        'parent' => 'guest'
    ),

    # 後臺總管理員
    'administrator' => array(
        'privileges' => array(
            'edit', 'view', 'delete'
        ),
        'resources' => array(
            'workbench'
        )
    )
);

?>