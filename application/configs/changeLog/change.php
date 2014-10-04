<?php

return array(

    array(
	   'date' => '2014-05-28',
       'version' => '3.1.3',
       'change' => array(
            array(
       	        'type' => 'new',
                'desc' => '在線人員顯示'
            )
       )
    ),

    array(
        'date' => '2014-05-21',
        'version' => '3.1.2',
        'change' => array(
            array(
        	   'type' => 'new',
               'desc' => 'Chrome 桌面通知功能 (<a href="/profile/index#notification">Profile</a> 中開啟通知)'
            )
        )
    ),
	
    array(
	   'date' => '2014-02-11',
       'version' => '3.1.1',
       'change' => array(
            array(
       	        'type' => 'new',
                'desc' => 'Youtube影片嵌入功能(直接將youtube連結貼上)'
            )
        )
    ),

    array(
        'date' => '2014-02-09',
        'version' => '3.1.0',
        'desc' => '<p>功能大致上都已完善，正式將版本訂為3.1.0</p>',
        'change' => array(
            array(
                'type' => 'new',
                'desc' => '縮址功能'
            )
        )
    ),

    array(
        'date' => '2014-02-07',
        'version' => '3.0.11',
        'change' => array(
            array(
                'type' => 'fix',
                'desc' => '修正圖片上傳Bug'
            ),
        
            array(
                'type' => 'fix',
                'desc' => '修正刪除圖片Bug'
            )
        )
    ),

    array(
        'date' => '2014-01-31',
        'version' => '3.0.10',
        'change' => array(
            array(
                'type' => 'new',
                'desc' => '單一留言單一頁面 (訊息通知點選進入)'
            ),
        
            array(
                'type' => 'fix',
                'desc' => '修正上傳圖片通知訊息'
            )
        )
    ),

    array(
        'date' => '2014-01-30',
        'version' => '3.0.9',
        'change' => array(
            array(
                'type' => 'new',
                'desc' => '開放上傳圖片'
            )
        )
    ),

    array(
        'date' => '2014-01-23',
        'version' => '3.0.8',
        'change' => array(
            array(
                'type' => 'update',
                'desc' => '改為傳統式的推播訊息，等主機到期後再更新新的推播方式'
            ),
        
            array(
                'type' => 'update',
                'desc' => '修正讀取誰點讚(爛)的讀取方式，減少loading'
            )
        )
    ),

    array(
        'date' => '2014-01-22',
        'version' => '3.0.7',
        'change' => array(
            array(
                'type' => 'update',
                'desc' => '查看誰點讚以及點爛'
            )
        )
    ),

    array(
        'date' => '2014-01-21',
        'version' => '3.0.6',
        'change' => array(
            array(
                'type' => 'new',
                'desc' => '列表文字的"更多"按鈕'
            ),
            
            array(
	           'type' => 'fix',
               'desc' => '修正Textarea bug'
            )
        )
    ),

    array(
	   'date' => '2014-01-20',
       'version' => '3.0.5',
       'change' => array(
            array(
       	        'type' => 'update',
                'desc' => '虛擬主機設定有問題，一直無法抓取及時推播訊息，暫時拿掉功能'
            ),
       
            array(
                'type' => 'update',
                'desc' => '改善Remember Me記性問題'
            )
        )
    ),

    array(
        'date' => '2014-01-16',
        'version' => '3.0.4',
        'change' => array(
            array(
        	   'type' => 'New',
               'desc' => '更新記錄'
            )
        )
    ),

    array(
	   'date'    => '2014-01-15',
       'version' => '3.0.3',
       'change'  => array(
            array(
       	        'type' => 'new',
                'desc' => '訊息刪除，刪除後一段時間內可反悔復原'
            )
        )
    ),

    array(
	   'date' => '2014-01-11',
       'version' => '3.0.2',
       'change' => array(
            array(
                'type' => 'new',
                'desc' => '訊息通知beta版，推播訊息尚有問題，先測試單一訊息通知功能'
            )
        )
    ),

    array(
        'date' => '2014-01-10',
        'version' => '3.0.1',
        'change'  => array(
            array(
        	   'type' => 'new',
               'desc' => '登入的Remember me'
            ),
        
            array(
	           'type' => 'new',
               'desc' => '點讚及爛'
            ),
        
            array(
	           'type' => 'fix',
               'desc' => '新舊文章排版'
            )
        )
    ),

    array(
	   'date' => '2014-01-09',
       'version' => '3.0',
       'desc' => '
            <p>全面改為Zend Framework 加大神本人自行開發的Orbas Framework，提供更快速的開發環境
                                版本號碼訂為3.0，現在環境乾淨的跟Nexus 5 一樣甚麼都沒有 (敏捷式開發方式，準備、開槍、瞄準、瞄準、瞄準)
                                僅提供基本的留言、回應、自動超連結、以及無限滾動 (不須點選more即可看更多訊息)
                                最大突破是開始支援手機版，未來人在外面也可以透過手機與Orbas好友分享訊息</p>
            <p>接下來目標：</p>
            <ol>
                <li>更及時的推播訊息<br />打算試試HTML5 websocket，看好不好用</li>
                <li>將原有的功能一個一個拉回來 (包括毛根之印)</li>
                <li>Code Prettify<br />將打好的code上色</li>
            </ol>
            <p>... 等八百項更新目標</p>'
    )

);