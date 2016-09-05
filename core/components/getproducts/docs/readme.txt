--------------------
getProducts
--------------------
Author: Andchir <andchir@gmail.com>
--------------------

Студия "Без рекламы" - http://www.no-ad.ru/

--------------------

English
Snippet for a print list of resources. Designed specifically for large catalogs (eg catalog in the online store).
Do not use xPDO, optimized filtering on TV for maximum speed.

Русский
Сниппет для вывода списка ресурсов. Предназначен специально для больших каталогов (например каталог товаров в интернет-магазине).
Не использует xPDO, оптимизирована фильтрация по TV для максимальной скорости.

--------------------

Русский

Параметры сниппета:

parents - ID родительских ресурсов через запятую. По умолчанию текущий.
resources - Список ID ресурсов (товаров) через запятую. Внимание! Если нужен вывод только указанных ID, отключить вывод по родителю так: &parents=`-1`
depth - Глубина поиска родителей. По умолчанию 1.
tpl - Имя чанка шаблона для вывода ресурса.
tpl_nN - Имя чанка для каждого N (порядковый номер) элемента. Пример чанка для каждого 4-го элемента: &tpl_n4=`tpl4th`.
outputSeparator - Резделитель. По умолчанию - \n (новая строка).
outputSeparator_nN - Имя чанка для разделителя, который нужно вставить после каждого N элемента.
    Пример после каждого 2-го элемента: &outputSeparator_n2=`separator2th`.
    Пример2: &outputSeparator_n2=`@INLINE <br clear="all"><hr>`
outerTpl - Имя чанка шаблона обертки вывода. Доступен только плейсхолдер [[+inner]].
className - Имя класса (объекта) элементов таблицы БД. По умолчанию "modResource".
packageName - Имя пакета элементов таблицы БД. Например: shop - будет запрошен класс по адресу "/core/components/shop/model/shop/shopcontent.class.php". Рекоммендуется использовать пакет MIGXDB (http://modx.com/extras/package/migx).
migx_configName - Название конфигурации MIGX, есил используются таблицы созданные в MIGXDB. Нужно для того чтобы знать каким TV соответствуют поля для processTVs и др. По умолчанию название соответствует packageName.
where - JSON строка для условия WHERE в SQL запросе. Пример: &where=`{"template":15}` (только поля ресурсов, без TV).
sortby - Поле для сортировки (только поля ресурсов, без TV). По умолчанию "menuindex". Для сортировки вразнобой использовать &sortby=`RAND()`.
sortdir - Направление сортировки. По умолчанию "ASC".
sortbyTV - Сортировка по TV. Указать имя TV.
sortdirTV - Направление сортировки по TV (ASC|DESC). По умолчанию "ASC";
sortbyTVType - Тип значения TV (string|integer). По умолчанию "string";
orderby - JSON строка сортировки. Пример: &orderby=`{"parent":"ASC","pagetitle":"ASC"}`
orderbyResources - Сортировать по порядку, указанному в списке &resources.
tvFilters - JSON строка фильтрации по ТВ. Пример: &tvFilters=`{"country":"Китай","producer":"Sony"}` Пока поддерживается только проверка на точное соответствие "=".
includeTVs - Добавить плейсхолдеры значений TV для ресурсов (1|0). Префикс для TV: "tv.". Пример: [[+tv.image]]. По умолчанию = 0 (отключен).
includeTVList - Список имён TV, которые нужно добавить через запятую.
processTVs - Применять параметр "Параметры ввода" для TV.
processTVList - Список TV через запятую, для которых применять processTVs.
fromParentList - список полей через запятую, которые нужно добавить товарам от родителей. Например "pagetitle,image" - в чанке сниппета будут доступны плейсхолдеры [[+parent.pagetitle]] и [[+parent.image]] (TV). По умолчанию выключено.
addSubItemCount - В чанке сниппета будет доступен плейсхолдер [[+subitemcount]] - число дочерних ресурсов.
subItemCountWhere - JSON строка для условия WHERE в SQL запросе для подсчета дочерних элементов.
noResults - Текст, который будет выводиться, если по запросу ничего не найдено.
toPlaceholder - Имя плейсхолдера, в который нужно отправить результат работы сниппета. По умолчанию не используется.
totalVar - Имя плейсхолдера с общим количеством ресурсов. По умолчанию "total".
context - Контекст, из которого нужно вывести ресурсы. По умолчанию текущий.
activeParentSnippet - Сниппет для активного контейнера-ресурса - [[+activeParent_snippet]]. См. пример с меню ниже.
activeClass - Имя CSS-класса для активного ресурса. По умолчанию "active".
includeContent - Включать в выборку из БД значение поля "content" (1|0). По умолчанию выключено.
returnIDs - Возвращать только ID рессурсов (1|0). По умолчанию 0 (выключено).
useSmarty - Использовать в чанке шаблонизатор Smarty (1|0). По умолчанию 0 (выключено). Подробнее ниже.
debug - Режим отладки (1|0). В журнал ошибок будут писаться SQL запросы, полученные в сниппете. По умолчанию = 0 (отключен).

Параметры кэширования:
gp_cache - Включить кэширование (1|0). По умолчанию выключено - 0.
cacheId - Идентификатор кэша. По умолчанию "gpCache".

Необязательные параметры кэширования:
cache_key - Ключ кэша (название папки для файлов кэша). По умолчанию берется из настроек системы - cache_resource_key.
cache_handler - Обработчик кэширования. По умолчанию берется из настроек системы - cache_resource_handler (xPDOFileCache).
cache_expires - число секунд для кэширования. По умолчанию 0 (бесконечное).

================================

Пример фильтрации по цене - больше и меньше:

&tvFilters=`{"price:>=,<=:AND":[200,500]}`

Пример с поиском подстроки:

&tvFilters=`{"param:LIKE":"%черный%"}`

Поиск по множественным значениям:

&tvFilters=`{"param:LIKE:OR":["%черный%","%синий%","%зеленый%"]}`

--------------------

Пример для поиска по стандартным полям:

&where=`{"temlate:=:AND":"2","pagetitle:LIKE:AND":"%черный%"}`

--------------------

Плейсхолдеры в чанке "tpl":

idx - Индекс строки от нуля.
first - (1|0) - Первая строка. Если первая строка, то выведется "1", если нет - "0".
last - (1|0) - Последняя строка.
odd - (1|0) - Четная строка.
activeClass - Класс активного ресурса.
classnames - Все CSS-классы одной строкой.
active - (1|0) - активный ресурс.
activeParent - ID активного родителя.
activeParent_snippet - Вывод сниппета из параметра &activeParentSnippet.
tv.любойTV - TV параметры.
parent.полеОтРодителя - поля от роделя (в т.ч. TV).

--------------------

Пример вызова:

[[!getProducts?
&parents=`5`
&includeTVs=`1`
&includeTVList=`price,image,producer,country`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
&tvFilters=`{"country":"Китай","producer":"Sony"}`
]]

Пример использования с getPage:

[[!getPage?
&cache=`1`
&elementClass=`modSnippet`
&element=`getProducts`
&parents=`5`
&includeTVs=`1`
&includeTVList=`price,image,producer,country`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
&tvFilters=`{"country":"Китай","producer":"Sony"}`
&pageFirstTpl=` <li class="control"><a [[+classes]] href="[[+href]]">Первая</a></li> `
&pageLastTpl=` <li class="control"><a [[+classes]] href="[[+href]]">Последняя</a></li> `
]]
<br class="clear" />
<ul class="pages">
[[!+page.nav]]
</ul>

Пример вывода элементов из таблицы "modx_shop_content"
(см. http://modx-shopkeeper.ru/documentation/modx-revolution/tovaryi-iz-otdelnoj-tabliczyi.html):

[[!getPage?
&cache=`1`
&elementClass=`modSnippet`
&element=`getProducts`
&className=`shopContent`
&packageName=`shop`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
&pageFirstTpl=` <li class="control"><a [[+classes]] href="[[+href]]">Первая</a></li> `
&pageLastTpl=` <li class="control"><a [[+classes]] href="[[+href]]">Последняя</a></li> `
]]
<br class="clear" />
<ul class="pages">
[[!+page.nav]]
</ul>

Пример вывода с кэшированием, кэшируется для всех страниц:

[[getProducts@top_products?
&gp_cache=`1`
&cacheId=`top_products`
]]

Пример вывода с кэшированием, кэшируется для всех страниц + учитывается валюта:

[[!getProducts@top_products?
&gp_cache=`1`
&cacheId=`top_products_[[!+shk_currency]]`
]]

--------------------

Пример вывода многоуровневого меню (замена Wayfinder). Подуровни выводятся только для текущей категории:

[[getProducts?
&parents=`4`
&where=`{"hidemenu":0,"template:<>":5}`
&tpl=`menuRowTpl`
&addSubItemCount=`1`
&activeParentSnippet=`getProducts?parents=[[+id]]&tpl=menuRowTpl2`
]]

menuRowTpl:

<li>
    <a href="[[~[[+id]]]]" class="[[+activeClass]]">[[+pagetitle]] ([[+subitemcount]])</a>
    [[+active:is=`1`:then=`
    <ul>
        [[+activeParent_snippet]]
    </ul>
    `:else=``]]
</li>

menuRowTpl2:

<li>
    <a href="[[~[[+id]]]]" class="[[+activeClass]]">[[+pagetitle]]</a>
</li>

--------------------

Smarty

Если включить параметр "useSmarty", в чанке сниппета можно использовать шаблонизатор Smarty.
Рекоммендуется установить пакет modxSmarty (http://modx.com/extras/package/modxsmarty) от fi1osof.
В чанке вместо, например, [[+pagetitle]] нужно писать {$item.pagetitle}.
Для TV-параметров синтаксис такой: {$item['tv.price']}

Логические операторы в Smarty: http://www.smarty.net/docs/en/language.function.if.tpl

Пример чанка с использованием Smarty (и пакета modxSmarty):

<div class="product shk-item">
    <div class="product-b">
        <div class="product-descr">
            <a href="{link id="{$item.id}"}">
                {if $item['tv.image'] ne ""}
                    <img class="shk-image" src="{$item['tv.image']}" alt="" height="130" width="130" />
                {else}
                    <img class="shk-image" src="/assets/images/nophoto.jpg" alt="" height="130" width="130" />
                {/if}
            </a>
            <h3>{$item.pagetitle}</h3>
            {$item.introtext}<br />
            <a href="{link id="{$item.id}"}">Подробнее &rsaquo;</a>
            <div style="clear:both;"></div>
            <small>
                {if $item['tv.inventory'] gt 0}
                    <b style="color:green;">есть в наличии</b>
                {else}
                    <b style="color:red;">нет в наличии</b>
                {/if}
            </small>
        </div>
        <form action="{link id="{field name="id"}"}" method="post">
            <fieldset>
                <input type="hidden" name="shk-id" value="{$item.id}" />
                <input type="hidden" name="shk-name" value="{$item.pagetitle}" />
                <input type="hidden" name="shk-count" value="1" />
                <div class="product-price">
                    <button type="submit" class="shk-but">В корзину</button>
                    <div>Цена: <span class="shk-price">{$item['tv.price']}</span> руб.</div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

====================

English

Snippet properties:

parents - ID parent resources, separated by commas. By default, the current one.
resources - ID list of resources (products), separated by commas.
depth - Search Depth parents. The default is 1.
tpl - Name chunk template to display the resource.
className - Class name of content in DB table. Default "modResource".
packageName - Package name on content. Example: shop - open class from path "/core/components/shop/model/shop/shopcontent.class.php". Recommended to use MIGXDB (http://modx.com/extras/package/migx).
where - JSON string to the WHERE clause in SQL query. Example: &where=`{"template":15}` (only the fields of resources, without TV).
sortby - Field name for sorting (only the fields of resources, without TV). By default "pagetitle".
sortdir - Sort direction. Default "ASC".
orderby - JSON string for sorting. Example: &orderby=`{"parent":"ASC","pagetitle":"ASC"}`
tvFilters - JSON string filtering on TV. Example: &tvFilters=`{"country":"China","producer":"Sony"}` While the test is only supported on an exact match "=".
includeTVs - Add placeholders values ​​for TV (1|0). Prefix for TV: "tv.". Example: [[+tv.image]]. Default = 0 (disabled).
includeTVList - Name List TV, you need to add a comma.
processTVs - Indicates if TemplateVar values should be rendered as they would on the resource being summarized. TemplateVars must be included (see includeTVs/includeTVList) to be processed.
processTVList - An optional comma-delimited list of TemplateVar names to process explicitly. TemplateVars specified here must be included via includeTVs/includeTVList.
sortbyTV - Sorting by TV. Specify the name of the TV.
sortdirTV - Sort direction for TV (ASC | DESC). Default "ASC";
sortbyTVType - Value type TV (string | integer). Default "string";
fromParentList - list of fields separated by commas, that you want to add a product from the parents. Example "pagetitle,image" - in the chunk of snippet will be available placeholders [[+parent.pagetitle]] and [[+parent.image]] (TV). Default is off.
noResults - The text to be displayed, if the query returns no results.
toPlaceholder - Placeholder name in which you want to send the output of the snippet. Not used by default.
totalVar - Placeholder name with number total resources. By default, "total".
context - The context from which to derive resources. By default - the current one.
addSubItemCount - In chunk snippet will be available placeholder [[+subitemcount]] - the number of child resources.
activeClass - CSS-class name for the active resource.
includeContent - Include from the database field "content" value (1|0). Default is off.
debug - Debugging mode (1|0). In the error log (in manager) will be written SQL queries received in the snippet. Default = 0 (disabled).

Caching options:
gp_cache - Enable cache (1|0). Default - 0.
cacheId - Cache ID string. Default "gpCache".

===

Example filter by price - more or less:

&tvFilters=`{"price:>,<:AND":[200,500]}`

Example of the search substring:

&tvFilters=`{"param:LIKE":"%black"}`

===

Example:

[[!getProducts?
&parents=`5`
&includeTVs=`1`
&includeTVList=`price,image,producer,country`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
&tvFilters=`{"country":"China","producer":"Sony"}`
]]

Example with getPage:

[[!getPage?
&elementClass=`modSnippet`
&element=`getProducts`
&parents=`5`
&includeTVs=`1`
&includeTVList=`price,image,producer,country`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
&tvFilters=`{"country":"China","producer":"Sony"}`
]]
<br class="clear" />
<ul class="pages">
[[!+page.nav]]
</ul>

