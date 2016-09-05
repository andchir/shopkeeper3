
INSERT INTO `modx_shopkeeper3_config` (`id`, `setting`, `value`, `xtype`) VALUES
(1, 'statuses', '[{"label":"Новый","tpl":"userMail","color":"#b2d6ff","id":1},{"label":"Принят к оплате","tpl":"userMail","color":"#c7fff7","id":2},{"label":"Отправлен","tpl":"userMail","color":"#fffdb8","id":3},{"label":"Выполнен","tpl":"userMail","color":"#c9ffc2","id":4},{"label":"Отменен","tpl":"userMail","color":"#ffc9c9","id":5},{"label":"Оплата получена","tpl":"userMail","color":"#ffdbad","id":6}]', 'array'),
(2, 'order_fields', '[{"name":"status","label":"Статус","rank":0,"id":1},{"name":"id","rank":1,"label":"ID","id":2},{"name":"date","rank":2,"label":"Время","id":3},{"name":"price","rank":3,"label":"Цена","id":4},{"name":"count_total","rank":4,"label":"Кол-во","id":5},{"name":"email","label":"Эл. адрес","rank":5,"id":6},{"name":"username","rank":6,"label":"Пользователь","id":7}]', 'array'),
(3, 'currency_rate', '[{"label":"руб.","value":"1","id":1},{"label":"грн","value":"4","id":2},{"label":"USD","value":"34","id":3}]', 'array'),
(4, 'delivery', '[{"label":"Самовывоз","price":"0","id":1},{"label":"Доставка по городу","price":"500","free_start":"2000","id":2}]', 'array'),
(5, 'payments', '[{"label":"При получении","price":"При получении","id":1},{"label":"Электронные деньги","price":"Электронные деньги","id":2}]', 'array'),
(7, 'contacts_fields', '[{"name":"field1_1","label":"Имя","id":1,"rank":0},{"rank":1,"name":"field1_2","label":"Адрес эл. почты","id":2},{"rank":2,"name":"field1_3","label":"Телефон","id":3},{"rank":3,"name":"field1_4","label":"Комментарий","id":4}]', 'array');

