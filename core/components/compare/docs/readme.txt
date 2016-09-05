
Snippet Compare for MODX Revolution

Compare Products in the parameters. Product ID comparison are stored in cookies. It can also be used for the "Favorites".

Andchir
http://modx-shopkeeper.ru/

---------------------------

Студия "Без рекламы" - http://no-ad.ru/

############################################

Сравнение товаров по параметрам. ID товаров для сравнения сохраняются а куках. Можно использовать также для "Избранного".

############################################

Параметры сниппета "compare":

action - Действие.
   Возможные значения:
   to_compare - вывод строки со ссылкой на страницу сравнения;
   print_id_list - список ID товаров, выбранных для сравнения (через запятую);
   print_products - вывод таблицы параметров товаров (сравнение);
   print_parent_id - вывод ID категории, товары которой выбраны.
   По умолчанию - to_compare.

toCompare_tpl - Шаблон вывода строки со ссылкой на страницу сравнения и числом выбранных товаров.

product_tpl - Шаблон вывода таблицы параметров товаров (сравнение). Пример: /chunks/compare_product.tpl

jsScript - Добавлять JS скрипт в <head>. По умолчанию - 1.

minProducts - Минимальное число товаров. По умолчанию - 2.

limitProducts - Максимальное число товаров для сравнения. По умолчанию - 0 (без ограничения).

targetActiveClass = CSS класс, который нужно добавить элементу (чекбокс, кнопка и т.п.) после добавления товара к сравнению.

comparePageId - ID страницы сравнения.

onlyThisParentId - ID категории (документа), в которой можно добавить товары к сравнению. Запрет сравнения товаров из разных категорий. По умолчанию - 0 (любая категория).

filterTVID - TV ID через запятую, которые не нужно выводить в таблице параметров. Можно указать для каждого раздела отдельно (первый - по умолчанию).
    ID категории 1~TV ID 1, TV ID 2...||ID категории 2~TV ID 1, TV ID 2...||...
    Пример: &filterTVID=`5~1,2,5||6~4,2||7~5,1`
    или без категорий: &filterTVID=`1,2,5`

removeLastTwo - Если сравниваются только два товара, при удалении одного удалять оба (т.к. сравнивать будет нечего). 1 - включить (по умолчанию), 0 - выключить.

noResults - Текст, который будет выводиться, если не выбрано ни одного товара для сравнения.

--------------------------------

Пример toCompare_tpl:

<p>
    Выбрано <span id="skolko_vibrano">[[+count_current]]</span> из [[+count_max]]
    / <a href="[[+href_compare]]" onclick="return shkCompare.toCompareLink();">сравнить</a>
    <span id="sravnenie_otmena" style="display:[[+display_cancel]];"> / <a href="[[+href_cancel]]">отменить</a></span>
</p>
<br clear="all" />

--------------------------------

Шаблон таблицы параметров товаров для сравнения:

См. пример /chunks/compare_product.tpl

Шаблон разделен на 6 частей (разделитель <!--tpl_separator-->).

1 - верхняя часть;

2 - строка одного параметра.
    [[+inner]] - ячейки таблицы с данными,
    [[+classes]] - CSS-классы для строки таблицы: even - четная строка, odd - нечетная строка, last - последняя строка.

3 - первая ячейка верхнего ряда.

4 - ячейки верхнего ряда таблицы. Например можно вывести названия товаров и фото.
    Плейсхолдеры - любые поля и имена TV;
    [[+iteration]] - порядковый номер товара в списке (колонки),
    [[+classes]] - CSS-классы для ячейки таблицы: even - четная колонка, odd - нечетная колонка, last - последняя колока.

5 - ячейка с названием параметра (название берется из поля "Заголовок" в свойствах TV).
    [[+param_name]] - наименование (заголовок) параметра,
    [[+row_number]] - номер строки таблицы.

6 - ячейка со значением параметра.
    [[+param_name]] - наименование параметра,
    [[+tv_name]] - имя TV,
    [[+param_value]] - значение параметра,
    [[+iteration]] - порядковый номер товара в списке,
    [[+row_number]] - номер строки таблицы,
    [[+classes]] - CSS-классы для ячейки таблицы: even - четная колонка, odd - нечетная колонка, first - первая колонка, last - последняя колока.

7 - нижняя часть.

--------------------------------

Примеры вызова

На странице каталога - Добавление к сравнению:

[[!compare?
&comparePageId=`15`
&limitProducts=`4`
&onlyThisParentId=`[[*id]]`
]]

На странице каталога - Добавление в избранное:

[[!compare?
&comparePageId=`26`
&minProducts=`1`
&targetActiveClass=`active`
&toCompare_tpl=`toFavorites`
]]

Страница сравнения:

[[!compare?
&action=`print_products`
&product_tpl=`@FILE compare_product.tpl`
&filterTVID=`4,16`
]]

Избранное:

[[!getProducts?
&resources=`[[!compare?action=`print_id_list`]]`
&tpl=`product_fav`
&noResults=`<p>Вы ничего не выбрали.</p>`
]]

---------------------------------

Пример чекбокса, который добавляет товары к сравнению:

<input type="checkbox" name="compare" id="compare[[+id]]" value="1" onclick="return shkCompare.toCompare([[+id]],[[+parent]],this)" [[+id:in_compare=`checked="checked"`]] />
<label for="compare[[+id]]">Сравнить</label>

---------------------------------

Для отметки товаров, которые выбраны к сравнению можно использовать модификатор "compare".
Примеры использования:

1.
<div class="product [[+id:in_compare=`active`]]">...

2.
<input type="checkbox" name="compare" id="compare[[+id]]" value="1" onclick="return shkCompare.toCompare([[+id]],[[+parent]],this)" [[+id:in_compare=`checked="checked"`]] />
<label for="compare[[+id]]">Сравнить</label>

---------------------------------

Для удаления из избранного в чанке вывода товара использовать ссылку такого вида:

<a href="[[~[[*id]]]]?cmpr_action=del_product&pid=[[+id]]">Убрать из избранного</a>

---------------------------------

JavaScript-функции, которые вызываются при определенных событиях:

cmpOnToCompareLinkMinimum - если кликнули по ссылке "Сравнить", но выбран только один товар или ни одного (можно вывести сообщение);
cmpOnToCompareCheckClicked(id,parent,elem) - клик по чекбоксу "сравнить";
cmpOnToCompareLimitReached(limit) - выбрано максимальное число товаров для сравнение (можно вывести сообщение);
cmpOnToCompareFromAnotherCategory - если добавляется товар из другой категории (можно вывести сообщение).
cmpOnToCompareAdded(id,parent,elem) - Товар добавлен к сравнению.
cmpOnToCompareRemoved(id,parent,elem) - Товар убран из списка к сравнению.

Просто создайте функцию с таким именем и она будет вызвана при соответствующем событии.
Пример:

<script type="text/javascript">
function cmpOnToCompareLinkMinimum(){
    alert("Для сравнения необходимо выбрать минимум 2 товара.\n Пожалуйста, выберите товар для сравнения.");
}
</script>

